@extends('layout.app')

@section('content')

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f1f5f9;
    }

    /* Dashboard */

    .dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: #fff;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 15px 30px rgba(99, 102, 241, .25);
        transition: .35s;
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(99, 102, 241, .35);
    }

    .card h2 {
        font-size: 38px;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .card p {
        font-size: 16px;
        opacity: .95;
        letter-spacing: .5px;
    }

    /* Toolbar */

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        background: #fff;
        padding: 18px;
        border-radius: 14px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .06);
        margin-bottom: 25px;
    }

    .search-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-form input {
        width: 320px;
        padding: 12px 15px;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        transition: .3s;
        font-size: 15px;
    }

    .search-form input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, .15);
    }

    .search-form select {
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #dbe3ec;
        background: #fff;
    }

    .search-form button {
        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 12px 22px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }

    .search-form button:hover {
        background: #312e81;
    }

    .export-btn {
        background: #059669;
        color: #fff;
        text-decoration: none;
        padding: 12px 22px;
        border-radius: 10px;
        font-weight: 600;
        transition: .3s;
    }

    .export-btn:hover {
        background: #047857;
        transform: translateY(-2px);
    }

    /* Wrapper */

    .wrapper {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 25px;
    }

    /* Result */

    .result-count {
        margin-bottom: 20px;
        font-size: 17px;
        font-weight: 600;
        color: #475569;
    }

    /* Post Card */

    .post-card {
        background: #fff;
        border-radius: 15px;
        padding: 22px;
        margin-bottom: 20px;
        border-left: 6px solid #4f46e5;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        transition: .3s;
    }

    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 35px rgba(0, 0, 0, .12);
    }

    .post-card h3 {
        color: #1e293b;
        margin-bottom: 12px;
        font-size: 24px;
    }

    .post-card p {
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 18px;
    }

    .post-meta {
        display: inline-block;
        background: #eef2ff;
        color: #4338ca;
        padding: 8px 14px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
    }

    /* Sidebar */

    .sidebar {
        background: #fff;
        border-radius: 15px;
        padding: 22px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        position: sticky;
        top: 20px;
    }

    .sidebar h3 {
        color: #1e293b;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 10px;
    }

    .sidebar ul {
        list-style: none;
    }

    .sidebar li {
        padding: 14px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .sidebar li:last-child {
        border: none;
    }

    .sidebar strong {
        color: #334155;
    }

    .sidebar small {
        color: #64748b;
    }

    /* No Post */

    .no-post {
        background: #fff;
        border-radius: 15px;
        text-align: center;
        padding: 60px 30px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
    }

    .no-post h2 {
        color: #ef4444;
        margin-bottom: 10px;
    }

    /* Pagination */

    .pagination {
        justify-content: center;
        margin-top: 35px;
    }

    .pagination .page-link {
        border: none;
        margin: 0 4px;
        border-radius: 10px;
        color: #4f46e5;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .pagination .page-item.active .page-link {
        background: #4f46e5;
        color: #fff;
    }

    .pagination .page-link:hover {
        background: #6366f1;
        color: #fff;
    }

    /* Success */

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-left: 5px solid #22c55e;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    mark {
        background: #facc15;
        color: #111827;
        padding: 2px 5px;
        border-radius: 4px;
        font-weight: 700;
    }

    /* Responsive */

    @media(max-width:992px) {

        .wrapper {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: relative;
            top: 0;
        }

        .search-form {
            width: 100%;
        }

        .search-form input {
            width: 100%;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .export-btn {
            text-align: center;
        }

    }

    @media(max-width:576px) {

        .card h2 {
            font-size: 30px;
        }

        .post-card h3 {
            font-size: 20px;
        }

    }
</style>

<div class="dashboard">

    <div class="card">
        <h2>{{ $totalPosts }}</h2>
        <p>Total Posts</p>
    </div>

    <div class="card">
        <h2>{{ $todayPosts }}</h2>
        <p>Today's Posts</p>
    </div>

    <div class="card">
        <h2>{{ $weekPosts }}</h2>
        <p>This Week</p>
    </div>

    <div class="card">
        <h2>{{ $monthPosts }}</h2>
        <p>This Month</p>
    </div>

</div>

<div class="toolbar">

    <form method="GET" action="{{ route('posts.index') }}" class="search-form">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search posts...">

        <select name="sort">

            <option value="latest" {{ $sort=='latest'?'selected':'' }}>
                Latest
            </option>

            <option value="oldest" {{ $sort=='oldest'?'selected':'' }}>
                Oldest
            </option>

            <option value="az" {{ $sort=='az'?'selected':'' }}>
                Title A-Z
            </option>

            <option value="za" {{ $sort=='za'?'selected':'' }}>
                Title Z-A
            </option>

        </select>

        <button type="submit">
            Search
        </button>

    </form>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">

        <a href="{{ route('search.analytics') }}"
            class="export-btn"
            style="background:#4f46e5;">
            📊 Analytics Dashboard
        </a>

        <a href="{{ route('posts.export') }}"
            class="export-btn">
            ⬇ Export CSV
        </a>

    </div>

</div>

<div class="result-count">

    Showing

    <strong>{{ $posts->count() }}</strong>

    of

    <strong>{{ $posts->total() }}</strong>

    Posts

    @if($search)

    <br><br>

    <span style="color:#4f46e5;font-weight:600;">
        🔍 Search:
        "{{ $search }}"
    </span>

    |

    <span style="color:#059669;font-weight:600;">
        ⚡ {{ $searchTime }} sec
    </span>

    @endif

</div>

<div class="wrapper">

    <div>

        @if($posts->count())

        @foreach($posts as $post)

        <div class="post-card">

            <h3>
                @if($search)
                {!! $post->highlight_title !!}
                @else
                {{ $post->title }}
                @endif
            </h3>

            @if($search)

            <div style="margin-bottom:12px;">

                <span style="
        background:#eef2ff;
        color:#4338ca;
        padding:5px 12px;
        border-radius:30px;
        font-size:13px;
        font-weight:600;
    ">
                    ✓ Search Match
                </span>

            </div>

            @endif

            <p>
                @if($search)
                {!! Str::limit($post->highlight_content, 250) !!}
                @else
                {{ Str::limit($post->content, 250) }}
                @endif
            </p>

            <div class="post-meta">

                Created :

                {{ $post->created_at->format('d M Y h:i A') }}

            </div>

        </div>

        @endforeach

        @if ($posts->lastPage() > 1)
        <div class="text-center mt-4">

            @for ($i = 1; $i <= $posts->lastPage(); $i++)

                @if ($i == $posts->currentPage())

                <span style="display:inline-block;padding:8px 14px;margin:3px;background:#4f46e5;color:#fff;border-radius:8px;font-weight:bold;">
                    {{ $i }}
                </span>

                @else

                <a href="{{ $posts->url($i) }}"
                    style="display:inline-block;padding:8px 14px;margin:3px;border:1px solid #4f46e5;border-radius:8px;color:#4f46e5;text-decoration:none;">
                    {{ $i }}
                </a>

                @endif

                @endfor

        </div>
        @endif

        @else

        <div class="no-post">

            <h2>No Posts Found</h2>

            <p>Please try another search.</p>

        </div>

        @endif

    </div>

    <div class="sidebar">

        <h3>🕒 Recent Posts</h3>

        @if($recentPosts->count())

        <ul>

            @foreach($recentPosts as $recent)

            <li>

                <strong>{{ $recent->title }}</strong>

                <br>

                <small>
                    {{ $recent->created_at->format('d M Y') }}
                </small>

            </li>

            @endforeach

        </ul>

        @else

        <p>No recent posts available.</p>

        @endif

        <hr>

        <h3>📊 Quick Summary</h3>

        <p><strong>Total Posts:</strong> {{ $totalPosts }}</p>

        <p><strong>Today:</strong> {{ $todayPosts }}</p>

        <p><strong>This Week:</strong> {{ $weekPosts }}</p>

        <p><strong>This Month:</strong> {{ $monthPosts }}</p>

        @if(request('search'))
        <hr>
        <p>
            <strong>Current Search:</strong><br>
            "{{ request('search') }}"
        </p>
        @endif

    </div>

</div>

@endsection