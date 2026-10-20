<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category', 60)->default('General')->after('title');
            $table->string('tags')->nullable()->after('content');
        });

        Schema::table('search_analytics', function (Blueprint $table) {
            $table->unsignedInteger('results_count')->default(0)->after('search_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['category', 'tags']);
        });

        Schema::table('search_analytics', function (Blueprint $table) {
            $table->dropColumn('results_count');
        });
    }
};
