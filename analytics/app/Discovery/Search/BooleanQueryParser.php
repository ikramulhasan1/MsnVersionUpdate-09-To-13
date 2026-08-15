<?php

declare(strict_types=1);

namespace App\Discovery\Search;

/**
 * Parses a simple boolean search string (e.g. "Restaurant AND WordPress
 * AND NOT Facebook") into an ordered list of BooleanQueryTerm — the
 * Website Discovery module's "Boolean Query (Advanced)" search-panel
 * field (Phase F1). WebsiteSearchService::applyBooleanQuery() applies
 * the result to a real query — every term is matched, LIKE-style,
 * across a fixed set of free-text columns (business name, domain,
 * industry, sub-niche, and every technology column) — see that
 * method's own docblock for the exact column list.
 *
 * Deliberately a simple, LEFT-TO-RIGHT, NO-OPERATOR-PRECEDENCE parser
 * — no parentheses/grouping support. AND/OR/NOT are recognized
 * case-insensitively; every other token is a search term. A term
 * appearing with no explicit operator before it (including the very
 * first term) is treated as AND — i.e. required — matching how the
 * prompt's own example ("Restaurant AND WordPress AND NOT Facebook")
 * reads even without the leading "AND". A double-quoted phrase (e.g.
 * `"fine dining"`) is treated as a single term rather than being split
 * on its internal whitespace.
 *
 * "OR" and "AND" are NOT combinable with correct precedence in one
 * query (there is no grouping to resolve `A AND B OR C` unambiguously)
 * — each term's operator only describes its relationship to the
 * immediately preceding term, evaluated strictly in the order written.
 * A future phase could add real operator precedence/parentheses; this
 * one deliberately does not, in the same spirit as this module's other
 * "build the honest version now, extend later" filters.
 */
final class BooleanQueryParser
{
    private const array KNOWN_OPERATORS = ['AND', 'OR', 'NOT'];

    /**
     * @return array<int, BooleanQueryTerm>
     */
    public function parse(string $query): array
    {
        $tokens = $this->tokenize($query);

        $terms = [];
        $pendingOperator = BooleanQueryOperator::AND;
        $negateNext = false;

        foreach ($tokens as $token) {
            $upperToken = strtoupper($token);

            if (in_array($upperToken, self::KNOWN_OPERATORS, true)) {
                match ($upperToken) {
                    'AND' => $pendingOperator = BooleanQueryOperator::AND,
                    'OR' => $pendingOperator = BooleanQueryOperator::OR,
                    'NOT' => $negateNext = true,
                };

                continue;
            }

            $terms[] = new BooleanQueryTerm(
                term: $token,
                operator: $negateNext ? BooleanQueryOperator::NOT : $pendingOperator,
            );

            // Reset for the next term — each operator token only applies
            // to the single term that follows it.
            $pendingOperator = BooleanQueryOperator::AND;
            $negateNext = false;
        }

        return $terms;
    }

    /**
     * Splits on whitespace, except a double-quoted phrase (kept intact,
     * quotes stripped) is treated as one token — so `"fine dining"
     * AND WordPress` tokenizes as ['fine dining', 'AND', 'WordPress'],
     * not four separate words.
     *
     * @return array<int, string>
     */
    private function tokenize(string $query): array
    {
        preg_match_all('/"([^"]+)"|(\S+)/', trim($query), $matches, PREG_SET_ORDER);

        $tokens = [];

        foreach ($matches as $match) {
            // Checking $match[0] (the full match, always present) for
            // surrounding quotes and stripping them manually — rather
            // than indexing $match[1]/$match[2] directly — sidesteps a
            // PHP/PCRE quirk where a trailing unmatched capture group can
            // be omitted from $match entirely instead of padded with an
            // empty string, which would make indexing it unreliable here.
            $full = $match[0];

            if (strlen($full) >= 2 && $full[0] === '"' && $full[strlen($full) - 1] === '"') {
                $token = substr($full, 1, -1);
            } else {
                $token = $full;
            }

            if ($token !== '') {
                $tokens[] = $token;
            }
        }

        return $tokens;
    }
}