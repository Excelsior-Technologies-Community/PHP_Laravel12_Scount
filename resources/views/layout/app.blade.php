<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel 12 Scout Search Engine')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        header {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
            color: #fff;
            padding: 14px 40px;
            box-shadow: 0 4px 20px -2px rgba(67, 56, 202, 0.25);
        }

        .navbar-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            text-decoration: none;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        nav a {
            color: #e0e7ff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            transition: .25s;
            font-weight: 600;
            font-size: 14px;
        }

        nav a:hover, nav a.active {
            color: #fff;
            background: rgba(255, 255, 255, .18);
        }

        .ctrl-k-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ctrl-k-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .container-custom {
            max-width: 1240px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
            flex: 1;
        }

        /* High-Contrast Search Highlights */
        mark, .mark {
            background-color: #fde047 !important;
            color: #0f172a !important;
            padding: 1px 4px !important;
            border-radius: 4px !important;
            font-weight: 700 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            display: inline-block;
        }

        /* Metric Dashboard Cards Gradients */
        .stat-card-indigo {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
        }
        .stat-card-emerald {
            background: linear-gradient(135deg, #059669 0%, #065f46 100%) !important;
        }
        .stat-card-sky {
            background: linear-gradient(135deg, #0284c7 0%, #075985 100%) !important;
        }
        .stat-card-violet {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%) !important;
        }

        /* Global Federated Search (Ctrl+K) Modal Header & Input */
        .fed-modal-header {
            background: #0f172a !important;
            border-bottom: 1px solid #1e293b !important;
            padding: 14px 20px !important;
        }

        #fedSearchInput {
            color: #ffffff !important;
            background: transparent !important;
            font-size: 1.1rem;
            font-weight: 500;
        }

        #fedSearchInput::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        #fedSearchInput:focus {
            box-shadow: none !important;
            outline: none !important;
            color: #ffffff !important;
            background: transparent !important;
        }

        footer {
            background: #0f172a;
            color: #94a3b8;
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 13px;
        }

        /* Voice Search Pulse Indicator */
        .voice-pulse {
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        @media(max-width:768px) {
            header { padding: 15px 20px; }
            .container-custom { margin: 15px; padding: 15px; }
        }
    </style>
    @yield('styles')
</head>

<body>

    <header>
        <div class="navbar-custom">
            <a href="{{ route('posts.index') }}" class="logo">
                <i class="bi bi-search-heart fs-4"></i> Laravel Scout Pro
            </a>

            <!-- Quick Ctrl+K Search Trigger -->
            <button class="ctrl-k-btn d-none d-md-flex" onclick="openFederatedSearchModal()">
                <i class="bi bi-search"></i>
                <span>Global Explorer</span>
                <kbd class="bg-dark bg-opacity-25 px-1.5 py-0.5 rounded text-white font-mono text-xs">Ctrl + K</kbd>
            </button>

            <nav>
                <a href="{{ route('posts.index') }}" class="{{ request()->routeIs('posts.index') ? 'active' : '' }}">
                    <i class="bi bi-house me-1"></i> Home
                </a>
                <a href="{{ route('posts.create') }}" class="{{ request()->routeIs('posts.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle me-1"></i> Add Post
                </a>
                <a href="{{ route('posts.export') }}">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Export CSV
                </a>
                <a href="{{ route('search.analytics') }}" class="{{ request()->routeIs('search.analytics*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-1"></i> Analytics
                </a>
                <a href="{{ route('scout.studio') }}" class="{{ request()->routeIs('scout.studio*') ? 'active' : '' }}">
                    <i class="bi bi-cpu me-1"></i> Scout Studio
                </a>
            </nav>
        </div>
    </header>

    <div class="container-custom">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- FEATURE 3: GLOBAL FEDERATED SEARCH MODAL (Ctrl + K) -->
    <div class="modal fade" id="federatedSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header fed-modal-header border-0">
                    <div class="input-group input-group-lg border-0 align-items-center">
                        <span class="input-group-text bg-transparent border-0 text-info ps-1 pe-2">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" id="fedSearchInput" class="form-control border-0 ps-1"
                               placeholder="Type to search Posts, Authors, or Topics across entire platform..." autocomplete="off">
                        <button type="button" class="btn-close btn-close-white me-1" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Modal Body with Categorized Results -->
                <div class="modal-body p-0" style="min-height: 280px; max-height: 420px; overflow-y: auto;">
                    <!-- Loading Spinner -->
                    <div id="fedLoading" class="text-center py-4 text-muted" style="display: none;">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        Searching Scout indexes...
                    </div>

                    <!-- Default / Empty State -->
                    <div id="fedEmptyState" class="text-center py-5 text-muted">
                        <i class="bi bi-stars fs-1 mb-2 d-block text-primary text-opacity-50"></i>
                        <p class="mb-0 fw-medium">Global Multi-Model Explorer</p>
                        <small>Search across published posts, author profiles, and categorized topics.</small>
                    </div>

                    <!-- Dynamic Results Container -->
                    <div id="fedResultsContainer" class="p-3 space-y-3" style="display: none;">
                        <!-- Injected via JavaScript -->
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between text-muted small font-mono">
                    <div>
                        <span class="badge bg-secondary me-1">ESC</span> to close &bull;
                        <span class="badge bg-secondary ms-1">ENTER</span> to select
                    </div>
                    <div id="fedResultStats">0 matches found</div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p><strong>Laravel 12 Scout Full-Text Search Platform</strong></p>
        <p>Live Typeahead • Typo Tolerance • Keyword Highlighting • Voice Search • Index Studio</p>
        <p>© {{ date('Y') }} All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Scripts (Ctrl+K Listener & Federated Search) -->
    <script>
        // Keyboard Shortcut: Ctrl+K or Cmd+K
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openFederatedSearchModal();
            }
        });

        function openFederatedSearchModal() {
            const modalEl = document.getElementById('federatedSearchModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => {
                const input = document.getElementById('fedSearchInput');
                input.focus();
                input.select();
            }, 300);
        }

        // Federated Search Input Handler
        let fedDebounceTimer = null;
        const fedInput = document.getElementById('fedSearchInput');
        if (fedInput) {
            fedInput.addEventListener('input', function () {
                clearTimeout(fedDebounceTimer);
                const query = this.value.trim();

                if (query.length === 0) {
                    document.getElementById('fedEmptyState').style.display = 'block';
                    document.getElementById('fedResultsContainer').style.display = 'none';
                    document.getElementById('fedLoading').style.display = 'none';
                    document.getElementById('fedResultStats').innerText = '0 matches found';
                    return;
                }

                document.getElementById('fedEmptyState').style.display = 'none';
                document.getElementById('fedLoading').style.display = 'block';

                fedDebounceTimer = setTimeout(() => {
                    fetch(`/search/federated?query=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('fedLoading').style.display = 'none';
                            renderFederatedResults(data);
                        })
                        .catch(err => {
                            document.getElementById('fedLoading').style.display = 'none';
                        });
                }, 250);
            });
        }

        function renderFederatedResults(data) {
            const container = document.getElementById('fedResultsContainer');
            container.innerHTML = '';
            document.getElementById('fedResultStats').innerText = `${data.total} matches in ${data.duration_ms}ms`;

            if (data.total === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-search fs-2 mb-2 d-block"></i>
                        <p class="mb-0 fw-semibold">No results found for "${data.query || fedInput.value}"</p>
                        <small>Try a different keyword or check for spelling errors.</small>
                    </div>
                `;
                container.style.display = 'block';
                return;
            }

            let html = '';

            // 1. Posts Section
            if (data.posts && data.posts.length > 0) {
                html += `<div class="mb-3"><div class="text-uppercase text-muted fw-bold small mb-2 font-mono"><i class="bi bi-file-earmark-text text-primary me-1"></i> Matching Articles (${data.posts.length})</div><div class="list-group">`;
                data.posts.forEach(p => {
                    html += `
                        <a href="/?search=${encodeURIComponent(p.title.replace(/<[^>]*>?/gm, ''))}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2.5">
                            <div>
                                <div class="fw-semibold text-dark mb-0.5">${p.title}</div>
                                <div class="text-muted small">${p.description}</div>
                            </div>
                            <span class="badge bg-light text-dark border font-mono small">${p.subtitle}</span>
                        </a>
                    `;
                });
                html += `</div></div>`;
            }

            // 2. Users Section
            if (data.users && data.users.length > 0) {
                html += `<div class="mb-3"><div class="text-uppercase text-muted fw-bold small mb-2 font-mono"><i class="bi bi-people text-success me-1"></i> Authors & Users (${data.users.length})</div><div class="list-group">`;
                data.users.forEach(u => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center p-2.5">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle"><i class="bi bi-person"></i></div>
                                <div>
                                    <div class="fw-semibold text-dark">${u.title}</div>
                                    <div class="text-muted small">${u.description}</div>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success font-mono small">Author</span>
                        </div>
                    `;
                });
                html += `</div></div>`;
            }

            // 3. Categories Section
            if (data.categories && data.categories.length > 0) {
                html += `<div><div class="text-uppercase text-muted fw-bold small mb-2 font-mono"><i class="bi bi-tags text-warning me-1"></i> Categories & Topics</div><div class="d-flex flex-wrap gap-2">`;
                data.categories.forEach(c => {
                    html += `
                        <a href="/?category=${encodeURIComponent(c.raw_category)}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-tag me-1"></i> ${c.title}
                        </a>
                    `;
                });
                html += `</div></div>`;
            }

            container.innerHTML = html;
            container.style.display = 'block';
        }
    </script>
    @yield('scripts')
</body>

</html>
