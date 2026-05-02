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
        Schema::table('movies', function (Blueprint $table) {
            $table->index('release_date');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('is_visible');
            $table->index('movie_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('status');
            $table->index('publish_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['release_date']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['is_visible']);
            $table->dropIndex(['movie_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['publish_at']);
        });
    }
};
