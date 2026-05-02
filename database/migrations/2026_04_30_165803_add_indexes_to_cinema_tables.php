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
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('status');
        });

        Schema::table('showtimes', function (Blueprint $table) {
            $table->index('movie_id');
            $table->index('room_id');
            $table->index('start_time');
        });

        Schema::table('ticket_details', function (Blueprint $table) {
            $table->index('ticket_id');
            $table->index('seat_id');
        });

        Schema::table('movies', function (Blueprint $table) {
            $table->index('genre');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index('cinema_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['showtime_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropIndex(['movie_id']);
            $table->dropIndex(['room_id']);
            $table->dropIndex(['start_time']);
        });

        Schema::table('ticket_details', function (Blueprint $table) {
            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['seat_id']);
        });

        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['genre']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex(['cinema_id']);
        });
    }
};
