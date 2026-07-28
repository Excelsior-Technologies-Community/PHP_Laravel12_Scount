@extends('layout.app')

@section('content')

<style>
    body {
        background: #f1f5f9;
        font-family: 'Segoe UI', sans-serif;
    }

    .dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: #fff;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 12px 25px rgba(79, 70, 229, .25);
        transition: .3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h2 {
        font-size: 34px;
        margin-bottom: 8px;
    }

    .card p {
        margin: 0;
        opacity: .95;
    }

    .toolbar {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
        margin-bottom: 25px;
    }

    .filter-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-form input,
    .filter-form select {

        padding: 11px;
        border: 1px solid #dbe3ec;
        border-radius: 8px;
        min-width: 170px;

    }

    .filter-form button {

        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        cursor: pointer;

    }

    .btn {

        text-decoration: none;
        color: #fff;
        padding: 11px 20px;
        border-radius: 8px;
        font-weight: 600;

    }

    .export {
        background: #059669;
    }

    .back {
        background: #2563eb;
    }

    .clear {
        background: #dc2626;
        border: none;
        cursor: pointer;
    }

    .actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .table-wrapper {

        background: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .06);

    }

    .success {

        background: #dcfce7;
        color: #166534;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;

    }

    @media(max-width:768px) {

        .toolbar {

            flex-direction: column;
            align-items: stretch;

        }

        .filter-form {

            width: 100%;

        }

        .filter-form input,
        .filter-form select,
        .filter-form button {

            width: 100%;

        }

        .actions {

            width: 100%;
            flex-direction: column;

        }

        .btn,
        .clear {

            width: 100%;
            text-align: center;

        }

    }
</style>


<div class="dashboard">

    <div class="card">
        <h2>{{ $totalSearches }}</h2>
        <p>Total Searches</p>
    </div>

    <div class="card">
        <h2>{{ $totalKeywords }}</h2>
        <p>Unique Keywords</p>
    </div>

    <div class="card">
        <h2>{{ $todaySearches }}</h2>
        <p>Today's Searches</p>
    </div>

    <div class="card">
        <h2>
            {{ $topKeyword ? $topKeyword->search_count : 0 }}
        </h2>

        <p>

            {{ $topKeyword ? $topKeyword->keyword : 'No Data' }}

        </p>

    </div>

</div>

<div class="toolbar">

    <form method="GET"
        action="{{ route('search.analytics') }}"
        class="filter-form">

        <input
            type="text"
            name="keyword"
            placeholder="Search keyword..."
            value="{{ request('keyword') }}">

        <input
            type="date"
            name="date"
            value="{{ request('date') }}">

        <select name="sort">

            <option value="latest"
                {{ request('sort')=='latest' ? 'selected':'' }}>
                Latest
            </option>

            <option value="most"
                {{ request('sort')=='most' ? 'selected':'' }}>
                Most Searched
            </option>

            <option value="oldest"
                {{ request('sort')=='oldest' ? 'selected':'' }}>
                Oldest
            </option>

        </select>

        <button type="submit">
            Filter
        </button>

    </form>

    <div class="actions">

        <a href="{{ route('posts.index') }}"
            class="btn back">
            ← Back
        </a>

        <a href="{{ route('search.analytics.export') }}"
            class="btn export">
            📥 Export CSV
        </a>

        <form
            action="{{ route('search.analytics.clear') }}"
            method="POST"
            onsubmit="return confirm('Clear all analytics?')">

            @csrf
            @method('DELETE')

            <button class="btn clear">

                🗑 Clear Analytics

            </button>

        </form>

    </div>

</div>

<div class="table-wrapper">

    <table class="table table-bordered table-hover align-middle mb-0">

        <thead class="table-dark">

            <tr>
                <th>#</th>
                <th>Keyword</th>
                <th>Total Searches</th>
                <th>IP Address</th>
                <th>Last Searched</th>
                <th>Created</th>
            </tr>

        </thead>

        <tbody>

            @forelse($analytics as $item)

            <tr>

                <td>{{ $analytics->firstItem() + $loop->index }}</td>

                <td>
                    <strong>{{ $item->keyword }}</strong>
                </td>

                <td>

                    <span class="badge bg-success">

                        {{ $item->search_count }}

                    </span>

                </td>

                <td>

                    {{ $item->ip_address ?? '-' }}

                </td>

                <td>
                    {{ $item->last_searched_at ? $item->last_searched_at->format('d M Y h:i A') : '-' }}
                </td>

                <td>

                    {{ $item->created_at->format('d M Y') }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6" class="text-center py-4">

                    No search analytics found.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

    <div class="mt-4">

        {{ $analytics->withQueryString()->links() }}

    </div>

</div>

@endsection