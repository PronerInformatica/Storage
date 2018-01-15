<?php

namespace Proner\Storage;

use Proner\Storage\Drivers\Ftp;

class Storage
{
    use StorageTrait;

    private $host;
    private $login;
    private $password;
    private $driver;

    public function __construct($driver = 'ftp'){

        $this->driver = $driver;
        switch ($driver) {
            case null:
            case 'ftp':
                $this->driver = new Ftp();
                break;
            default:
                echo "driver não encontrato";
        }

        //TRATAMENTO DO WORKDIR
        if( $_ENV['PSTORAGE_WORKDIR_LOCAL'] !== null){
            $_ENV['PSTORAGE_WORKDIR_LOCAL'] = $this->directorySeparator($_ENV['PSTORAGE_WORKDIR_LOCAL']);
        }
        if( $_ENV['PSTORAGE_WORKDIR_LOCAL'] === null){
            $_ENV['PSTORAGE_WORKDIR_LOCAL'] = ".";
        }

        if( $_ENV['PSTORAGE_WORKDIR_REMOTE'] !== null){
            $_ENV['PSTORAGE_WORKDIR_REMOTE'] = "./".$_ENV['PSTORAGE_WORKDIR_REMOTE'];
        }
        if( $_ENV['PSTORAGE_WORKDIR_REMOTE'] === null){
            $_ENV['PSTORAGE_WORKDIR_REMOTE'] = ".";
        }
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setLogin($login,$password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function setWorkdirLocal($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_LOCAL'] = $this->directorySeparator($workdir);
    }

    public function setWorkdirRemote($workdir)
    {
        $_ENV['PSTORAGE_WORKDIR_REMOTE'] = $workdir;
    }

    public function get($file, $path = null, $name = null, $absolutePath = false)
    {
        try{
            $this->driver->connect($this->host);
            $this->driver->login($this->login,$this->password);
            $this->driver->get($file,$path,$name,$absolutePath);
            $this->driver->close();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getContent($file)
    {
        try{
            $tempFile = md5(rand(0, 99999999));
            $pathTemp = $this->directorySeparator(__DIR__ . '/../' . 'temp');
            $this->get($file,$pathTemp,$tempFile,true);
            $content = file_get_contents($pathTemp. DIRECTORY_SEPARATOR .$tempFile);
            unlink($pathTemp. DIRECTORY_SEPARATOR .$tempFile);
            return $content;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function put($file, $path = null, $name = null, $absolutePath = false)
    {
        try{
            $this->driver->connect($this->host);
            $this->driver->login($this->login,$this->password);
            $this->driver->put($file,$path,$name,$absolutePath);
            $this->driver->close();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function putContent($content,$file)
    {
        try{
            $tempFile = md5(rand(0, 99999999));
            $pathTemp = $this->directorySeparator(__DIR__ . '/../' . 'temp');
            file_put_contents($pathTemp . DIRECTORY_SEPARATOR. $tempFile, $content);
            $this->put($pathTemp . DIRECTORY_SEPARATOR. $tempFile,dirname($file),basename($file),true);
            unlink($pathTemp. DIRECTORY_SEPARATOR .$tempFile);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}