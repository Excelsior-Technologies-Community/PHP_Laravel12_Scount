<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchAnalytics extends Model
{
    protected $fillable = [
        'keyword',
        'search_count',
        'last_searched_at',
        'ip_address',
    ];

    protected $casts = [
        'last_searched_at' => 'datetime',
    ];
}