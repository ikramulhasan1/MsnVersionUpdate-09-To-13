<?php

declare(strict_types=1);

namespace App\Discovery\Search;

/**
 * One parsed term from a Boolean Query string (see BooleanQueryParser)
 * — the raw text to search for, and how it relates to the terms before
 * it (AND/OR require it; NOT excludes it). See BooleanQueryParser's own
 * docblock for the exact left-to-right, no-parentheses evaluation rule
 * this operator is applied under.
 */
final readonly class BooleanQueryTerm
{
    public function __construct(
        public string $term,
        public BooleanQueryOperator $operator,
    ) {
    }
}