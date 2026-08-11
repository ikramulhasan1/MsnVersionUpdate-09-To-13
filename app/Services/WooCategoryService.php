<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooImageService
{
    /**
     * Laravel Cover Image Path
     */
    protected string $laravelImagePath =
        '/home/u972008518/domains/vectorsdrawing.com/public_html/public/storage/covers';

    /**
     * WordPress Upload Path
     */
    protected string $wordpressUploadPath =
        '/home/u972008518/domains/crafty7.com/public_html/wp-content/uploads';

    /**
     * REST Endpoint
     */
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint =
            rtrim(env('WOO_URL'), '/')
            . '/wp-json/laravel/v1/attach-image';
    }

    /**
     * Full Process
     */
    public function process(
        int $productId,
        ?string $filename
    ): array {

        if (empty($filename)) {

            return [
                'success' => false,
                'message' => 'Empty image'
            ];

        }

        if (!$this->copy($filename)) {

            return [
                'success' => false,
                'message' => 'Laravel image not found'
            ];

        }

        return $this->attach(
            $productId,
            $filename
        );
    }

    /**
     * Copy Image
     */
    protected function copy(
        string $filename
    ): bool {

        $source =
            $this->laravelImagePath
            . '/'
            . $filename;

        if (!file_exists($source)) {

            Log::warning(
                'Image Missing',
                [
                    'image' => $filename
                ]
            );

            return false;

        }

        $year = date('Y');
        $month = date('m');

        $destinationDirectory =
            $this->wordpressUploadPath
            . "/{$year}/{$month}";

        if (!is_dir($destinationDirectory)) {

            mkdir(
                $destinationDirectory,
                0755,
                true
            );

        }

        $destination =
            $destinationDirectory
            . '/'
            . $filename;

        if (!file_exists($destination)) {

            copy(
                $source,
                $destination
            );

        }

        return true;
    }

    /**
     * Attach Image
     */
    protected function attach(
        int $productId,
        string $filename
    ): array {

        $response = Http::timeout(120)
            ->post(
                $this->endpoint,
                [
                    'product_id' => $productId,
                    'filename' => $filename,
                ]
            );

        if (!$response->successful()) {

            Log::error(
                'Image Attach Failed',
                [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]
            );

            return [
                'success' => false,
                'message' => $response->body()
            ];

        }

        return $response->json();
    }

    /**
     * Gallery Images
     */
    public function processGallery(
        int $productId,
        ?string $gallery
    ): void {

        if (empty($gallery)) {
            return;
        }

        $gallery = str_replace(
            "'",
            "",
            $gallery
        );

        $images = explode(
            ",",
            $gallery
        );

        foreach ($images as $image) {

            $image = trim($image);

            if ($image == '') {
                continue;
            }

            $this->process(
                $productId,
                $image
            );

        }

    }
}