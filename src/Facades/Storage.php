<?php

namespace Proner\Storage\Facades;

use Proner\Storage\StorageTrait;

class Storage
{
    use StorageTrait;

    public static function setWorkdirLocal($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_LOCAL'] = StorageTrait::directorySeparatorStatic($workdir);
    }

    public static function setWorkdirRemote($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_REMOTE'] = $workdir;
    }

    private static function build()
    {
        $storage = new \Proner\Storage\Storage($_ENV['PSTORAGE_DRIVER']);
        if ($_ENV['PSTORAGE_HOST']){
            $storage->setHost($_ENV['PSTORAGE_HOST']);
        }else{
            throw new \Exception("Variavel de ambiente PSTORAGE_HOST nao definida");
        }

        if ($_ENV['PSTORAGE_USER']){
            $storage->setLogin($_ENV['PSTORAGE_USER'],$_ENV['PSTORAGE_PASS']);
        }else{
            throw new \Exception("Variavel de ambiente PSTORAGE_USER nao definida");
        }
        return $storage;
    }

    public static function get($file, $path = null, $name = null)
    {
        $storage = self::build();
        $storage->get($file, $path, $name);
    }

    public static function getContent($file)
    {
        $storage = self::build();
        return $storage->getContent($file);
    }

    public static function put($file, $path = null, $name = null)
    {
        $storage = self::build();
        $storage->put($file, $path, $name);
    }

    public static function putContent($content,$file)
    {
        $storage = self::build();
        $storage->putContent($content,$file);
    }
}