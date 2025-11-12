<?php

namespace ilhamrhmtkbr\App\Helper;

class FormSessionHelper
{
    public static string $COOKIE_NAME = "X-IOGM-TALENT-HUB-SESSION";

    public static string $FILENAME = '';

    public static function getLocationFile(string $filename): string
    {
        return __DIR__ . '/../' . $filename . '.txt';
    }

    public static function setSessionData(array $data): void
    {
        $filename = self::$FILENAME == '' ? uniqid() : self::$FILENAME;
        setcookie(self::$COOKIE_NAME, $filename, time() + (60 * 60 * 24), "/");
        file_put_contents(self::getLocationFile($filename), json_encode($data));
    }

    public static function getSessionData(): ?array
    {
        $filename = isset($_COOKIE[self::$COOKIE_NAME]) ? $_COOKIE[self::$COOKIE_NAME] : '';
        if (!empty($filename) && file_exists(self::getLocationFile($filename))) {
            $sessionData = json_decode(file_get_contents(self::getLocationFile($filename)), true);
            return $sessionData;
        }
        return null;
    }

    public static function destroySessionData(): void
    {
        $filename = $_COOKIE[self::$COOKIE_NAME] ?? null;

        if (!empty($filename) && file_exists(self::getLocationFile($filename))) {
            unlink(self::getLocationFile($filename));
        }
        if ($filename) {
            setcookie(self::$COOKIE_NAME, '', 1, "/");
        }
    }
}
