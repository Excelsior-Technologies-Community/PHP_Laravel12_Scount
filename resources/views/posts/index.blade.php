@extends('layout.app')

@section('title', 'Laravel Scout - Full-Text Search Engine')

@section('content')

<!-- Statistics Dashboard Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="p-3 stat-card-indigo rounded-4 text-white shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Posts</small>
                <h3 class="fw-bold mb-0 mt-1 font-mono text-white">{{ number_format($totalPosts) }}</h3>
                <small class="text-white-50">Indexed in Scout</small>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                <i class="bi bi-file-earmark-text fs-4 text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="p-3 stat-card-emerald rounded-4 text-white shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Added Today</small>
                <h3 class="fw-bold mb-0 mt-1 font-mono text-white">{{ number_format($todayPosts) }}</h3>
                <small class="text-white-50">Fresh Content</small>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                <i class="bi bi-calendar-check fs-4 text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="p-3 stat-card-sky rounded-4 text-white shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">This Week</small>
                <h3 class="fw-bold mb-0 mt-1 font-mono text-white">{{ number_format($weekPosts) }}</h3>
                <small class="text-white-50">Weekly Velocity</small>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                <i class="bi bi-graph-up-arrow fs-4 text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="p-3 stat-card-violet rounded-4 text-white shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">This Month</small>
                <h3 class="fw-bold mb-0 mt-1 font-mono text-white">{{ number_format($monthPosts) }}</h3>
                <small class="text-white-50">Monthly Volume</small>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                <i class="bi bi-calendar3 fs-4 text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Search & Toolbar Section -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 position-relative">
    <form action="{{ route('posts.index') }}" method="GET" id="mainSearchForm">
        <div class="row g-2 align-items-center">

            <!-- Search Input with Voice Button (Feature 8) & Instant Autocomplete (Feature 1) -->
            <div class="col-12 col-lg-6 position-relative">
                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0 text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="liveSearchInput" class="form-control border-0 ps-2"
                           placeholder="Search posts by title, body, keywords..." value="{{ $search }}" autocomplete="off"
                           style="color: #0f172a; font-weight: 500;">

                    <!-- Voice Search Button (Feature 8) -->
                    <button type="button" class="btn bg-white border-0 text-secondary pe-2" id="voiceSearchBtn" title="Search with Voice (Speech to Text)">
                        <i class="bi bi-mic-fill fs-5" id="voiceIcon"></i>
                    </button>

                    <!-- Quick Clear Button -->
                    @if(!empty($search))
                        <a href="{{ route('posts.index') }}" class="btn bg-white border-0 text-muted pe-3" title="Clear Search">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    @endif
                </div>

                <!-- Instant Autocomplete Dropdown Panel (Feature 1) -->
                <div id="autocompleteDropdown" class="position-absolute w-100 bg-white border rounded-4 shadow-lg p-3 z-3 mt-1"
                     style="display: none; top: 100%; left: 0; max-height: 400px; overflow-y: auto;">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <small class="fw-bold text-uppercase text-muted font-mono" style="font-size: 0.75rem;">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Instant Suggestions
                        </small>
                        <small class="text-muted font-mono" id="liveSearchTimer">0 ms</small>
                    </div>

                    <!-- Autocomplete Suggestions List -->
                    <div id="autoSuggestions" class="d-flex flex-wrap gap-1 mb-2"></div>

                    <!-- Live Results List -->
                    <div id="autoResultsList" class="list-group list-group-flush"></div>
                </div>
            </div>

            <!-- Category Filter Dropdown -->
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="category" class="form-select form-select-lg shadow-sm border text-secondary" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Dropdown -->
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="sort" class="form-select form-select-lg shadow-sm border text-secondary" onchange="this.form.submit()">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="az" {{ $sort === 'az' ? 'selected' : '' }}>Title (A - Z)</option>
                    <option value="za" {{ $sort === 'za' ? 'selected' : '' }}>Title (Z - A)</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="col-12 col-lg-2">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-filter"></i> Search
                </button>
            </div>

        </div>
    </form>

    <!-- Voice Recording Pulse Banner (Feature 8) -->
    <div id="voiceRecordingBanner" class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 mt-3 d-flex align-items-center justify-content-between" style="display: none !important;">
        <div class="d-flex align-items-center gap-2 text-danger fw-semibold">
            <span class="rounded-circle bg-danger voice-pulse d-inline-block" style="width: 12px; height: 12px;"></span>
            <span>Listening to voice... Speak now into your microphone.</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="stopVoiceRecognition()">Cancel</button>
    </div>

    <!-- Category Facet Filter Pills -->
    <div class="d-flex flex-wrap align-items-center gap-1.5 mt-3 pt-3 border-top">
        <span class="text-muted small fw-semibold me-2 font-mono">Topics:</span>
        <a href="{{ route('posts.index', ['search' => $search, 'category' => 'all', 'sort' => $sort]) }}"
           class="badge rounded-pill text-decoration-none px-3 py-2 {{ (empty($category) || $category === 'all') ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
            All Topics
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('posts.index', ['search' => $search, 'category' => $cat, 'sort' => $sort]) }}"
               class="badge rounded-pill text-decoration-none px-3 py-2 {{ $category === $cat ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>
