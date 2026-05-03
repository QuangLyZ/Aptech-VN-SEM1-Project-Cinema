<?php
// One-off script to check published posts in local environment.
// Run: php scripts/check_published_posts.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "PHP: " . phpversion() . PHP_EOL;
echo "Environment: " . app()->environment() . PHP_EOL;

try {
    $count = \App\Models\Post::published()->count();
    echo "Published count: " . $count . PHP_EOL;

    $samples = \App\Models\Post::published()->orderByDesc('publish_at')->orderByDesc('created_at')->limit(10)->get(['id','title','status','publish_at','thumbnail'])->toArray();
    echo "Sample rows:\n";
    foreach ($samples as $row) {
        echo implode(' | ', [
            $row['id'] ?? '',
            $row['title'] ?? '',
            $row['status'] ?? '',
            $row['publish_at'] ?? 'NULL',
            $row['thumbnail'] ?? 'NULL',
        ]) . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
