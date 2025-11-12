<?php

namespace ilhamrhmtkbr\App\Helper\Components;

class AlertWithCloseHelper
{
    public static function setAlertData(string $type, ?string $message, string $name): ?array
    {
        if (!$message) {
            return null;
        }

        $alert['type'] = $type;
        $alert['message'] = $message;
        $alert['name'] = $name;
        return $alert;
    }
}
