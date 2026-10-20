<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users for Global Federated Search
        $users = [
            ['name' => 'Taylor Otwell', 'email' => 'taylor@laravel.com'],
            ['name' => 'Jeffrey Way', 'email' => 'jeffrey@laracasts.com'],
            ['name' => 'Adam Wathan', 'email' => 'adam@tailwindcss.com'],
            ['name' => 'Caleb Porzio', 'email' => 'caleb@livewire.com'],
            ['name' => 'Nuno Maduro', 'email' => 'nuno@pestphp.com'],
            ['name' => 'Sarah Connor', 'email' => 'sarah@skynet.dev'],
            ['name' => 'Alexandre Da Silva', 'email' => 'alex@vuejs.org'],
            ['name' => 'Elena Rostova', 'email' => 'elena@docker.io'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                ]
            );
        }

        // 2. Seed Rich Posts for Scout Search & Highlighting
        $posts = [
            [
                'title' => 'Mastering Laravel 12 Scout with Full-Text Search',
                'category' => 'Laravel',
                'tags' => 'laravel, scout, database, fulltext',
                'content' => 'Laravel Scout provides a simple, driver-based solution for adding full-text search to your Eloquent models. Using model observers, Scout will automatically keep your search indexes in sync with your Eloquent records. Learn how to configure database, Algolia, and Meilisearch drivers effortlessly.',
            ],
            [
                'title' => 'Building Fast Real-Time Autocomplete with Debounce',
                'category' => 'JavaScript',
                'tags' => 'javascript, autocomplete, live-search, debounce',
                'content' => 'Typeahead autocomplete significantly improves user search experience. By using a 250ms debounced event listener on input fields, we can prevent spamming API endpoints while providing snappy search suggestions, category tags, and instant keyboard navigation.',
            ],
            [
                'title' => 'Dockerizing Laravel Applications with Alpine & PHP 8.4',
                'category' => 'DevOps',
                'tags' => 'docker, devops, php, containers',
                'content' => 'Learn how to construct lightweight multi-stage Docker containers for Laravel 12 running on PHP 8.4 Alpine Linux. Optimize production builds with OPcache, Redis caching, supervisor background workers, and Nginx reverse proxy configurations.',
            ],
            [
                'title' => 'Levenshtein Distance & Phonetic Typo Tolerance in Search',
                'category' => 'PHP',
                'tags' => 'php, algorithm, levenshtein, typo-tolerance',
                'content' => 'Users frequently misspell search queries like "laravl" or "dockr". By analyzing token vocabulary through Levenshtein distance metrics and Soundex phonetic indexing, search engines can intelligently offer "Did You Mean?" suggestions that recover zero-result queries.',
            ],
            [
                'title' => 'PostgreSQL vs MySQL Full-Text Search Performance Benchmarks',
                'category' => 'Database',
                'tags' => 'database, mysql, postgresql, benchmark',
                'content' => 'When scaling databases to millions of records, standard LIKE queries suffer severe performance degradation. This deep dive compares PostgreSQL tsvector GIN indices against MySQL InnoDB MATCH AGAINST full-text indexes in Laravel Scout.',
            ],
            [
                'title' => 'Modern Tailwind CSS & Bootstrap 5 Dark Mode Architectures',
                'category' => 'Frontend',
                'tags' => 'css, bootstrap, tailwind, ui-ux',
                'content' => 'Creating accessible and aesthetic dark/light modes requires thoughtful CSS custom property hierarchies and theme toggle persistence via LocalStorage. Explore modern card elevations, glowing search badges, and smooth transitions.',
            ],
            [
                'title' => 'Web Speech API: Implementing Voice Search in Browsers',
                'category' => 'JavaScript',
                'tags' => 'javascript, speech-api, voice-search, html5',
                'content' => 'The HTML5 Web Speech API enables native speech-to-text transcription directly within desktop and mobile browsers without third-party cloud APIs. We explore SpeechRecognition lifecycle events, pulsing recording UI states, and auto-query dispatch.',
            ],
            [
                'title' => 'Redis Queue Architecture for Heavy Laravel Jobs',
                'category' => 'DevOps',
                'tags' => 'redis, queues, horizon, worker',
                'content' => 'High-throughput web applications rely on background Redis workers to handle asynchronous processing, batch email dispatch, AI translation queues, and search index re-building without blocking HTTP requests.',
            ],
        ];

        foreach ($posts as $postData) {
            Post::firstOrCreate(
                ['title' => $postData['title']],
                $postData
            );
        }
    }
}
