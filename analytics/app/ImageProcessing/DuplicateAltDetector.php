<?php

declare(strict_types=1);

namespace App\ImageProcessing;

/**
 * Image Everything (Phase S3) — pure PHP, no external API: flags pairs
 * of images within the SAME job whose currently-selected alt text is
 * the same or near-identical, using normalized token-overlap (Jaccard)
 * similarity rather than an exact string match — "Nike Air Zoom
 * Pegasus 41 running shoe" and "Nike Air Zoom Pegasus 41 running
 * shoes" are different strings but the same non-differentiated alt
 * text in practice, which is exactly the case this app's own
 * requirement ("প্রায়-একই alt text") calls out.
 */
final class DuplicateAltDetector
{
    /**
     * @param  list<array{id: int, alt: string, label: string}>  $entries  one row per image in the job: 'id' is the ImageProcessingItem id, 'alt' its currently-selected alt text, 'label' a human-readable reference (filename or "Image N") used to build the suggestion text below.
     * @return array<int, array{is_duplicate: bool, similar_to: list<int>, suggestion: ?string}> keyed by item id
     */
    public function detect(array $entries): array
    {
        $threshold = (float) config('image-processing.image_seo.duplicate_alt_similarity_threshold', 0.80);

        $results = [];

        foreach ($entries as $entry) {
            $results[$entry['id']] = ['is_duplicate' => false, 'similar_to' => [], 'suggestion' => null];
        }

        $count = count($entries);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $a = $entries[$i];
                $b = $entries[$j];

                if (trim($a['alt']) === '' || trim($b['alt']) === '') {
                    continue;
                }

                if ($this->similarity($a['alt'], $b['alt']) >= $threshold) {
                    $results[$a['id']]['is_duplicate'] = true;
                    $results[$a['id']]['similar_to'][] = $b['id'];
                    $results[$b['id']]['is_duplicate'] = true;
                    $results[$b['id']]['similar_to'][] = $a['id'];
                }
            }
        }

        $byId = [];

        foreach ($entries as $entry) {
            $byId[$entry['id']] = $entry['label'];
        }

        foreach ($results as $id => &$result) {
            if (! $result['is_duplicate']) {
                continue;
            }

            $otherLabels = array_map(static fn (int $otherId): string => $byId[$otherId] ?? "image #{$otherId}", $result['similar_to']);

            $result['suggestion'] = sprintf(
                'This alt text is very close to %s. Add a distinguishing detail — color, angle, variant, or position (e.g. "%s") — so each image reads uniquely.',
                implode(' and ', $otherLabels),
                $byId[$id] ?? "image #{$id}",
            );
        }

        unset($result);

        return $results;
    }

    /**
     * Jaccard similarity over normalized word tokens — deliberately not
     * Levenshtein/similar_text() (those measure character-level edit
     * distance, which penalizes reordered words as heavily as
     * genuinely different ones; two alt texts that use the same words
     * in a different order ARE the "practically identical" case this
     * app's own requirement is about).
     */
    private function similarity(string $a, string $b): float
    {
        $tokensA = $this->tokens($a);
        $tokensB = $this->tokens($b);

        if ($tokensA === [] || $tokensB === []) {
            return 0.0;
        }

        $intersection = count(array_intersect($tokensA, $tokensB));
        $union = count(array_unique(array_merge($tokensA, $tokensB)));

        return $union === 0 ? 0.0 : $intersection / $union;
    }

    /**
     * @return list<string>
     */
    private function tokens(string $text): array
    {
        return array_values(array_unique(array_filter(
            preg_split('/[^a-z0-9]+/i', strtolower($text)) ?: [],
        )));
    }
}