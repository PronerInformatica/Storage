<?php

namespace Proner\Storage\Facades;

use Proner\Storage\StorageTrait;

class Storage
{
    use StorageTrait;

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
        $storage = new \Proner\Storage\Storage($_ENV['PSTORAGE_DRIVER']);
        if ($_ENV['PSTORAGE_HOST']) {
            $storage->setHost($_ENV['PSTORAGE_HOST']);
        } else {
            throw new \Exception("Variavel de ambiente PSTORAGE_HOST nao definida");
        }

        if ($_ENV['PSTORAGE_USER']) {
            $storage->setLogin($_ENV['PSTORAGE_USER'], $_ENV['PSTORAGE_PASS']);
        } else {
            throw new \Exception("Variavel de ambiente PSTORAGE_USER nao definida");
        }

        if ($_ENV['PSTORAGE_WORKDIR_LOCAL']) {
            $storage->setWorkdirLocal($_ENV['PSTORAGE_WORKDIR_LOCAL']);
        }

        if ($_ENV['PSTORAGE_WORKDIR_REMOTE']) {
            $storage->setWorkdirRemote($_ENV['PSTORAGE_WORKDIR_REMOTE']);
        }
        return $storage;
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
            $storage = self::build();
            return $storage->get($file, $path, $name);
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
            $storage = self::build();
            return $storage->getContent($file);
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
            $storage = self::build();
            return $storage->put($file, $path, $name);
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
            $storage = self::build();
            $storage->putContent($content, $file);
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
            $storage = self::build();
            return $storage->fileExists($file, $path);
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
            $storage = self::build();
            return $storage->getImage($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
