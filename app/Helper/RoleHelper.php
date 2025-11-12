<?php

namespace ilhamrhmtkbr\App\Helper;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Repository\UserRepository;

class RoleHelper
{
    private static UserRepository $userRepository;

    public static function initialize(): void
    {
        if (!isset(self::$userRepository)) {
            self::$userRepository = new UserRepository(Database::getConnection());
        }
    }

    public static function getRoleName(User $user): string
    {
        self::initialize(); // Pastikan properti sudah diinisialisasi
        $roleName = 'Candidate';

        $result = self::$userRepository->findUserLoginRole($user);

        if ($result != null) {
            if (strpos($result, 'HRD') !== false) {
                $roleName = 'HR';
            } else {
                $roleName = 'Employee';
            }
        }

        return $roleName;
    }
}
