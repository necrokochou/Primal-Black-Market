<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';

try {
    $mongo = new MongoDB\Driver\Manager(
      "mongodb://{$databases['mongoHost']}:{$databases['mongoPort']}"
    );

    $mongo->executeCommand(
        $databases['mongoDB'],
        new MongoDB\Driver\Command(['ping' => 1])
    );

    echo "✅ Connected to MongoDB successfully.\n";
} catch (Throwable $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "\n";
}
