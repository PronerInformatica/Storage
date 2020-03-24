<?php

namespace Proner\Storage\Facades;

use Proner\Storage\StorageTrait;

class Storage
{
    use StorageTrait;

    private static $storage;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @param $workdir
     */
    public static function setWorkdirLocal($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_LOCAL'] = StorageTrait::directorySeparatorStatic($workdir);
    }

    /**
     * @param $workdir
     */
    public static function setWorkdirRemote($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_REMOTE'] = $workdir;
    }

    /**
     * @return \Proner\Storage\Storage
     * @throws \Exception
     */
    private static function build()
    {
        if (self::$storage === null) {
            self::$storage = new \Proner\Storage\Storage($_ENV['PSTORAGE_DRIVER']);
            if ($_ENV['PSTORAGE_HOST']) {
                self::$storage->setHost($_ENV['PSTORAGE_HOST']);
            } else {
                throw new \Exception("Variavel de ambiente PSTORAGE_HOST nao definida");
            }

            if ($_ENV['PSTORAGE_USER']) {
                self::$storage->setLogin($_ENV['PSTORAGE_USER'], $_ENV['PSTORAGE_PASS']);
            } else {
                throw new \Exception("Variavel de ambiente PSTORAGE_USER nao definida");
            }

            if ($_ENV['PSTORAGE_WORKDIR_LOCAL']) {
                self::$storage->setWorkdirLocal($_ENV['PSTORAGE_WORKDIR_LOCAL']);
            }

            if ($_ENV['PSTORAGE_WORKDIR_REMOTE']) {
                self::$storage->setWorkdirRemote($_ENV['PSTORAGE_WORKDIR_REMOTE']);
            }

            if ($_ENV['PSTORAGE_CACHE'] === true) {
                $cacheHost = $_ENV['PSTORAGE_CACHE_HOST'] ?? null;
                $cachePort = $_ENV['PSTORAGE_CACHE_PORT'] ?? null;
                $cacheSecurity = $_ENV['PSTORAGE_CACHE_SECURITY'] ?? null;
                $cacheLogin = $_ENV['PSTORAGE_CACHE_LOGIN'] ?? null;
                $cachePassword = $_ENV['PSTORAGE_CACHE_PASSWORD'] ?? null;
                self::$storage->cacheConnect($cacheHost, $cachePort, $cacheSecurity, $cacheLogin, $cachePassword);
                if ($_ENV['PSTORAGE_CACHE_TTL']) {
                    self::$storage->setCacheTtl = $_ENV['PSTORAGE_CACHE_TTL'];
                }
            }
        }
    }

    /**
     * @param $file
     * @param null $path
     * @param null $name
     * @return bool
     * @throws \Exception
     */
    public static function get($file, $path = null, $name = null)
    {
        try {
            self::build();
            return self::$storage->get($file, $path, $name);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $file
     * @return false|string
     * @throws \Exception
     */
    public static function getContent($file)
    {
        try {
            self::build();
            return self::$storage->getContent($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $file
     * @param null $path
     * @param null $name
     * @return bool
     * @throws \Exception
     */
    public static function put($file, $path = null, $name = null)
    {
        try {
            self::build();
            return self::$storage->put($file, $path, $name);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $content
     * @param $file
     * @throws \Exception
     */
    public static function putContent($content, $file)
    {
        try {
            self::build();
            self::$storage->putContent($content, $file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $file
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public static function fileExists($file, $path)
    {
        try {
            self::build();
            return self::$storage->fileExists($file, $path);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $file
     * @return bool
     * @throws \Exception
     */
    public static function getImage($file)
    {
        try {
            self::build();
            return self::$storage->getImage($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
