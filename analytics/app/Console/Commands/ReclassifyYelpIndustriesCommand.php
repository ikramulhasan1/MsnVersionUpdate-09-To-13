<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Discovery\Taxonomy\YelpCategoryClassifier;
use App\Models\DiscoveredWebsite;
use Illuminate\Console\Command;

final class ReclassifyYelpIndustriesCommand extends Command
{
    protected $signature = 'discovery:reclassify-yelp-industries';

    protected $description = 'Re-map existing Yelp-discovered websites\' raw category text onto the curated Industry/Sub-Niche taxonomy.';

    public function handle(): int
    {
        $classifier = new YelpCategoryClassifier();

        $websites = DiscoveredWebsite::query()
            ->where('discovery_source', 'yelp')
            ->whereNotNull('industry')
            ->get();

        $reclassified = 0;
        $unmatched = 0;

        foreach ($websites as $website) {
            $rawCategories = array_values(array_filter([
                $website->industry,
                $website->sub_niche,
            ]));

            $classification = $classifier->classify($rawCategories);

            if ($classification['industry'] === null) {
                $unmatched++;
                continue;
            }

            $website->update([
                'industry' => $classification['industry'],
                'sub_niche' => $classification['subNiche'],
            ]);

            $reclassified++;
        }

        $this->info(sprintf(
            'Reclassified %d website(s). %d website(s) had no matching curated industry and were left unchanged.',
            $reclassified,
            $unmatched,
        ));

        return self::SUCCESS;
    }
}