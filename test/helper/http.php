<?php

namespace ilhamrhmtkbr\App\Facades {
    function header(string $value): void
    {
        echo $value;
    }
}

namespace ilhamrhmtkbr\App\Service {
    function setCookie(string $name, string $value) {}
}

namespace ilhamrhmtkbr\App\Redis {
    function setCookie(string $name, string $value) {}
}

namespace ilhamrhmtkbr\App\Helper {
    function setCookie(string $name, string $value) {}
}
