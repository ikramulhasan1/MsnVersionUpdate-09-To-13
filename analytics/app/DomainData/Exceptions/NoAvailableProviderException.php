<?php

declare(strict_types=1);

namespace App\DomainData\Exceptions;

/**
 * Phase Q1 — the domain-data counterpart to
 * App\KeywordData\Exceptions\NoAvailableProviderException. Thrown by
 * App\DomainData\DomainDataService when every active provider offering
 * a given DomainCapability (in priority order) was tried and failed,
 * or none is configured at all.
 */
final class NoAvailableProviderException extends \RuntimeException
{
}