</div>

<!-- FEATURE 4: "DID YOU MEAN?" TYPO TOLERANCE BANNER -->
@if(!empty($didYouMean))
    <div class="alert alert-warning border-warning border-opacity-50 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center gap-3">
        <div class="bg-warning text-dark p-2 rounded-circle fs-4">
            💡
        </div>
        <div>
            <span class="text-dark fw-bold">Showing 0 exact matches for "<em>{{ $search }}</em>".</span>
            <div class="mt-0.5">
                Did you mean:
                <a href="{{ route('posts.index', ['search' => $didYouMean, 'category' => $category, 'sort' => $sort]) }}"
                   class="fw-bold text-decoration-underline text-primary fs-6">
                    {{ $didYouMean }}
                </a>?
                <small class="text-muted ms-2">(Calculated using Levenshtein fuzzy phonetic matching)</small>
            </div>
        </div>
    </div>
@endif

<!-- Search Results & Active Query Summary Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        @if(!empty($search))
            <h5 class="fw-bold text-dark mb-0">
                Search Results for "<span class="text-primary">{{ $search }}</span>"
                <span class="badge bg-secondary font-mono small ms-2">{{ $posts->total() }} found</span>
            </h5>
            <small class="text-muted font-mono">Scout query processed in {{ $searchTime }} seconds</small>
        @else
            <h5 class="fw-bold text-dark mb-0">Published Articles & Tutorials</h5>
            <small class="text-muted">Showing {{ $posts->total() }} published entries</small>
        @endif
    </div>

    <!-- Trending Searches Tags Bar -->
    @if($trendingSearches->isNotEmpty())
        <div class="d-flex flex-wrap align-items-center gap-1">
            <small class="text-muted font-mono me-1"><i class="bi bi-fire text-danger"></i> Trending:</small>
            @foreach($trendingSearches as $trend)
                <a href="{{ route('posts.index', ['search' => $trend->keyword]) }}"
                   class="badge bg-white text-dark border text-decoration-none rounded-pill px-2.5 py-1.5 hover-shadow font-mono"
                   title="{{ $trend->search_count }} searches">
                    {{ $trend->keyword }}
                </a>
            @endforeach
        </div>
    @endif
</div>

<!-- Posts Cards Grid (Feature 2: Contextual Keyword Highlighting) -->
@if($posts->isEmpty())
    <div class="text-center py-5 bg-white rounded-4 border p-5">
        <i class="bi bi-search text-muted fs-1 mb-2 d-block"></i>
        <h5 class="fw-bold text-dark">No matching articles found</h5>
        <p class="text-muted small">Try searching for broader terms, check your spelling, or explore the trending topics above.</p>
        <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Create This Post Now
        </a>
    </div>
@else
    <div class="row g-4 mb-4">
        @foreach($posts as $post)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border rounded-4 p-4 shadow-sm hover-elevate transition-all bg-white" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono small px-2 py-1 rounded-pill" style="background-color: #eef2ff; color: #4338ca;">
                            <i class="bi bi-tag-fill me-1"></i> {{ $post->category ?? 'General' }}
                        </span>
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                            {{ $post->created_at->format('M d, Y') }}
                        </small>
                    </div>

                    <!-- Highlighted Title (Feature 2) -->
                    <h5 class="fw-bold text-dark mb-2 lh-snug">
                        {!! $post->highlight_title !!}
                    </h5>

                    <!-- Highlighted Contextual Excerpt (Feature 2) -->
                    <p class="text-secondary small mb-3 flex-grow-1 lh-base">
                        {!! $post->highlight_content !!}
                    </p>

                    @if(!empty($post->tags))
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach(explode(',', $post->tags) as $tag)
                                <span class="badge bg-light text-muted border font-mono" style="font-size: 0.7rem;">#{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-clock me-1"></i> 3 min read</span>
                        <span class="font-mono text-muted">ID: #{{ $post->id }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
@endif

@endsection

