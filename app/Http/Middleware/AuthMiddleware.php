<?php

namespace ilhamrhmtkbr\App\Http\Middleware;

use ilhamrhmtkbr\App\Facades\Session;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\UrlHelper;

class AuthMiddleware implements Middleware
{

    private Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function before(): void
    {
        $user = $this->session->current();
        $path = UrlHelper::getPathInfo() ?? '';

        if (str_contains($path, "/user/login") || str_contains($path, "/user/register") || str_contains($path, "/user/forgot-password")) {
            if ($user != null) {
                View::redirect('/user/dashboard');
            }
        } else {
            if ($user == null) {
                View::redirect('/user/login');
            }
        }
    }
}
