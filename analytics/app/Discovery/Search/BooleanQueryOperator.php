<?php

declare(strict_types=1);

namespace App\Discovery\Search;

/**
 * Internal to BooleanQueryParser/BooleanQueryTerm — not a UI-facing
 * filter vocabulary (unlike everything in App\Discovery\Enums, which
 * a search-panel.blade.php dropdown/checkbox renders options from),
 * so it lives in this namespace rather than that one.
 */
enum BooleanQueryOperator: string
{
    case AND = 'and';
    case OR = 'or';
    case NOT = 'not';
}