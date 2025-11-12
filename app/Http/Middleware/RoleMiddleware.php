<?php

namespace ilhamrhmtkbr\App\Http\Middleware;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\UrlHelper;
use ilhamrhmtkbr\App\Redis\Session;
use ilhamrhmtkbr\App\Repository\UserRepository;

class RoleMiddleware implements Middleware
{
    private UserRepository $userRepository;
    private Session $session;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->session = new Session();
        $this->userRepository = new UserRepository($connection);
    }

    public function before(): void
    {
        $user = $this->session->current();
        $path = UrlHelper::getPathInfo() ?? '';

        $result = $this->userRepository->findUserLoginRole($user);

        if ($result == null) {
            if (str_contains($path, 'hr') || str_contains($path, 'employee')) {
                View::redirect('/candidate/jobs');
            }
        } else {
            if (str_contains($path, '/')) {
                $fragmentPath = explode('/', $path);
                if ($result == 'HRD') {
                    if (!str_contains($result, strtoupper($fragmentPath[1]))) {
                        View::redirect('/user/dashboard');
                    }
                } else {
                    if (str_contains('hr', $fragmentPath[1])) {
                        View::redirect('/user/dashboard');
                    }
                }
            }
        }
    }
}
