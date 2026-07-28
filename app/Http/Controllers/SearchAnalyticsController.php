<?php

namespace App\Http\Controllers;

use App\Models\SearchAnalytics;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchAnalyticsController extends Controller
{
    /**
     * Display Search Analytics Dashboard
     */
    public function index(Request $request)
    {
        $query = SearchAnalytics::query();

        /*
        |--------------------------------------------------------------------------
        | Search by Keyword
        |--------------------------------------------------------------------------
        */

        if ($request->filled('keyword')) {
            $query->where('keyword', 'like', '%' . $request->keyword . '%');
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by Date
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date')) {
            $query->whereDate('last_searched_at', $request->date);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($request->sort) {

            case 'most':
                $query->orderByDesc('search_count');
                break;

            case 'oldest':
                $query->oldest('last_searched_at');
                break;

            default:
                $query->latest('last_searched_at');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalSearches = SearchAnalytics::sum('search_count');

        $totalKeywords = SearchAnalytics::count();

        $todaySearches = SearchAnalytics::whereDate(
            'last_searched_at',
            today()
        )->sum('search_count');

        $uniqueIps = SearchAnalytics::whereNotNull('ip_address')
            ->distinct()
            ->count('ip_address');

        $topKeyword = SearchAnalytics::orderByDesc('search_count')->first();

        /*
        |--------------------------------------------------------------------------
        | Paginated Analytics
        |--------------------------------------------------------------------------
        */

        $analytics = $query
            ->paginate(10)
            ->withQueryString();

        return view('search-analytics.index', compact(
            'analytics',
            'totalSearches',
            'totalKeywords',
            'todaySearches',
            'uniqueIps',
            'topKeyword'
        ));
    }

    /**
     * Export Analytics CSV
     */
    public function export()
    {
        $fileName = 'search_analytics.csv';

        $analytics = SearchAnalytics::latest('last_searched_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($analytics) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                '#',
                'Keyword',
                'Search Count',
                'IP Address',
                'Last Searched',
                'Created At',
            ]);

            $sr = 1;

            foreach ($analytics as $item) {

                fputcsv($file, [
                    $sr++,
                    $item->keyword,
                    $item->search_count,
                    $item->ip_address ?? '-',
                    optional($item->last_searched_at)->format('d M Y h:i A'),
                    optional($item->created_at)->format('d M Y'),
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
    /**
     * Clear All Analytics
     */
    public function clear()
    {
        SearchAnalytics::truncate();

        return redirect()
            ->route('search.analytics')
            ->with('success', 'All search analytics have been cleared successfully.');
    }
}
