<?php
session_start();

echo "Current session data:\n";
print_r($_SESSION);

// Clear all session data
session_destroy();
session_start();

echo "\nSession cleared successfully!\n";
echo "New session data:\n";
print_r($_SESSION);
?>
