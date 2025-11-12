<?php

namespace ilhamrhmtkbr\App\Config;

class RedisConf
{
    private static ?\Redis $redis = null;

    public static function getInstance(): \Redis
    {
        if (self::$redis === null) {
            self::$redis = new \Redis();
            self::$redis->connect('talent-hub-redis', 6379); // Ganti '127.0.0.1' dan port jika diperlukan
        }

        return self::$redis;
    }
}
