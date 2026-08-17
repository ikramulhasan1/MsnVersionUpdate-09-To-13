{{--
    Phase K3 (Bulk Audit) — two of this module's three submission entry
    points live on this one page: a pasted URL-per-line textarea, and a
    CSV upload. Both post to the SAME route (BulkAuditController::store())
    and are mutually exclusive at the validation layer (see
    StoreBulkAuditRequest's own docblock) — a person fills in one or the
    other, not both, though nothing here disables one field just because
    the other has a value; the server-side validation is the actual
    enforcement.

    Mode selection reuses home/index.blade.php's own Quick Scan/Full
    Audit radio pattern (Phase K1) verbatim — same two options, same
    App\Audit\Enums\AuditMode::description() text, same live-updating
    hint paragraph — so choosing a mode looks and behaves identically
    whether a person is auditing one site or fifty.
--}}
@extends('layouts.app')

@section('title', 'Bulk Website Audit')

@section('content')
    <section class="container dashboard-section">
        <div class="mb-4">
            <p class="text-secondary small mb-1">Website Audit</p>
            <h1 class="h3 mb-0">Audit Multiple Websites</h1>
            <p class="text-secondary mt-2 mb-0">
                Paste a list of URLs (one per line) or upload a CSV file, and every one will be queued for
                audit together.
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('bulk-audits.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="bulk-audit-name" class="form-label small fw-medium">
                            Batch Name <span class="text-secondary fw-normal">(optional)</span>
                        </label>
                        <input type="text" class="form-control" id="bulk-audit-name" name="name"
                            value="{{ old('name') }}" placeholder="e.g. Client A — Q3 Review" maxlength="255">
                    </div>

                    {{-- Reuses home/index.blade.php's own Quick Scan/Full Audit radio
                         pattern (Phase K1) verbatim — see this file's own docblock. --}}
                    <div class="mb-4">
                        <p class="form-label small fw-medium mb-2">Audit Mode</p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="bulk-mode-quick"
                                    value="quick" @checked(old('mode', 'quick') === 'quick')>
                                <label class="form-check-label" for="bulk-mode-quick"
                                    title="{{ \App\Audit\Enums\AuditMode::QUICK->description() }}">
                                    Quick Scan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="bulk-mode-full"
                                    value="full" @checked(old('mode') === 'full')>
                                <label class="form-check-label" for="bulk-mode-full"
                                    title="{{ \App\Audit\Enums\AuditMode::FULL->description() }}">
                                    Full Audit
                                </label>
                            </div>
                        </div>
                        <p class="text-secondary small mt-2 mb-0" id="bulk-mode-hint">
                            {{ old('mode') === 'full' ? \App\Audit\Enums\AuditMode::FULL->description() : \App\Audit\Enums\AuditMode::QUICK->description() }}
                        </p>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label for="bulk-audit-urls" class="form-label small fw-medium">
                                Paste URLs <span class="text-secondary fw-normal">(one per line)</span>
                            </label>
                            <textarea class="form-control" id="bulk-audit-urls" name="urls" rows="10"
                                placeholder="https://example.com&#10;https://another-site.com">{{ old('urls') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="bulk-audit-csv" class="form-label small fw-medium">
                                Or Upload a CSV File
                            </label>
                            <input type="file" class="form-control" id="bulk-audit-csv" name="csv"
                                accept=".csv,.txt">
                            <p class="text-secondary small mt-2 mb-0">
                                The first column of every row is read as a URL — a header row (or any other
                                row whose first cell isn't a URL) is skipped automatically.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">Queue Audits</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Mirrors home/index.blade.php's own hint-update handler (Phase
        // K1) — see that file's own docblock for why these two strings
        // are duplicated in plain JS rather than round-tripping to the
        // server.
        const BULK_AUDIT_MODE_HINTS = {
            full: 'Crawls multiple pages and includes real PageSpeed Insights data. Takes longer, most complete.',
            quick: 'Homepage only, no PageSpeed Insights call. Much faster, less depth.',
        };

        document.querySelectorAll('input[name="mode"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const hint = document.getElementById('bulk-mode-hint');
                if (hint && BULK_AUDIT_MODE_HINTS[radio.value]) {
                    hint.textContent = BULK_AUDIT_MODE_HINTS[radio.value];
                }
            });
        });
    </script>
@endpush