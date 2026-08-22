<?php

declare(strict_types=1);

namespace Tests\Feature\ImageProcessing;

use App\Enums\ImageItemStatus;
use App\ImageProcessing\Exceptions\ImageAnalysisException;
use App\ImageProcessing\ImageJobService;
use App\ImageProcessing\ImageMetadataExtractor;
use App\ImageProcessing\Jobs\AnalyzeImageMetadataJob;
use App\Models\ImageProcessingItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use Tests\TestCase;

/**
 * Image Everything (Phase S2) — exercises the REAL pipeline end to
 * end: App\ImageProcessing\ImageJobService::uploadImage() ->
 * App\ImageProcessing\Jobs\AnalyzeImageMetadataJob (QUEUE_CONNECTION
 * is 'sync' in phpunit.xml, so this runs inline, no queue:work
 * needed) -> App\ImageProcessing\ImageMetadataExtractor -> the
 * item's own $metadata/$quality_analysis/$quality_score columns.
 *
 * Uses Storage::fake('private-images') (a real local-disk fake, not a
 * mock) plus a REAL Imagick-generated JPEG fixture, so this is testing
 * actual Imagick decoding/analysis, not a stubbed substitute.
 */
final class ImageMetadataAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_an_image_runs_analysis_and_populates_metadata_and_quality_columns(): void
    {
        Storage::fake('private-images');

        $service = app(ImageJobService::class);
        $job = $service->createJob(User::factory()->create());

        $file = new UploadedFile(
            $this->makeSampleJpeg(),
            'holiday-photo.jpg',
            'image/jpeg',
            null,
            true,
        );

        $item = $service->uploadImage($job, $file);
        $item->refresh();

        $this->assertSame(ImageItemStatus::ANALYZED, $item->status);
        $this->assertNotNull($item->analyzed_at);
        $this->assertNull($item->error_message);

        // --- metadata ---
        $this->assertIsArray($item->metadata);
        $this->assertSame(640, $item->metadata['width']);
        $this->assertSame(480, $item->metadata['height']);
        $this->assertSame('JPEG', $item->metadata['format']);
        $this->assertSame(4, $item->metadata['aspect_ratio']['width']);
        $this->assertSame(3, $item->metadata['aspect_ratio']['height']);
        $this->assertArrayHasKey('color_profile', $item->metadata);
        $this->assertArrayHasKey('resolution', $item->metadata);
        $this->assertArrayHasKey('orientation', $item->metadata);
        $this->assertArrayHasKey('transparency', $item->metadata);

        // EXIF/GPS: extracted (not omitted) AND flagged for the UI's
        // own privacy warning — see this app's own requirement that
        // GPS presence must be independently checkable, not just
        // buried in raw EXIF.
        $this->assertTrue($item->metadata['exif']['has_exif']);
        $this->assertSame('TestCam', $item->metadata['exif']['camera']['make']);
        $this->assertTrue($item->metadata['exif']['gps']['present']);
        $this->assertEqualsWithDelta(40.446, $item->metadata['exif']['gps']['latitude'], 0.001);
        $this->assertEqualsWithDelta(-79.982, $item->metadata['exif']['gps']['longitude'], 0.001);
        $this->assertTrue($item->hasGpsData());

        // --- quality analysis ---
        $this->assertIsArray($item->quality_analysis);
        $this->assertArrayHasKey('blur', $item->quality_analysis);
        $this->assertArrayHasKey('noise', $item->quality_analysis);
        $this->assertArrayHasKey('compression', $item->quality_analysis);
        $this->assertArrayHasKey('dynamic_range', $item->quality_analysis);
        $this->assertTrue($item->quality_analysis['compression']['applicable']);

        $this->assertIsInt($item->quality_score);
        $this->assertGreaterThanOrEqual(0, $item->quality_score);
        $this->assertLessThanOrEqual(100, $item->quality_score);
        $this->assertSame($item->quality_score, $item->quality_analysis['quality_score']);
    }

    public function test_a_png_is_marked_analyzed_with_compression_not_applicable(): void
    {
        Storage::fake('private-images');

        $service = app(ImageJobService::class);
        $job = $service->createJob(User::factory()->create());

        $imagick = new Imagick();
        $imagick->newImage(200, 150, new ImagickPixel('transparent'));
        $draw = new ImagickDraw();
        $draw->setFillColor(new ImagickPixel('rgba(200,50,50,0.6)'));
        $draw->circle(100, 75, 140, 75);
        $imagick->drawImage($draw);
        $imagick->setImageFormat('png');
        $path = tempnam(sys_get_temp_dir(), 'phase_s2_test_').'.png';
        $imagick->writeImage($path);
        $imagick->clear();
        $imagick->destroy();

        $item = $service->uploadImage($job, new UploadedFile($path, 'sticker.png', 'image/png', null, true));
        $item->refresh();

        $this->assertSame(ImageItemStatus::ANALYZED, $item->status);
        $this->assertSame('PNG', $item->metadata['format']);
        $this->assertTrue($item->metadata['transparency']['has_alpha_channel']);
        $this->assertTrue($item->metadata['transparency']['actually_transparent']);
        $this->assertFalse($item->quality_analysis['compression']['applicable']);
        $this->assertNull($item->quality_analysis['compression']['blockiness_ratio']);

        @unlink($path);
    }

    public function test_the_job_marks_the_item_failed_instead_of_crashing_when_the_stored_file_is_gone(): void
    {
        Storage::fake('private-images');

        $service = app(ImageJobService::class);
        $job = $service->createJob(User::factory()->create());

        $item = $service->uploadImage($job, new UploadedFile(
            $this->makeSampleJpeg(),
            'will-vanish.jpg',
            'image/jpeg',
            null,
            true,
        ));

        // Simulate App\Console\Commands\CleanupExpiredImageJobsCommand
        // (or any other process) having already removed the real file
        // out from under a still-pending analysis — see
        // AnalyzeImageMetadataJob's own docblock for this exact
        // scenario.
        Storage::disk('private-images')->delete($item->temp_path);
        $item->forceFill(['status' => ImageItemStatus::UPLOADED])->save();

        AnalyzeImageMetadataJob::dispatchSync($item);

        $item->refresh();

        $this->assertSame(ImageItemStatus::FAILED, $item->status);
        $this->assertNotNull($item->error_message);
        $this->assertStringContainsString('missing', strtolower((string) $item->error_message));
    }

    public function test_extractor_throws_for_a_stored_path_that_does_not_exist(): void
    {
        Storage::fake('private-images');

        $item = new ImageProcessingItem();
        $item->temp_path = 'nonexistent/job/originals/does-not-exist.jpg';
        $item->original_filename = 'does-not-exist.jpg';
        $item->file_size_bytes = 0;

        $this->expectException(ImageAnalysisException::class);

        app(ImageMetadataExtractor::class)->analyze($item);
    }

    /**
     * Returns a REAL JPEG fixture file
     * (tests/Fixtures/Images/sample-with-gps-exif.jpg) with genuinely
     * embedded EXIF/GPS — NOT generated at test-run time via
     * Imagick::setImageProperty('exif:...'), because that call was
     * confirmed during development to NOT actually persist EXIF into
     * the written file on this Imagick/ImageMagick build (properties
     * silently vanish on the writeImage() -> readImage() round-trip).
     * This fixture was built once with Imagick for the image content
     * and exiftool for the EXIF/GPS tags (the same rational
     * degrees/minutes/seconds format ImageMetadataExtractor::
     * gpsToDecimal() parses), then committed — real cameras/phones
     * produce EXIF the same way exiftool does, so this is a faithful
     * fixture, not a synthetic shortcut.
     */
    private function makeSampleJpeg(): string
    {
        return __DIR__.'/../../Fixtures/Images/sample-with-gps-exif.jpg';
    }
}
