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
        Schema::create('search_analytics', function (Blueprint $table) {

            $table->id();

            $table->string('keyword')->unique();

            $table->unsignedInteger('search_count')->default(1);

            $table->timestamp('last_searched_at')->nullable();

            $table->string('ip_address')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_analytics');
    }
};