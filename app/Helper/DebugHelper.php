<?php

namespace ilhamrhmtkbr\App\Helper;

class DebugHelper
{
    private static string $logFile = __DIR__ . '/../../debug.log';

    public static function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";

        file_put_contents(
            self::$logFile,
            $logMessage,
            FILE_APPEND | LOCK_EX
        );
    }
}