<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role SET DEFAULT 1');
            DB::statement('UPDATE "Users" SET admin_role = 1 WHERE admin_role = 0');
            DB::statement('ALTER TABLE tickets ALTER COLUMN user_id DROP NOT NULL');
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE Users MODIFY admin_role INTEGER NOT NULL DEFAULT 1');
            DB::statement('UPDATE Users SET admin_role = 1 WHERE admin_role = 0');
            DB::statement('ALTER TABLE tickets MODIFY user_id BIGINT UNSIGNED NULL');
            return;
        }

        DB::statement('UPDATE Users SET admin_role = 1 WHERE admin_role = 0');
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "Users" ALTER COLUMN admin_role SET DEFAULT 0');
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE Users MODIFY admin_role INTEGER NOT NULL DEFAULT 0');
        }
    }
};
