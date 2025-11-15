<?php

date_default_timezone_set('Asia/Jakarta');

// Load autoloader once at startup
require __DIR__ . '/../vendor/autoload.php';

use ilhamrhmtkbr\App\Exceptions\RedirectException;
use ilhamrhmtkbr\App\Facades\Router;
use ilhamrhmtkbr\App\Facades\View;

/**
 * Request handler for FrankenPHP worker mode
 * This function is called for EACH HTTP request
 */
\ilhamrhmtkbr\App\Helper\EnvHelper::loadEnv();

$handler = function () {
    // Reset router state to prevent route collision between requests
    Router::reset();

    // Clear ALL output buffers before processing request
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // Start fresh output buffer
    ob_start();

    try {
        // Load routes - isolated per request
        require __DIR__ . '/../routes/web.php';

        // Execute router
        Router::run();

    } catch (RedirectException $e) {
        // CRITICAL: RedirectException is NORMAL in worker mode
        // Headers already set in View::redirect(), just finish the request
        // DO NOT throw or redirect again!

    } catch (\Throwable $e) {
        // Log error for debugging
        error_log("Request error: " . $e->getMessage() . "\n" . $e->getTraceAsString());

        // Clear buffer on error
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Try to redirect to home (will throw RedirectException)
        try {
            View::redirect('/');
        } catch (RedirectException $redirectException) {
            // Redirect exception is expected, just continue
        }
    }

    // Flush output for worker mode
    if (function_exists('frankenphp_handle_request')) {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
    }
};

// Check if running in FrankenPHP worker mode
if (function_exists('frankenphp_handle_request')) {
    /**
     * FrankenPHP Worker Mode
     * Process requests in a loop without restarting PHP
     */
    while (frankenphp_handle_request($handler)) {
        // Cleanup after each request
        gc_collect_cycles();

        // Note: Do NOT reset opcache here!
        // FrankenPHP manages opcache automatically
        // Resetting it will kill performance
    }
} else {
    /**
     * Normal Mode (Apache/Nginx/PHP-FPM)
     * Traditional single-request processing
     */
    require __DIR__ . '/../routes/web.php';
    Router::run();
}