<?php
declare(strict_types = 1);
namespace Proner\Storage\Facades;

use Proner\Storage\Tools;

class Storage
{
    private static $storage;

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    
    /**
     * @param string $workdir
     */
    public static function setWorkdirLocal(string $workdir)
    {
        self::$storage->setWorkdirLocal($workdir);
    }

    /**
     * @param string $workdir
     */
    public static function setWorkdirRemote(string $workdir)
    {
        self::$storage->setWorkdirRemote($workdir);
    }

    /**
     * @throws \Exception
     */
    private static function build()
    {
        if (self::$storage === null) {
            self::$storage = new \Proner\Storage\Storage($_ENV['PSTORAGE_DRIVER']);
            if (isset($_ENV['PSTORAGE_HOST'])) {
                self::$storage->setHost($_ENV['PSTORAGE_HOST']);
            }

            if (isset($_ENV['PSTORAGE_USER'])) {
                self::$storage->setLogin($_ENV['PSTORAGE_USER'], $_ENV['PSTORAGE_PASS']);
            }

            if (isset($_ENV['PSTORAGE_WORKDIR_LOCAL'])) {
                self::$storage->setWorkdirLocal($_ENV['PSTORAGE_WORKDIR_LOCAL']);
            }

            if (isset($_ENV['PSTORAGE_WORKDIR_REMOTE'])) {
                self::$storage->setWorkdirRemote($_ENV['PSTORAGE_WORKDIR_REMOTE']);
            }

            if (isset($_ENV['PSTORAGE_CACHE']) && $_ENV['PSTORAGE_CACHE'] === true) {
                $cacheHost = $_ENV['PSTORAGE_CACHE_HOST'] ?? null;
                $cachePort = (int)$_ENV['PSTORAGE_CACHE_PORT'] ?? null;
                $cacheSecurity = $_ENV['PSTORAGE_CACHE_SECURITY'] ?? null;
                $cacheLogin = $_ENV['PSTORAGE_CACHE_LOGIN'] ?? null;
                $cachePassword = $_ENV['PSTORAGE_CACHE_PASSWORD'] ?? null;
                self::$storage->cacheConnect($cacheHost, $cachePort, $cacheSecurity, $cacheLogin, $cachePassword);
                if ($_ENV['PSTORAGE_CACHE_TTL']) {
                    self::$storage->setCacheTtl((int)$_ENV['PSTORAGE_CACHE_TTL']);
                }
            }
        }
        return self::$storage;
    }

    /**
     * @param string $file
     * @param string $path
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public static function get($file, $path = null, $name = null)
    {
        try {
            $storage = self::build();
            return $storage->get($file, $path, $name);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $file
     * @return false|string
     * @throws \Exception
     */
    public static function getContent($file)
    {
        try {
            $storage = self::build();
            return $storage->getContent($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $file
     * @param string $path
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public static function put($file, $path = null, $name = null)
    {
        try {
            $storage = self::build();
            return $storage->put($file, $path, $name);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $content
     * @param string $file
     * @throws \Exception
     */
    public static function putContent($content, $file)
    {
        try {
            $storage = self::build();
            $storage->putContent($content, $file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $file
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public static function fileExists($file, $path)
    {
        try {
            $storage = self::build();
            return $storage->fileExists($file, $path);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public static function getImage($file)
    {
        try {
            $storage = self::build();
            return $storage->getImage($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public static function delete($file)
    {
        try {
            $storage = self::build();
            return $storage->delete($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
