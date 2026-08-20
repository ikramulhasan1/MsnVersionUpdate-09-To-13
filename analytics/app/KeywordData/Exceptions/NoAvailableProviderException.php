<?php

declare(strict_types=1);

namespace App\KeywordData\Exceptions;

/**
 * Phase O2 — thrown by App\KeywordData\KeywordDataService when EVERY
 * active provider offering a given capability was tried (in priority
 * order) and failed, or when no active provider offers that
 * capability at all (including "no provider configured yet"). Every
 * caller of KeywordDataService (Phase O3's Keyword Research page,
 * Phase O4's Keyword Magic Tool) must catch this and show a real,
 * specific error — never let it surface as a raw 500.
 */
final class NoAvailableProviderException extends \RuntimeException
{
}