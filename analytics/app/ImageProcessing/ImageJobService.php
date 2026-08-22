<?php

declare(strict_types=1);

namespace App\ImageProcessing;

use App\Enums\ImageItemStatus;
use App\Enums\ImageJobStatus;
use App\ImageProcessing\Exceptions\InvalidImageException;
use App\ImageProcessing\Jobs\AnalyzeImageMetadataJob;
use App\Models\ImageProcessingItem;
use App\Models\ImageProcessingJob;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image Everything (Phase S1) — the ONE place jobs get created and
 * images get uploaded/validated/stored. Every later phase (S2's own
 * metadata extraction, S3's own SEO generation, S4's own resize/
 * compress/convert) reads an already-validated, already-stored
 * ImageProcessingItem through this service or its own models directly
 * — none of them re-implement upload handling.
 *
 * SECURITY — UUID-BASED STORED FILENAMES: the original filename a
 * person uploaded (kept only in $original_filename, for DISPLAY
 * purposes) is NEVER used as part of the actual storage path. Every
 * stored file gets a fresh UUID-based name instead — this avoids path-
 * traversal attempts hidden in a crafted filename (e.g.
 * "../../../etc/passwd.jpg") ever reaching the filesystem layer at
 * all, and avoids filename collisions between two different uploads
 * that happen to share a name.
 */
final class ImageJobService
{
    public function createJob(User $user): ImageProcessingJob
    {
        $ttlHours = (int) config('image-processing.job_ttl_hours', 2);

        return $user->imageProcessingJobs()->create([
            'status' => ImageJobStatus::PENDING,
            'expires_at' => now()->addHours($ttlHours),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * PRODUCTION-CRITICAL VALIDATION — read before removing any check
     * below: this method is the ONLY path an uploaded file can reach
     * this app's own storage through, so every one of these checks
     * matters. Order matters too — cheapest/most-obviously-wrong
     * checks first (extension, declared MIME), the real magic-byte
     * signature check LAST and authoritative (a declared MIME type is
     * exactly that — DECLARED, easily spoofed by whoever built the
     * upload request; the signature check reads the file's own actual
     * first bytes, which a renamed-but-not-re-encoded malicious file
     * can't fake without breaking the image format itself).
     *
     * @throws InvalidImageException
     */
    public function uploadImage(ImageProcessingJob $job, UploadedFile $file): ImageProcessingItem
    {
        $allowedTypes = config('image-processing.allowed_types', []);
        $declaredMime = $file->getMimeType();

        if ($declaredMime === null || ! array_key_exists($declaredMime, $allowedTypes)) {
            throw new InvalidImageException("Unsupported file type: {$file->getClientOriginalName()}. Allowed: JPEG, PNG, WebP, GIF.");
        }

        $this->verifyFileSignature($file, $declaredMime, $allowedTypes[$declaredMime]);

        $dimensions = @getimagesize($file->getRealPath());

        if ($dimensions === false) {
            throw new InvalidImageException("Could not read image dimensions for: {$file->getClientOriginalName()}. The file may be corrupted.");
        }

        $extension = $file->getClientOriginalExtension() ?: $this->extensionForMime($declaredMime);
        $storedFilename = Str::uuid()->toString().'.'.$extension;
        $relativePath = "{$job->uuid}/originals/{$storedFilename}";

        Storage::disk('private-images')->putFileAs(
            "{$job->uuid}/originals",
            $file,
            $storedFilename,
        );

        $item = $job->items()->create([
            'original_filename' => $file->getClientOriginalName(),
            'temp_path' => $relativePath,
            'mime_type' => $declaredMime,
            'file_size_bytes' => $file->getSize(),
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'format' => strtoupper($extension),
            'status' => ImageItemStatus::UPLOADED,
        ]);

        $job->update([
            'total_images' => $job->items()->count(),
            'last_activity_at' => now(),
        ]);

        // Phase S2 — queued, not synchronous (see
        // App\ImageProcessing\Jobs\AnalyzeImageMetadataJob's own docblock for why):
        // dispatched once per image, right as that image's own upload
        // finishes, so a bulk upload's images each start analysis
        // independently rather than waiting on one another.
        AnalyzeImageMetadataJob::dispatch($item);

        return $item;
    }

    /**
     * Reads the file's own real first bytes and compares them against
     * the known magic-byte signature for the DECLARED mime type — a
     * mismatch means either genuine corruption or a deliberately
     * mislabeled file, and either way this app refuses it rather than
     * trusting the declared MIME type alone.
     *
     * WebP gets a second check beyond the generic RIFF prefix every
     * RIFF-container format shares (WAV audio also starts with RIFF) —
     * bytes 8-11 must additionally spell "WEBP" for this to actually
     * be a WebP image specifically, not just some other RIFF-based
     * file renamed to look like one.
     *
     * @throws InvalidImageException
     */
    private function verifyFileSignature(UploadedFile $file, string $declaredMime, string $expectedHexPrefix): void
    {
        $handle = fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw new InvalidImageException("Could not read file: {$file->getClientOriginalName()}.");
        }

        $header = fread($handle, 16);
        fclose($handle);

        $actualHexPrefix = bin2hex(substr($header, 0, strlen($expectedHexPrefix) / 2));

        if ($actualHexPrefix !== $expectedHexPrefix) {
            throw new InvalidImageException("File signature doesn't match its declared type: {$file->getClientOriginalName()}. This file may be corrupted or mislabeled.");
        }

        if ($declaredMime === 'image/webp') {
            $webpMarker = substr($header, 8, 4);

            if ($webpMarker !== 'WEBP') {
                throw new InvalidImageException("File signature doesn't match WebP format: {$file->getClientOriginalName()}.");
            }
        }
    }

    private function extensionForMime(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };
    }
}