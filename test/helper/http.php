<?php

namespace ilhamrhmtkbr\App\Facades {
    function header(string $value): void
    {
//        echo $value;
        \header($value);
    }
}

namespace ilhamrhmtkbr\App\Service {
    function setcookie(string $name, string $value) {}
}

namespace ilhamrhmtkbr\App\Facades {
    function setcookie(string $name, string $value) {}
}

namespace ilhamrhmtkbr\App\Helper {
    function setcookie(string $name, string $value) {}
}
