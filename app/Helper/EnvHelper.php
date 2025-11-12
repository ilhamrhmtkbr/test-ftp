<?php

namespace ilhamrhmtkbr\App\Helper;

use Exception;

class EnvHelper
{
    public static function loadEnv(): void
    {
        $filePath = __DIR__ . '../../.env';
        if (!file_exists($filePath)) {
            throw new Exception(".env file not found : " . $filePath);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Lewati baris komentar
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Pisahkan key dan value
            [$key, $value] = explode('=', $line, 2);

            // Bersihkan key dan value
            $key = trim($key);
            $value = trim($value);

            // Hapus tanda kutip di sekitar value (jika ada)
            $value = trim($value, '"\'');

            // Simpan ke lingkungan
            $_ENV[$key] = $value;
        }
    }
}
