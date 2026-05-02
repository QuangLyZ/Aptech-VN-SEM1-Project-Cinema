<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_owners', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('email')->unique();
            $blueprint->string('note')->nullable(); // Ghi chú lý do thăng chức/vai trò
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_owners');
    }
};
