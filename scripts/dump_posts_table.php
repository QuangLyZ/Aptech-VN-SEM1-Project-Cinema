<?php
// Dump top posts rows without applying any scopes to inspect DB content
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $rows = \Illuminate\Support\Facades\DB::table('posts')
        ->select(['id','title','status','publish_at','created_at'])
        ->orderByDesc('created_at')
        ->limit(20)
        ->get();

    if ($rows->isEmpty()) {
        echo "No rows found in table 'posts'.\n";
        exit(0);
    }

    foreach ($rows as $r) {
        echo implode(' | ', [
            $r->id ?? '',
            $r->title ?? '',
            $r->status ?? '',
            $r->publish_at ?? 'NULL',
            $r->created_at ?? 'NULL'
        ]) . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
