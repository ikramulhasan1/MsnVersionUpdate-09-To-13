<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiUsageLog;
use Illuminate\View\View;

/**
 * Phase O2 (Keyword Data Service Layer) — read-only cost visibility
 * for an Admin, over App\Models\ApiUsageLog (see that table's own
 * migration docblock for the "estimated, not a real invoice" caveat
 * that applies to every figure this page shows).
 */
final class ApiUsageController extends Controller
{
    public function index(): View
    {
        $todayTotal = ApiUsageLog::query()
            ->whereDate('created_at', now()->toDateString())
            ->sum('estimated_cost_usd');

        $monthTotal = ApiUsageLog::query()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('estimated_cost_usd');

        $byProvider = ApiUsageLog::query()
            ->with('provider')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn (ApiUsageLog $log): string => $log->provider?->name ?? 'Deleted provider')
            ->map(fn ($logs) => [
                'calls' => $logs->count(),
                'keywords' => $logs->sum('keyword_count'),
                'cost' => $logs->sum('estimated_cost_usd'),
            ])
            ->sortByDesc('cost');

        $recentLogs = ApiUsageLog::query()->with('provider')->latest('created_at')->limit(50)->get();

        return view('admin.api-usage.index', [
            'todayTotal' => $todayTotal,
            'monthTotal' => $monthTotal,
            'byProvider' => $byProvider,
            'recentLogs' => $recentLogs,
        ]);
    }
}