<?php

namespace ilhamrhmtkbr\App\Config;

class RedisConf
{
    private static $redis = null;

    public static function getInstance()
    {
        if (self::$redis === null) {
            if ($_ENV['REDIS_TYPE'] === 'docker') {
                // PERBAIKAN: Instantiate Redis object dulu sebelum connect
                self::$redis = new \Redis();
                self::$redis->connect('talent-hub-redis', 6379);

                // Optional: Set database jika diperlukan
                if (isset($_ENV['REDIS_DB'])) {
                    self::$redis->select((int)$_ENV['REDIS_DB']);
                }
            } else {
                self::$redis = new \Predis\Client([
                    'host' => $_ENV['REDIS_HOST'],
                    'port' => $_ENV['REDIS_PORT'],
                    'database' => $_ENV['REDIS_DB'],
                    'username' => $_ENV['REDIS_USER'],
                    'password' => $_ENV['REDIS_PASSWORD'],
                ]);
            }
        }

        return self::$redis;
    }
}