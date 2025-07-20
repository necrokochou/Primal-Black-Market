<?php
session_start();
echo "Current session state:\n";
echo "Session ID: " . session_id() . "\n";
echo "User: " . ($_SESSION['user'] ?? 'Not logged in') . "\n";
echo "All session data:\n";
print_r($_SESSION);

// Clear the session
session_destroy();
session_start();
echo "\nSession cleared.\n";
echo "New session ID: " . session_id() . "\n";
echo "User: " . ($_SESSION['user'] ?? 'Not logged in') . "\n";
?>
