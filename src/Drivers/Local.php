<?php
namespace Proner\Storage\Drivers;

use Exception;
use Proner\Storage\StorageTrait;

class Local implements DriversInterface
{
    use StorageTrait;

    private $storage;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param $host
     * @return bool
     */
    public function connect($host = null)
    {
        return true;
    }

    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login = null, $password = null)
    {
        return true;
    }

    /**
     * @param $file
     * @param $pathDestination
     * @param $newName
     * @return bool
     * @throws Exception
     */
    public function get($file, $pathDestination = null, $newName = null)
    {
        $nameFileLocal = basename($file);
        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        $pathDestination = $this->storage->getWorkdirLocal() . $this->directorySeparator($pathDestination);
        $content = $this->getContent($file);
        if (file_put_contents($pathDestination . DS . $nameFileLocal, $content) !== false) {
            return true;
        }
        throw new Exception("Erro ao gravar arquivo no local");
    }

    /**
     * @param $file
     * @param $pathDestination
     * @param null $newName
     * @return bool
     * @throws Exception
     */
    public function put($file, $pathDestination = null, $newName = null)
    {
        $nameFileLocal = basename($file);
        $content = $this->getContent($file);
        $pathDestination = $this->storage->getWorkdirRemote() . $this->directorySeparator($pathDestination);

        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        if ($data = file_put_contents($pathDestination . DS . $nameFileLocal, $content) !== false) {
            return true;
        }
        throw new Exception("Erro ao gravar arquivo no destino");
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function getContent($file)
    {
        $content = file_get_contents($file);
        if ($content !== false) {
            return $content;
        }
        throw new Exception("Error fetching the contents of ".$file." file");
    }

    /**
     * @param $file
     * @param $content
     * @return bool
     * @throws Exception
     */
    public function putContent($file, $content)
    {
        if (file_put_contents($file, $content)) {
            return true;
        }
        throw new Exception("Error while writing the contents of ".$file." file");
    }

    /**
     * @param $file
     * @param $path
     * @return bool
     */
    public function fileExists($file, $path = null)
    {
        $dir = $this->storage->getWorkdirRemote();
        if ($path) {
            $dir = $this->storage->getWorkdirRemote().$path;
        }

        $files = scandir($dir);
        foreach ($files as $f) {
            if ($file == basename($f)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }
}
