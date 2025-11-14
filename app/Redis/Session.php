<?php

namespace ilhamrhmtkbr\App\Redis;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Config\RedisConf;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Repository\UserRepository;

class Session{
    public $redis;
    public static string $COOKIE_NAME = "X-IOGM-TALENT-HUB";

    public function __construct(){
        $this->redis = RedisConf::getInstance();
    }

    public function get(string $email): ?string
    {
        return $this->redis->get($email);
    }

    public function create(string $userId): void
    {
        $userRepository = new UserRepository(Database::getConnection());
        $user = new User();
        $user->email = $userId;
        $findUser = $userRepository->findOne($user);
        $user->name = $findUser->name;

        $this->redis->set($user->email, json_encode($user, JSON_PRETTY_PRINT));

        setcookie(
            self::$COOKIE_NAME,
            base64_encode($user->email),
            time() + (60 * 60 * 24),
            "/",
            "", // domain (kosong = current domain)
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // secure: true jika HTTPS
            true  // httponly: tidak bisa diakses JavaScript (XSS protection)
        );
    }

    public function update(string $userId): void
    {
        $user = new User();
        $user->email = $userId;

        if ($this->redis->exists($user->email)) {
            $this->redis->set($user->email, json_encode($user, JSON_PRETTY_PRINT));
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
    }

    public function destroy(): void
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $userId = base64_decode($sessionId);

        if ($this->redis->exists($userId)) {
            $this->redis->del($userId);
            setcookie(
                self::$COOKIE_NAME,
                '',
                time() - 3600, // 1 jam yang lalu
                "/",
                "",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                true
            );
        }
    }

    public function current(): ?User
    {
        // Cek dari $_COOKIE dulu (untuk production)
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? null;

        // Kalau gak ada, cek dari HTTP header (untuk testing)
        if (!$sessionId && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $cookieHeader = $headers['Cookie'] ?? '';

            // Parse cookie header
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

        $dataRedis = $this->redis->get($userId);

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