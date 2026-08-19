<?php

declare(strict_types=1);

namespace App\Audit\Exceptions;

/**
 * Phase N1.5 (Free Trial) — thrown by
 * App\Audit\Services\AuditService::submit() when a logged-in user's
 * current plan blocks the action (daily audit limit reached, or the
 * plan/trial doesn't allow 'run-audit' at all). Caught by
 * App\Http\Controllers\AuditController::store() and turned into a
 * real, actionable redirect back to the form with this message —
 * never an uncaught 500.
 */
final class PlanLimitExceededException extends \RuntimeException
{
}