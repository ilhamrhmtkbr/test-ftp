<?php

namespace ilhamrhmtkbr\App\Facades;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Repository\UserRepository;

class Session{
    public $redis = null;
    private $useRedis = false;
    public static string $COOKIE_NAME = "X-IOGM-TALENT-HUB";
    private static $SESSION_PREFIX = "user_session_";

    public function __construct(){
        try {
            // Coba gunakan Redis
            $this->redis = \ilhamrhmtkbr\App\Config\Redis::getInstance();

            // Test koneksi
            $this->redis->ping();
            $this->useRedis = true;
        } catch (\Exception $e) {
            // Redis gagal, gunakan PHP session
            $this->useRedis = false;
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }
    }

    public function get(string $email): ?string
    {
        if ($this->useRedis) {
            return $this->redis->get($email);
        }

        // Fallback ke PHP session
        return $_SESSION[self::$SESSION_PREFIX . $email] ?? null;
    }

    public function create(string $userId): void
    {
        $userRepository = new UserRepository(Database::getConnection());
        $user = new User();
        $user->email = $userId;
        $findUser = $userRepository->findOne($user);
        $user->name = $findUser->name;

        $userData = json_encode($user, JSON_PRETTY_PRINT);

        if ($this->useRedis) {
            $this->redis->set($user->email, $userData);
        } else {
            // Fallback ke PHP session
            $_SESSION[self::$SESSION_PREFIX . $user->email] = $userData;
        }

        setcookie(
            self::$COOKIE_NAME,
            base64_encode($user->email),
            time() + (60 * 60 * 24),
            "/",
            "",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            true
        );
    }

    public function update(string $userId): void
    {
        $user = new User();
        $user->email = $userId;
        $userData = json_encode($user, JSON_PRETTY_PRINT);

        if ($this->useRedis) {
            if ($this->redis->exists($user->email)) {
                $this->redis->set($user->email, $userData);
            }
        } else {
            // Fallback ke PHP session
            $_SESSION[self::$SESSION_PREFIX . $user->email] = $userData;
        }

        setcookie(
            self::$COOKIE_NAME,
            base64_encode($userId),
            time() + (60 * 60 * 24),
            "/",
            "",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            true
        );
    }

    public function destroy(): void
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $userId = base64_decode($sessionId);

        if ($this->useRedis) {
            if ($this->redis->exists($userId)) {
                $this->redis->del($userId);
            }
        } else {
            // Fallback ke PHP session
            unset($_SESSION[self::$SESSION_PREFIX . $userId]);

            // Destroy semua session jika kosong
            if (empty(array_filter($_SESSION, function($key) {
                return strpos($key, self::$SESSION_PREFIX) === 0;
            }, ARRAY_FILTER_USE_KEY))) {
                session_destroy();
            }
        }

        setcookie(
            self::$COOKIE_NAME,
            '',
            time() - 3600,
            "/",
            "",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            true
        );
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? null;

        if (!$sessionId && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $cookieHeader = $headers['Cookie'] ?? '';

            if (preg_match('/' . self::$COOKIE_NAME . '=([^;]+)/', $cookieHeader, $matches)) {
                $sessionId = $matches[1];
            }
        }

        if (!$sessionId) {
            return null;
        }

        $userId = base64_decode($sessionId);

        if (empty($userId)) {
            return null;
        }

        $dataRedis = null;

        if ($this->useRedis) {
            $dataRedis = $this->redis->get($userId);
        } else {
            // Fallback ke PHP session
            $dataRedis = $_SESSION[self::$SESSION_PREFIX . $userId] ?? null;
        }

        if (!$dataRedis) {
            return null;
        }

        $dataUser = json_decode($dataRedis, true);

        if ($dataUser) {
            $user = new User();
            $user->name = $dataUser['name'];
            $user->email = $dataUser['email'];
            return $user;
        }

        return null;
    }
}