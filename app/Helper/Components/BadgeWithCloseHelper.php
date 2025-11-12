<?php

namespace ilhamrhmtkbr\App\Helper\Components;

class BadgeWithCloseHelper
{
    public static function setBadgeData(string $type, ?string $message, string $name): ?array
    {
        if (!$message) {
            return null;
        }

        $badge['type'] = $type;
        $badge['message'] = $message;
        $badge['name'] = $name;
        return $badge;
    }
}
