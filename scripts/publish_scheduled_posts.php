<?php
// Safe one-off: mark posts as visible where publish_at <= now() and status != 'visible'
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $now = \Carbon\Carbon::now();
    $affected = \Illuminate\Support\Facades\DB::table('posts')
        ->where('status', '!=', 'visible')
        ->whereNotNull('publish_at')
        ->where('publish_at', '<=', $now)
        ->update(['status' => 'visible', 'updated_at' => $now]);

    echo "Updated rows: " . $affected . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
