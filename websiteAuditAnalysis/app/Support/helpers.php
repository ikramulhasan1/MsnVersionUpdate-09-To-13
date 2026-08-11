<?php

declare(strict_types=1);

use App\Audit\Enums\AuditStatus;

if (! function_exists('audit_status_label')) {
    /**
     * Human-readable label for an audit status, accepting either the enum
     * or its raw string value (useful in Blade views).
     */
    function audit_status_label(AuditStatus|string $status): string
    {
        $status = $status instanceof AuditStatus ? $status : AuditStatus::from($status);

        return $status->label();
    }
}

if (! function_exists('audit_status_badge_class')) {
    /**
     * Bootstrap badge class for an audit status.
     */
    function audit_status_badge_class(AuditStatus|string $status): string
    {
        $status = $status instanceof AuditStatus ? $status : AuditStatus::from($status);

        return $status->badgeClass();
    }
}

if (! function_exists('audit_score_variant')) {
    /**
     * Bootstrap contextual variant (success/primary/warning/danger) for a
     * 0-100 audit score. Used to colour score badges, progress bars and
     * grade pills consistently across the dashboard.
     */
    function audit_score_variant(?int $score): string
    {
        if ($score === null) {
            return 'secondary';
        }

        return match (true) {
            $score >= 90 => 'success',
            $score >= 70 => 'primary',
            $score >= 50 => 'warning',
            default => 'danger',
        };
    }
}

if (! function_exists('audit_score_label')) {
    /**
     * Human-readable verdict for a 0-100 audit score.
     */
    function audit_score_label(?int $score): string
    {
        if ($score === null) {
            return 'Not available';
        }

        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 70 => 'Good',
            $score >= 50 => 'Needs Improvement',
            default => 'Poor',
        };
    }
}

if (! function_exists('audit_check_variant')) {
    /**
     * Bootstrap contextual variant for an individual check's status.
     * Normalizes the different per-analyzer status vocabularies (pass/
     * warning/fail, good/warning/critical) to a single set of variants
     * used consistently in the accordion and checks-overview bars.
     */
    function audit_check_variant(string $status): string
    {
        return match (strtolower($status)) {
            'pass', 'good' => 'success',
            'warning' => 'warning',
            'fail', 'critical' => 'danger',
            default => 'secondary',
        };
    }
}

if (! function_exists('audit_score_color_var')) {

    function audit_score_color_var(?int $score): string
    {
        return match (audit_score_variant($score)) {
            'success' => 'var(--audit-success)',
            'primary' => 'var(--audit-primary)',
            'warning' => 'var(--audit-warning)',
            'danger' => 'var(--audit-danger)',
            default => 'var(--audit-muted)',
        };
    }
}

if (! function_exists('audit_check_color_var')) {
    /**
     * Same mapping as audit_score_color_var(), but for an individual
     * check's pass/warning/fail status rather than a 0-100 score.
     */
    function audit_check_color_var(string $status): string
    {
        return match (audit_check_variant($status)) {
            'success' => 'var(--audit-success)',
            'warning' => 'var(--audit-warning)',
            'danger' => 'var(--audit-danger)',
            default => 'var(--audit-muted)',
        };
    }
}

if (! function_exists('audit_score_grade_letter')) {
    /**
     * Single-letter certification-style grade for the score-gauge "seal"
     * (A/B/C/D), following the same 90/70/50 thresholds as
     * audit_score_variant()/audit_score_label(). Separate from the
     * per-category $category['grade'] value (which comes straight from
     * an analyzer's own grade(), e.g. AccessibilityAnalyzer's A-F scale)
     * — this one is specifically for the overall-score seal, which has
     * no analyzer-supplied grade of its own.
     */
    function audit_score_grade_letter(?int $score): string
    {
        if ($score === null) {
            return '—';
        }

        return match (true) {
            $score >= 90 => 'A',
            $score >= 70 => 'B',
            $score >= 50 => 'C',
            default => 'D',
        };
    }
}
if (! function_exists('display_host')) {
    /**
     * Strip the scheme/www from a URL for a cleaner display string.
     */
    function display_host(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?: $url;

        return preg_replace('/^www\./', '', $host) ?? $host;
    }
}
