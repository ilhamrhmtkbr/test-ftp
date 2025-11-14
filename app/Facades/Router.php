<?php

namespace ilhamrhmtkbr\App\Facades;

use ilhamrhmtkbr\App\Exceptions\RedirectException;
use ilhamrhmtkbr\App\Helper\DebugHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

class Router
{
    private static array $routes = [];
    private static bool $routesLoaded = false;

    public static function add(
        string $method,
        string $path,
        string $controller,
        string $function,
        array $middleware = []
    ): void {
        // Add validation
        if (empty($path)) {
            throw new \InvalidArgumentException('Route path cannot be empty. Use "/" for root path.');
        }

        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middleware
        ];
    }

    /**
     * Reset routes - CRITICAL for FrankenPHP worker mode
     */
    public static function reset(): void
    {
        self::$routes = [];
        self::$routesLoaded = false;
    }

    public static function run(): void
    {
        try {
            $path = UrlHelper::getPathInfo();
            $method = $_SERVER['REQUEST_METHOD'];

            DebugHelper::log("========== ROUTER DEBUG ==========");
            DebugHelper::log("Request Path: " . $path);
            DebugHelper::log("Request Method: " . $method);
            DebugHelper::log("Total Routes: " . count(self::$routes));

            foreach (self::$routes as $route) {
                // Skip invalid routes - CHECK ALL REQUIRED KEYS
                if (empty($route['path']) || !isset($route['method']) || !isset($route['controller']) || !isset($route['function'])) {
                    continue;
                }

                $pattern = '#^' . $route['path'] . '$#';

                DebugHelper::log("Checking route: {$route['method']} {$route['path']}");
                DebugHelper::log("Pattern: " . $pattern);
                DebugHelper::log("Match? " . (preg_match($pattern, $path) ? 'YES' : 'NO'));

                // Handle DELETE method via POST override
                if (isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
                    if (preg_match($pattern, $path, $variables) && $route['method'] === 'DELETE') {
                        self::executeRoute($route, $variables);
                        return; // Route executed successfully
                    }
                }
                // Normal method matching
                else if (preg_match($pattern, $path, $variables) && $method === $route['method']) {
                    self::executeRoute($route, $variables);
                    return; // Route executed successfully
                }
            }

            // Route not found
            self::handleNotFound($path);

        } catch (RedirectException $e) {
            // CRITICAL: Catch redirect exception dari View::redirect()
            // Ini normal behavior di worker mode, jangan throw lagi!
            // Header sudah di-set di View::redirect(), cukup return
            return;

        } catch (\Throwable $e) {
            // Log error untuk debugging
            DebugHelper::log("Router error: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            // Clean output buffer
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Fallback ke home
            try {
                View::redirect('/');
            } catch (RedirectException $redirectException) {
                // Catch redirect exception dari fallback
                return;
            }
        }
    }

    /**
     * Execute route with middleware
     */
    private static function executeRoute(array $route, array $variables): void
    {
        // Run middleware
        foreach ($route['middleware'] as $middleware) {
            $instance = new $middleware;
            $instance->before();
        }

        // Execute controller
        $function = $route['function'];
        $controller = new $route['controller'];

        // Create request object
        $request = new Request();

        // Prepare parameters: [Request, ...route_params]
        array_shift($variables);
        array_unshift($variables, $request);

        // Call controller method
        // Jika controller call View::redirect(), akan throw RedirectException
        call_user_func_array([$controller, $function], $variables);
    }

    /**
     * Handle 404 with smart redirect to similar URL
     */
    private static function handleNotFound(string $path): void
    {
        $similarUrl = null;
        $minDistance = PHP_INT_MAX;

        foreach (self::$routes as $route) {
            if (!isset($route['method']) || !isset($route['path']) || empty($route['path'])) {
                continue;
            }

            // Only suggest GET routes
            if ($route['method'] === 'GET') {
                $distance = levenshtein($path, $route['path']);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $similarUrl = $route['path'];
                }
            }
        }

        // Redirect to similar URL or home
        // This will throw RedirectException in worker mode
        View::redirect($similarUrl ?? '/');
    }

    /**
     * Get all registered routes (useful for debugging)
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }
}