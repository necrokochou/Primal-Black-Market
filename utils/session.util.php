<?php
/**
 * Session management utility
 * Ensures clean session handling without headers already sent errors
 */

/**
 * Start session safely with output buffering
 */
function startSessionSafely(): void {
    // Start output buffering if not already started
    if (ob_get_level() == 0) {
        ob_start();
    }
    
    // Start session only if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * End session and clean output for redirects
 */
function endSessionForRedirect(): void {
    // Clean any output buffer before redirect
    if (ob_get_level()) {
        ob_end_clean();
    }
}

/**
 * Check if user is logged in and redirect if not
 */
function requireLogin(string $redirectTo = '/pages/login/index.php'): void {
    startSessionSafely();
    
    if (!isset($_SESSION['user'])) {
        endSessionForRedirect();
        header("Location: $redirectTo");
        exit;
    }
}
