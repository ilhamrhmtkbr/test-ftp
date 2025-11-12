<?php

namespace ilhamrhmtkbr\App\Facades;

use ilhamrhmtkbr\App\Exceptions\RedirectException;
use ilhamrhmtkbr\App\Helper\FormSessionHelper;
use ilhamrhmtkbr\App\Helper\RoleHelper;
use ilhamrhmtkbr\App\Models\User;

class View
{
    public static function render(
        string $view = 'HomepageApp',
        User $user = null,
        string $pageTitle = 'Talent Hub',
        bool $isNeedSessionFlash = false,
        mixed $data = null
    ): void {
        $roleName = null;
        if ($user) {
            $roleName = RoleHelper::getRoleName($user);
        }

        $session = null;
        if ($isNeedSessionFlash) {
            $session = FormSessionHelper::getSessionData();
        }

        // Always destroy session data after reading
        FormSessionHelper::destroySessionData();

        // Render header for authenticated users (except login/register pages)
        if ($roleName) {
            if (!str_contains($pageTitle, 'Login') && !str_contains($pageTitle, 'Register')) {
                require __DIR__ . '/../../resources/views/Components/header.php';
            }
        }

        // Render main view
        require __DIR__ . '/../../resources/views/Pages/' . $view . '.php';

        // Render footer for authenticated users (except login/register pages)
        if ($roleName) {
            if (!str_contains($pageTitle, 'Login') && !str_contains($pageTitle, 'Register')) {
                require __DIR__ . '/../../resources/views/Components/footer.php';
            }
        }
    }

    /**
     * Redirect to a different location
     * FIXED: Menggunakan exception untuk stop execution di worker mode
     *
     * @throws RedirectException
     */
    public static function redirect(string $location): void
    {
        // CRITICAL: Clean ALL output buffers first
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Set status code and redirect header
        http_response_code(302);
        header('Location: ' . $location, true, 302);

        // FrankenPHP worker mode - throw exception untuk stop execution
        if (function_exists('frankenphp_handle_request')) {
            throw new RedirectException($location);
        }

        // Normal mode (Apache/Nginx) - safe to exit
        exit();
    }

    /**
     * Set session flash data and return self for chaining
     */
    public static function withSessionFlash(array $data): self
    {
        if (isset($data['badge']) && $data['badge'] === null) {
            unset($data['badge']);
        }

        if (isset($data['float']) && $data['float'] === null) {
            unset($data['float']);
        }

        // Remove errors if they only contain badge/float keys
        if (isset($data['errors'])) {
            if (array_key_exists('badge', $data['errors']) || array_key_exists('float', $data['errors'])) {
                unset($data['errors']);
            }
        }

        FormSessionHelper::setSessionData($data);
        return new self();
    }

    /**
     * Render JSON response
     */
    public static function json(mixed $data, int $statusCode = 200): void
    {
        // Clean output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Set headers
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        // Output JSON
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Worker mode - throw exception untuk stop execution
        if (function_exists('frankenphp_handle_request')) {
            throw new RedirectException(''); // Empty location for JSON responses
        }

        exit();
    }
}