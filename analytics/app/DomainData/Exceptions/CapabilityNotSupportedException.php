<?php

declare(strict_types=1);

namespace App\DomainData\Exceptions;

/**
 * Phase Q1 — see App\DomainData\Contracts\DomainProviderAdapterInterface's
 * own docblock, and App\KeywordData\Exceptions\CapabilityNotSupportedException's
 * (the keyword-data equivalent this class mirrors) for the same
 * reasoning.
 */
final class CapabilityNotSupportedException extends \RuntimeException
{
}