@section('scripts')
<script>
    // -------------------------------------------------------------
    // FEATURE 1: INSTANT LIVE SEARCH & TYPEAHEAD AUTOCOMPLETE
    // -------------------------------------------------------------
    const liveSearchInput = document.getElementById('liveSearchInput');
    const dropdown = document.getElementById('autocompleteDropdown');
    const autoResultsList = document.getElementById('autoResultsList');
    const autoSuggestions = document.getElementById('autoSuggestions');
    const timerLabel = document.getElementById('liveSearchTimer');
    let liveDebounce = null;

    if (liveSearchInput) {
        liveSearchInput.addEventListener('input', function () {
            clearTimeout(liveDebounce);
            const query = this.value.trim();

            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }

            liveDebounce = setTimeout(() => {
                fetch(`/posts/live-search?query=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) return;
                        renderAutocomplete(data);
                    })
                    .catch(() => {});
            }, 200);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && e.target !== liveSearchInput) {
                dropdown.style.display = 'none';
            }
        });
    }

    function renderAutocomplete(data) {
        autoSuggestions.innerHTML = '';
        autoResultsList.innerHTML = '';
        timerLabel.innerText = `${data.duration_ms} ms (${data.count} hits)`;

        // Suggestions pills
        if (data.suggestions && data.suggestions.length > 0) {
            data.suggestions.forEach(s => {
                const badge = document.createElement('a');
                badge.href = `/?search=${encodeURIComponent(s)}`;
                badge.className = 'badge bg-light text-primary border text-decoration-none px-2 py-1 font-mono';
                badge.innerHTML = `<i class="bi bi-arrow-up-right me-1"></i> ${s}`;
                autoSuggestions.appendChild(badge);
            });
        }

        // Typo correction in dropdown
        if (data.did_you_mean) {
            const dymAlert = document.createElement('div');
            dymAlert.className = 'p-2 mb-2 bg-warning bg-opacity-10 border border-warning rounded-3 small';
            dymAlert.innerHTML = `Did you mean: <a href="/?search=${encodeURIComponent(data.did_you_mean)}" class="fw-bold text-primary">${data.did_you_mean}</a>?`;
            autoResultsList.appendChild(dymAlert);
        }

        // Live Results
        if (data.results && data.results.length > 0) {
            data.results.forEach(r => {
                const item = document.createElement('a');
                item.href = `/?search=${encodeURIComponent(r.raw_title)}`;
                item.className = 'list-group-item list-group-item-action px-2 py-2 border-0 rounded-3 mb-1';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-dark">${r.title}</span>
                        <span class="badge bg-light text-muted border font-mono small">${r.category}</span>
                    </div>
                    <small class="text-secondary d-block text-truncate">${r.snippet}</small>
                `;
                autoResultsList.appendChild(item);
            });
        } else if (!data.did_you_mean) {
            autoResultsList.innerHTML = '<div class="text-muted small py-2 text-center">No instant matches found.</div>';
        }

        dropdown.style.display = 'block';
    }

    // -------------------------------------------------------------
    // FEATURE 8: VOICE SEARCH INPUT (SPEECH-TO-TEXT VIA WEB SPEECH API)
    // -------------------------------------------------------------
    const voiceBtn = document.getElementById('voiceSearchBtn');
    const voiceIcon = document.getElementById('voiceIcon');
    const voiceBanner = document.getElementById('voiceRecordingBanner');
    let recognition = null;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        recognition.onstart = function () {
            voiceIcon.className = 'bi bi-mic-fill text-danger fs-5';
            voiceBanner.style.setProperty('display', 'flex', 'important');
        };

        recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript;
            liveSearchInput.value = transcript;
            voiceBanner.style.setProperty('display', 'none', 'important');
            voiceIcon.className = 'bi bi-mic-fill fs-5 text-secondary';
            // Trigger instant submit
            document.getElementById('mainSearchForm').submit();
        };

        recognition.onerror = function (event) {
            console.error('Speech recognition error:', event.error);
            stopVoiceRecognition();
        };

        recognition.onend = function () {
            stopVoiceRecognition();
        };

        if (voiceBtn) {
            voiceBtn.addEventListener('click', function () {
                try {
                    recognition.start();
                } catch (e) {
                    recognition.stop();
                }
            });
        }
    } else {
        if (voiceBtn) {
            voiceBtn.title = 'Speech Recognition not supported in this browser';
            voiceBtn.disabled = true;
            voiceBtn.style.opacity = '0.5';
        }
    }

    function stopVoiceRecognition() {
        if (recognition) {
            try { recognition.stop(); } catch (e) {}
        }
        voiceBanner.style.setProperty('display', 'none', 'important');
        voiceIcon.className = 'bi bi-mic-fill fs-5 text-secondary';
    }
</script>
@endsection
