<?php

namespace ilhamrhmtkbr\App\Http\Controller;

use ilhamrhmtkbr\App\Facades\View;

class HomeController
{
    public function index()
    {
        View::render();
    }
}
