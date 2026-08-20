@extends('layouts.app')

@section('title', 'Keyword Magic Tool — Website Audit & Analysis Platform')

@section('content')
    <section class="container-fluid py-4">
        <h1 class="h4 fw-semibold mb-1">Keyword Magic Tool</h1>
        <p class="text-secondary mb-4">Explore hundreds of keyword ideas from one seed keyword.</p>

        <form method="GET" action="{{ route('keyword-magic-tool.show') }}" class="card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="seed" class="form-label">Seed Keyword</label>
                        <input type="text" class="form-control" id="seed" name="seed"
                            value="{{ old('seed', $seed) }}" placeholder="e.g. running shoes" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country">
                            @foreach (['United States', 'United Kingdom', 'Bangladesh', 'India', 'Canada', 'Australia'] as $option)
                                <option value="{{ $option }}" @selected(old('country', $country ?? 'United States') === $option)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="language" class="form-label">Language</label>
                        <select class="form-select" id="language" name="language">
                            @foreach (['English', 'Bengali'] as $option)
                                <option value="{{ $option }}" @selected(old('language', $language ?? 'English') === $option)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </div>
        </form>

        @if ($result !== null)
            @if ($result['error'] !== null)
                <div class="alert alert-warning">
                    {{ $result['error'] }}
                    @can('view-admin-panel')
                        <a href="{{ route('admin.api-providers.index') }}">Set one up in API Providers</a>.
                    @endcan
                </div>
            @elseif (count($result['keywords']) === 0)
                <div class="alert alert-secondary">No related keywords found for "{{ $seed }}".</div>
            @else
                <div id="kmt-app" data-keywords="{{ json_encode($result['keywords']) }}" data-seed="{{ $seed }}">
                    <div class="row g-3">
                        {{-- Left sidebar: topic groups --}}
                        <div class="col-12 col-lg-2">
                            <div class="card">
                                <div class="card-body p-3">
                                    <h2 class="h6 fw-semibold mb-2">Groups</h2>
                                    <div id="kmt-groups" class="d-flex flex-column gap-1 small"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Main area: filters + table --}}
                        <div class="col-12 col-lg-10">
                            {{-- Filter panel --}}
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <ul class="nav nav-pills mb-3" id="kmt-match-type">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" data-match="broad">Broad Match</button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-match="phrase">Phrase Match</button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-match="exact">Exact Match</button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-match="related">Related</button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-match="questions">Questions</button>
                                        </li>
                                    </ul>
                                    <p class="text-secondary small mb-3">
                                        Match Type is an approximate grouping of this one result set — not a
                                        separate lookup.
                                    </p>

                                    <div class="row g-2">
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Min Volume</label>
                                            <input type="number" class="form-control form-control-sm" id="kmt-vol-min" min="0">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Max Volume</label>
                                            <input type="number" class="form-control form-control-sm" id="kmt-vol-max" min="0">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Min KD%</label>
                                            <input type="number" class="form-control form-control-sm" id="kmt-kd-min" min="0" max="100">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Max KD%</label>
                                            <input type="number" class="form-control form-control-sm" id="kmt-kd-max" min="0" max="100">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Min CPC ($)</label>
                                            <input type="number" step="0.01" class="form-control form-control-sm" id="kmt-cpc-min" min="0">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label small mb-1">Max CPC ($)</label>
                                            <input type="number" step="0.01" class="form-control form-control-sm" id="kmt-cpc-max" min="0">
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-1">
                                        <div class="col-6 col-md-3">
                                            <label class="form-label small mb-1">Word Count</label>
                                            <select class="form-select form-select-sm" id="kmt-word-count">
                                                <option value="">Any</option>
                                                <option value="1">1 word</option>
                                                <option value="2">2 words</option>
                                                <option value="3">3 words</option>
                                                <option value="4+">4+ words</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <label class="form-label small mb-1">Include word</label>
                                            <input type="text" class="form-control form-control-sm" id="kmt-include" placeholder="e.g. buy">
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <label class="form-label small mb-1">Exclude word</label>
                                            <input type="text" class="form-control form-control-sm" id="kmt-exclude" placeholder="e.g. free">
                                        </div>
                                        <div class="col-6 col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="kmt-reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Toolbar --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="small text-secondary mb-0"><span id="kmt-count">0</span> keywords</p>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="kmt-export" disabled>
                                    Export Selected to CSV
                                </button>
                            </div>

                            {{-- Table --}}
                            <div class="card">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="kmt-select-all"></th>
                                                <th style="cursor:pointer" data-sort="keyword">Keyword</th>
                                                <th style="cursor:pointer" data-sort="volume">Volume</th>
                                                <th>Trend</th>
                                                <th style="cursor:pointer" data-sort="difficulty">KD%</th>
                                                <th style="cursor:pointer" data-sort="cpc">CPC</th>
                                                <th>Intent</th>
                                                <th>SERP</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="kmt-tbody"></tbody>
                                    </table>
                                </div>
                                <div class="card-body p-3 d-flex justify-content-center gap-2" id="kmt-pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </section>

    @if ($result !== null && $result['error'] === null && count($result['keywords']) > 0)
        @push('scripts')
            <script>
                (function () {
                    const app = document.getElementById('kmt-app');
                    const allKeywords = JSON.parse(app.dataset.keywords);
                    const seed = app.dataset.seed.toLowerCase();
                    const PAGE_SIZE = 25;

                    let currentPage = 1;
                    let sortKey = 'volume';
                    let sortDir = 'desc';
                    let matchType = 'broad';
                    let activeGroup = null;
                    const selected = new Set();

                    // Phase O4 — Match Type here is a CLIENT-SIDE heuristic
                    // over this one already-fetched result set, not a
                    // separate DataForSEO lookup per tab (see this
                    // controller's own class docblock for why: a genuinely
                    // separate endpoint per tab would multiply API cost by
                    // 5x on every single search).
                    const seedWords = seed.split(/\s+/).filter(Boolean);
                    const questionWords = ['who', 'what', 'why', 'how', 'when', 'where', 'is', 'are', 'can', 'does', 'do', 'will', 'should'];

                    function matchesType(kw, type) {
                        const text = kw.keyword.toLowerCase();
                        if (type === 'broad') return true;
                        if (type === 'phrase') return text.includes(seed);
                        if (type === 'exact') return text === seed;
                        if (type === 'related') return !seedWords.some(function (w) { return text.includes(w); });
                        if (type === 'questions') return questionWords.some(function (w) { return text.startsWith(w + ' '); });
                        return true;
                    }

                    // Lightweight topic grouping — the most frequent
                    // non-seed significant word across all results, NOT a
                    // real SERP-similarity clustering algorithm (that needs
                    // per-keyword SERP data for every row, which this page
                    // deliberately doesn't fetch in bulk — see this
                    // controller's own docblock).
                    function buildGroups() {
                        const counts = {};
                        allKeywords.forEach(function (kw) {
                            kw.keyword.toLowerCase().split(/\s+/).forEach(function (word) {
                                if (word.length > 2 && !seedWords.includes(word)) {
                                    counts[word] = (counts[word] || 0) + 1;
                                }
                            });
                        });
                        return Object.entries(counts)
                            .sort(function (a, b) { return b[1] - a[1]; })
                            .slice(0, 12)
                            .map(function (entry) { return entry[0]; });
                    }

                    function renderGroups() {
                        const container = document.getElementById('kmt-groups');
                        const groups = buildGroups();
                        let html = '<button type="button" class="btn btn-sm btn-outline-secondary text-start' +
                            (activeGroup === null ? ' active' : '') + '" data-group="">All keywords</button>';
                        groups.forEach(function (g) {
                            html += '<button type="button" class="btn btn-sm btn-outline-secondary text-start' +
                                (activeGroup === g ? ' active' : '') + '" data-group="' + g + '">' + g + '</button>';
                        });
                        container.innerHTML = html;

                        container.querySelectorAll('button').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                activeGroup = btn.dataset.group || null;
                                currentPage = 1;
                                renderGroups();
                                render();
                            });
                        });
                    }

                    function getFiltered() {
                        const volMin = parseFloat(document.getElementById('kmt-vol-min').value);
                        const volMax = parseFloat(document.getElementById('kmt-vol-max').value);
                        const kdMin = parseFloat(document.getElementById('kmt-kd-min').value);
                        const kdMax = parseFloat(document.getElementById('kmt-kd-max').value);
                        const cpcMin = parseFloat(document.getElementById('kmt-cpc-min').value);
                        const cpcMax = parseFloat(document.getElementById('kmt-cpc-max').value);
                        const wordCount = document.getElementById('kmt-word-count').value;
                        const include = document.getElementById('kmt-include').value.toLowerCase().trim();
                        const exclude = document.getElementById('kmt-exclude').value.toLowerCase().trim();

                        return allKeywords.filter(function (kw) {
                            if (!matchesType(kw, matchType)) return false;
                            if (activeGroup && !kw.keyword.toLowerCase().includes(activeGroup)) return false;
                            if (!isNaN(volMin) && (kw.volume === null || kw.volume < volMin)) return false;
                            if (!isNaN(volMax) && (kw.volume === null || kw.volume > volMax)) return false;
                            if (!isNaN(kdMin) && (kw.difficulty === null || kw.difficulty < kdMin)) return false;
                            if (!isNaN(kdMax) && (kw.difficulty === null || kw.difficulty > kdMax)) return false;
                            if (!isNaN(cpcMin) && (kw.cpc === null || kw.cpc < cpcMin)) return false;
                            if (!isNaN(cpcMax) && (kw.cpc === null || kw.cpc > cpcMax)) return false;
                            if (wordCount === '4+' && kw.word_count < 4) return false;
                            if (wordCount && wordCount !== '4+' && kw.word_count !== parseInt(wordCount, 10)) return false;
                            if (include && !kw.keyword.toLowerCase().includes(include)) return false;
                            if (exclude && kw.keyword.toLowerCase().includes(exclude)) return false;
                            return true;
                        });
                    }

                    function sparkline(trend) {
                        if (!trend || trend.length === 0) return '<span class="text-secondary">—</span>';
                        const max = Math.max.apply(null, trend.map(function (t) { return t.volume || 0; })) || 1;
                        const points = trend.map(function (t, i) {
                            const x = (i / (trend.length - 1)) * 60;
                            const y = 20 - ((t.volume || 0) / max) * 18;
                            return x + ',' + y;
                        }).join(' ');
                        return '<svg width="60" height="20"><polyline points="' + points + '" fill="none" stroke="#9c7a3c" stroke-width="1.5"/></svg>';
                    }

                    function render() {
                        const filtered = getFiltered();

                        filtered.sort(function (a, b) {
                            const av = a[sortKey], bv = b[sortKey];
                            if (av === null) return 1;
                            if (bv === null) return -1;
                            if (typeof av === 'string') {
                                return sortDir === 'asc' ? av.localeCompare(bv) : bv.localeCompare(av);
                            }
                            return sortDir === 'asc' ? av - bv : bv - av;
                        });

                        document.getElementById('kmt-count').textContent = filtered.length;

                        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
                        currentPage = Math.min(currentPage, totalPages);
                        const pageItems = filtered.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

                        const tbody = document.getElementById('kmt-tbody');
                        tbody.innerHTML = pageItems.map(function (kw) {
                            const checked = selected.has(kw.keyword) ? 'checked' : '';
                            const intentBadge = kw.intent
                                ? '<span class="badge bg-secondary-subtle text-secondary-emphasis">' + kw.intent + '</span>'
                                : '<span class="text-secondary">—</span>';
                            const serpBadge = (kw.serp_features && kw.serp_features.length > 0)
                                ? '<span class="badge bg-secondary-subtle text-secondary-emphasis">' + kw.serp_features.length + '</span>'
                                : '<span class="text-secondary">—</span>';

                            return '<tr>' +
                                '<td><input type="checkbox" class="kmt-row-check" data-keyword="' + encodeURIComponent(kw.keyword) + '" ' + checked + '></td>' +
                                '<td>' + kw.keyword + '</td>' +
                                '<td>' + (kw.volume !== null ? kw.volume.toLocaleString() : '—') + '</td>' +
                                '<td>' + sparkline(kw.trend) + '</td>' +
                                '<td>' + (kw.difficulty !== null ? kw.difficulty + '%' : '—') + '</td>' +
                                '<td>' + (kw.cpc !== null ? '$' + kw.cpc.toFixed(2) : '—') + '</td>' +
                                '<td>' + intentBadge + '</td>' +
                                '<td>' + serpBadge + '</td>' +
                                // Phase O5 — calls the SAME shared
                                // modal/JS Keyword Research uses (see
                                // public/js/keyword-lists.js's own
                                // docblock). Built as a plain onclick
                                // string here (not a real DOM listener
                                // added below like the other row
                                // controls) since this whole row is
                                // itself already a template-string join,
                                // and passing structured data through an
                                // inline onclick is simplest given that.
                                '<td><button type="button" class="btn btn-sm btn-outline-secondary" onclick="KeywordLists.open(' +
                                    JSON.stringify({
                                        keyword: kw.keyword,
                                        volume: kw.volume,
                                        difficulty: kw.difficulty,
                                        cpc: kw.cpc,
                                    }).replace(/"/g, '&quot;') +
                                    ')">+</button></td>' +
                                '</tr>';
                        }).join('');

                        tbody.querySelectorAll('.kmt-row-check').forEach(function (cb) {
                            cb.addEventListener('change', function () {
                                const kw = decodeURIComponent(cb.dataset.keyword);
                                if (cb.checked) selected.add(kw); else selected.delete(kw);
                                document.getElementById('kmt-export').disabled = selected.size === 0;
                            });
                        });

                        renderPagination(totalPages);
                    }

                    function renderPagination(totalPages) {
                        const container = document.getElementById('kmt-pagination');
                        let html = '';
                        for (let p = 1; p <= totalPages; p++) {
                            html += '<button type="button" class="btn btn-sm ' +
                                (p === currentPage ? 'btn-primary' : 'btn-outline-secondary') +
                                '" data-page="' + p + '">' + p + '</button>';
                        }
                        container.innerHTML = html;
                        container.querySelectorAll('button').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                currentPage = parseInt(btn.dataset.page, 10);
                                render();
                            });
                        });
                    }

                    document.querySelectorAll('#kmt-match-type button').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            document.querySelectorAll('#kmt-match-type button').forEach(function (b) { b.classList.remove('active'); });
                            btn.classList.add('active');
                            matchType = btn.dataset.match;
                            currentPage = 1;
                            render();
                        });
                    });

                    document.querySelectorAll('th[data-sort]').forEach(function (th) {
                        th.addEventListener('click', function () {
                            const key = th.dataset.sort;
                            if (sortKey === key) {
                                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
                            } else {
                                sortKey = key;
                                sortDir = 'desc';
                            }
                            render();
                        });
                    });

                    ['kmt-vol-min', 'kmt-vol-max', 'kmt-kd-min', 'kmt-kd-max', 'kmt-cpc-min', 'kmt-cpc-max', 'kmt-word-count', 'kmt-include', 'kmt-exclude']
                        .forEach(function (id) {
                            document.getElementById(id).addEventListener('input', function () {
                                currentPage = 1;
                                render();
                            });
                        });

                    document.getElementById('kmt-reset').addEventListener('click', function () {
                        ['kmt-vol-min', 'kmt-vol-max', 'kmt-kd-min', 'kmt-kd-max', 'kmt-cpc-min', 'kmt-cpc-max', 'kmt-include', 'kmt-exclude']
                            .forEach(function (id) { document.getElementById(id).value = ''; });
                        document.getElementById('kmt-word-count').value = '';
                        currentPage = 1;
                        render();
                    });

                    document.getElementById('kmt-select-all').addEventListener('change', function (e) {
                        document.querySelectorAll('.kmt-row-check').forEach(function (cb) {
                            cb.checked = e.target.checked;
                            const kw = decodeURIComponent(cb.dataset.keyword);
                            if (e.target.checked) selected.add(kw); else selected.delete(kw);
                        });
                        document.getElementById('kmt-export').disabled = selected.size === 0;
                    });

                    document.getElementById('kmt-export').addEventListener('click', function () {
                        const rows = allKeywords.filter(function (kw) { return selected.has(kw.keyword); });
                        let csv = 'Keyword,Volume,KD%,CPC,Intent\n';
                        rows.forEach(function (kw) {
                            csv += '"' + kw.keyword.replace(/"/g, '""') + '",' +
                                (kw.volume ?? '') + ',' + (kw.difficulty ?? '') + ',' +
                                (kw.cpc ?? '') + ',' + (kw.intent ?? '') + '\n';
                        });
                        const blob = new Blob([csv], { type: 'text/csv' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'keywords-' + seed.replace(/\s+/g, '-') + '.csv';
                        link.click();
                    });

                    renderGroups();
                    render();
                })();
            </script>
        @endpush
    @endif

    {{-- Phase O5 (Keyword List/Project Management) — the shared "Add
         to List" modal + JS this page's own per-row "+" buttons use. --}}
    @include('keyword-lists._add-to-list-modal')
    @push('scripts')
        <script src="{{ asset('js/keyword-lists.js') }}"></script>
    @endpush
@endsection