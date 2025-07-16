<?php
require_once BASE_PATH . '/bootstrap.php';

$mongoEnv = getMongoEnv();

try {
    $mongo = new MongoDB\Driver\Manager("mongodb://{$mongoEnv['host']}:{$mongoEnv['port']}");

    $mongo->executeCommand($mongoEnv['db'], new MongoDB\Driver\Command(['ping' => 1]));

    echo "✅ Connected to MongoDB successfully.\n";
} catch (Throwable $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "\n";
}
