<?php

namespace ilhamrhmtkbr\App\Service;

use ilhamrhmtkbr\App\Models\UserSession;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Repository\SessionRepository;
use ilhamrhmtkbr\App\Repository\UserRepository;

class SessionService
{
    public static string $COOKIE_NAME = "X-IOGM-TALENT-HUB";

    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public function create(string $userId): UserSession
    {
        $session = new UserSession();
        $session->user_id = $userId;

        $this->sessionRepository->create($session);

        setcookie(self::$COOKIE_NAME, base64_encode($session->user_id), time() + (60 * 60 * 24), "/");

        return $session;
    }

    public static function update(string $userId): void
    {
        setcookie(self::$COOKIE_NAME, base64_encode($userId), time() + (60 * 60 * 24), "/");
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteById($sessionId);

        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User
    {
        if (isset($_COOKIE[self::$COOKIE_NAME])) {
            $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';

            $user = new User();
            $user->email = base64_decode($sessionId);

            $session = $this->sessionRepository->findOne($user);
            if ($session == null) {
                return null;
            }

            return $this->userRepository->findOne($user);
        } else {
            return null;
        }
    }
}
