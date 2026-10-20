<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SearchAnalytics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ScoutStudioController extends Controller
{
    /**
     * Display Scout Index Management Studio.
     */
    public function index()
    {
        $driver = config('scout.driver', 'database');
        $prefix = config('scout.prefix', '');
        $queueSync = config('scout.queue', false);

        // Models monitored by Scout
        $models = [
            [
                'name' => 'Post',
                'class' => Post::class,
                'table' => 'posts',
                'count' => Post::count(),
                'searchable_fields' => ['title', 'category', 'content', 'tags'],
            ],
            [
                'name' => 'User',
                'class' => User::class,
                'table' => 'users',
                'count' => User::count(),
                'searchable_fields' => ['name', 'email'],
            ],
        ];

        // Search Analytics Overview
        $totalSearches = SearchAnalytics::sum('search_count') ?: 0;
        $totalKeywords = SearchAnalytics::count();
        $zeroResultCount = SearchAnalytics::where('results_count', 0)->count();

        return view('scout.studio', compact(
            'driver',
            'prefix',
            'queueSync',
            'models',
            'totalSearches',
            'totalKeywords',
            'zeroResultCount'
        ));
    }

    /**
     * Import / Reindex a specific model.
     */
    public function import(Request $request)
    {
        $modelClass = $request->input('model', Post::class);
        $output = new BufferedOutput();

        try {
            Artisan::call('scout:import', ['model' => $modelClass], $output);
            $message = $output->fetch() ?: "Model [{$modelClass}] imported into search index successfully.";

            return back()->with('success', trim($message));
        } catch (\Throwable $e) {
            return back()->with('error', "Import error: " . $e->getMessage());
        }
    }

    /**
     * Flush a model's search index.
     */
    public function flush(Request $request)
    {
        $modelClass = $request->input('model', Post::class);
        $output = new BufferedOutput();

        try {
            Artisan::call('scout:flush', ['model' => $modelClass], $output);
            $message = $output->fetch() ?: "Model [{$modelClass}] flushed from search index.";

            return back()->with('info', trim($message));
        } catch (\Throwable $e) {
            return back()->with('error', "Flush error: " . $e->getMessage());
        }
    }

    /**
     * Re-sync all searchable models.
     */
    public function syncAll()
    {
        try {
            Artisan::call('scout:import', ['model' => Post::class]);
            Artisan::call('scout:import', ['model' => User::class]);

            return back()->with('success', "All searchable models (Post, User) synchronized with Scout index.");
        } catch (\Throwable $e) {
            return back()->with('error', "Sync error: " . $e->getMessage());
        }
    }
}
