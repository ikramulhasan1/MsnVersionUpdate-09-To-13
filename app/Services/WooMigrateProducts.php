<?php

namespace App\Console\Commands;

use App\Services\ProductMapper;
use App\Services\WooCategoryService;
use App\Services\WooCommerceService;
use App\Services\WooImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WooMigrateProducts extends Command
{
    protected $signature = 'woo:migrate';

    protected $description = 'Migrate Laravel Products To WooCommerce';

    protected WooCommerceService $woo;

    protected ProductMapper $mapper;

    protected WooCategoryService $category;

    protected WooImageService $image;

    public function __construct()
    {
        parent::__construct();

        $this->woo = new WooCommerceService;

        $this->mapper = new ProductMapper;

        $this->category = new WooCategoryService;

        $this->image = new WooImageService;
    }

    public function handle()
    {
        $this->info('');

        $this->info('------------------------------------------');
        $this->info(' Laravel → WooCommerce Migration Started');
        $this->info('------------------------------------------');

        try {

            $status = $this->woo->testConnection();

            $this->info(
                'Connected : '.
                $status->environment->home_url
            );

        } catch (\Throwable $e) {

            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();

        $this->info('Syncing Categories...');

        $this->category->initialize();

        $this->info('Categories Ready');

        $this->newLine();

        $total = DB::table('products')
            ->where('active', 1)
            ->count();

        $this->info("Total Products : {$total}");

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        DB::table('products')
            ->where('active', 1)
            ->orderBy('id')
            ->chunk(100, function ($products) use ($bar) {

                foreach ($products as $product) {

                    try {

                        $this->importProduct($product);

                    } catch (\Throwable $e) {

                        Log::error(
                            'Woo Import Error',
                            [
                                'product_id' => $product->id,
                                'message' => $e->getMessage(),
                            ]
                        );

                    }

                    $bar->advance();

                }

            });

        $bar->finish();

        $this->newLine(2);

        $this->info('Migration Finished Successfully.');

        return self::SUCCESS;
    }

    protected function importProduct($product)
    {
        /*
        |--------------------------------------------------------------------------
        | Skip Duplicate Product
        |--------------------------------------------------------------------------
        */

        $existing = $this->woo->getProductBySlug(
            $product->slug
        );

        if ($existing) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Category Mapping
        |--------------------------------------------------------------------------
        */

        $categoryIds = $this->category
            ->getWooCategoryIds($product);

        /*
        |--------------------------------------------------------------------------
        | Product Data
        |--------------------------------------------------------------------------
        */

        $data = $this->mapper
            ->map(
                $product,
                $categoryIds
            );

        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        $wooProduct = $this->woo
            ->createProduct($data);

        if (
            ! $wooProduct
            ||
            empty($wooProduct->id)
        ) {

            throw new \Exception(
                'Unable to create product.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Featured Image
        |--------------------------------------------------------------------------
        */

        if (! empty($product->cover)) {

            $image = $this->image->process(
                $wooProduct->id,
                $product->cover
            );

            if (
                empty($image['success'])
            ) {

                Log::warning(
                    'Image Import Failed',
                    [
                        'product_id' => $product->id,
                        'image' => $product->cover,
                        'message' => $image['message'] ?? null,
                    ]
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Gallery Images
        |--------------------------------------------------------------------------
        */

        if (! empty($product->screenshots)) {

            $gallery = str_replace(
                "'",
                '',
                $product->screenshots
            );

            $gallery = explode(
                ',',
                $gallery
            );

            foreach ($gallery as $file) {

                $file = trim($file);

                if ($file == '') {
                    continue;
                }

                $this->image->process(
                    $wooProduct->id,
                    $file
                );

            }

        }        /*
        |--------------------------------------------------------------------------
        | Save Migration Reference
        |--------------------------------------------------------------------------
        */

        DB::table('products')
            ->where('id', $product->id)
            ->update([
                'woo_product_id' => $wooProduct->id,
                'updated_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Console Output
        |--------------------------------------------------------------------------
        */

        $this->line('');

        $this->line(
            "✔ Product #{$product->id} => Woo #{$wooProduct->id}"
        );
    }

    /**
     * Check Product Exists
     */
    protected function productExists(string $slug): bool
    {
        return (bool) $this->woo->getProductBySlug($slug);
    }

    /**
     * Import Single Product
     */
    protected function importSingle(int $id): bool
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        if (! $product) {
            return false;
        }

        $this->importProduct($product);

        return true;
    }

    /**
     * Retry Failed Product
     */
    protected function retryProduct($product)
    {
        try {

            $this->importProduct($product);

        } catch (\Throwable $e) {

            Log::error(
                'Retry Failed',
                [
                    'product_id' => $product->id,
                    'message' => $e->getMessage(),
                ]
            );

        }
    }
}
