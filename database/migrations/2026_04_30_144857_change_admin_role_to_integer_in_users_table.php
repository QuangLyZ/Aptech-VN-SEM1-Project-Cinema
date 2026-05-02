<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop existing default
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role DROP DEFAULT');

        // 2. Change type with USING
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role TYPE INTEGER USING (CASE WHEN admin_role THEN 2 ELSE 0 END)');
        
        // 3. Set new default to 0 (Customer)
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role SET DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role DROP DEFAULT');
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role TYPE BOOLEAN USING (CASE WHEN admin_role = 2 THEN true ELSE false END)');
        DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role SET DEFAULT false');
    }
};
