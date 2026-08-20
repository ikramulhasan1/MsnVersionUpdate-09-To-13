<?php

declare(strict_types=1);

namespace App\KeywordData\Exceptions;

/**
 * Phase O2 — see App\KeywordData\Contracts\ApiProviderAdapterInterface's
 * own docblock for when this fires (and why it should be rare in
 * practice).
 */
final class CapabilityNotSupportedException extends \RuntimeException
{
}