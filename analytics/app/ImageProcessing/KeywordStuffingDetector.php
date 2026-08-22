<?php

declare(strict_types=1);

namespace App\ImageProcessing;

/**
 * Image Everything (Phase S3) — pure PHP, no external API, no AI: this
 * class ONLY counts word frequency in a piece of already-generated
 * text and, when a single word dominates it, mechanically trims the
 * extra repeats down to a natural-reading amount. It never re-writes
 * or re-phrases anything beyond that trim — App\ImageProcessing\SmartMetadataGenerator
 * is the only thing that ever composes new wording; this class is a
 * post-processing filter every candidate it produces passes through.
 *
 * TWO-CONDITION FLAG (see scan()): a text is only flagged once its
 * single most-repeated significant word BOTH clears an outright
 * repeat-count floor (config('image-processing.image_seo.stuffing_min_repeats'))
 * AND its own share of all significant words exceeds a ratio ceiling
 * (config('image-processing.image_seo.stuffing_ratio_threshold')) —
 * ratio alone would flag a single 6-word Short-style alt for using
 * "shoes" twice, which is completely normal, not stuffing.
 */
final class KeywordStuffingDetector
{
    /**
     * @return array{flagged: bool, word: ?string, count: int, ratio: float}
     */
    public function scan(string $text): array
    {
        $words = $this->significantWords($text);
        $total = count($words);

        if ($total === 0) {
            return ['flagged' => false, 'word' => null, 'count' => 0, 'ratio' => 0.0];
        }

        $counts = array_count_values($words);
        arsort($counts);

        $topWord = (string) array_key_first($counts);
        $topCount = $counts[$topWord];
        $ratio = round($topCount / $total, 2);

        $ratioThreshold = (float) config('image-processing.image_seo.stuffing_ratio_threshold', 0.30);
        $minRepeats = (int) config('image-processing.image_seo.stuffing_min_repeats', 3);

        $flagged = $topCount >= $minRepeats && $ratio > $ratioThreshold;

        return ['flagged' => $flagged, 'word' => $topWord, 'count' => $topCount, 'ratio' => $ratio];
    }

    /**
     * scan() plus the actual fix — the text a caller should actually
     * USE. When nothing is flagged, 'text' is just the original
     * unchanged (no unnecessary edits to already-natural copy).
     *
     * @return array{flagged: bool, word: ?string, count: int, ratio: float, text: string, was_fixed: bool}
     */
    public function scanAndFix(string $text): array
    {
        $scan = $this->scan($text);

        if (! $scan['flagged'] || $scan['word'] === null) {
            return $scan + ['text' => $text, 'was_fixed' => false];
        }

        $fixed = $this->naturalize($text, $scan['word']);

        // naturalize() can legitimately return the same string back
        // (e.g. every occurrence was load-bearing and couldn't be
        // trimmed without leaving a grammatically broken fragment) —
        // only report was_fixed when something genuinely changed.
        return $scan + ['text' => $fixed, 'was_fixed' => $fixed !== $text];
    }

    /**
     * Keeps the FIRST two occurrences of the flagged word (repeating a
     * keyword twice in a short piece of alt/caption copy still reads
     * naturally) and mechanically drops every occurrence after that,
     * along with the whitespace it leaves behind — never substitutes a
     * synonym (that would be composing new wording, which this class
     * deliberately never does).
     */
    private function naturalize(string $text, string $flaggedWord): string
    {
        $tokens = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($tokens === false) {
            return $text;
        }

        $seen = 0;
        $kept = [];

        foreach ($tokens as $token) {
            if (trim($token) === '') {
                $kept[] = $token;

                continue;
            }

            $bare = strtolower(preg_replace('/[^a-z0-9]/i', '', $token) ?? '');

            if ($bare === strtolower($flaggedWord)) {
                $seen++;

                if ($seen > 2) {
                    // Drop this occurrence AND the whitespace token
                    // immediately before it, so we don't leave a
                    // double space behind.
                    if ($kept !== [] && trim(end($kept)) === '') {
                        array_pop($kept);
                    }

                    continue;
                }
            }

            $kept[] = $token;
        }

        $result = implode('', $kept);
        $result = preg_replace('/\s{2,}/', ' ', $result) ?? $result;
        $result = preg_replace('/\s+([,.\-|])/', '$1', $result) ?? $result;
        $result = trim($result, " ,-|");

        return $result === '' ? $text : $result;
    }

    /**
     * @return list<string>
     */
    private function significantWords(string $text): array
    {
        $stopwords = array_flip(config('image-processing.image_seo.stopwords', []));

        $tokens = preg_split('/[^a-z0-9]+/i', strtolower($text)) ?: [];

        return array_values(array_filter(
            $tokens,
            static fn (string $t): bool => $t !== '' && strlen($t) > 2 && ! isset($stopwords[$t]),
        ));
    }
}