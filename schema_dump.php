<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'');

foreach ($tables as $table) {
    $tableName = $table->table_name;
    echo "Table: $tableName\n";
    $columns = Schema::getColumnListing($tableName);
    foreach ($columns as $column) {
        $type = Schema::getColumnType($tableName, $column);
        echo "  - $column ($type)\n";
    }
    echo "\n";
}
