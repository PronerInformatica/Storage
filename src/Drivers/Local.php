<?php
declare(strict_types = 1);
namespace Proner\Storage\Drivers;

use Proner\Storage\Tools;

class Local implements DriversInterface
{
    private $storage;
    private $tempFile;

    /**
     * Local constructor.
     * @param $storage
     */
    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $host
     * @return bool
     */
    public function connect(string $host = null)
    {
        return true;
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login(string $login = null, string $password = null)
    {
        return true;
    }

    /**
     * @param string $file
     * @param string $pathDestination
     * @param string $newName
     * @return bool
     * @throws \Exception
     */
    public function get($file, $pathDestination = null, $newName = null)
    {
        $nameFileLocal = basename($file);
        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        $pathDestination = $this->storage->getWorkdirLocal() . Tools::directorySeparator($pathDestination);
        $content = $this->getContent($file);
        if (file_put_contents($pathDestination . PS_DS . $nameFileLocal, $content) !== false) {
            return true;
        }
        throw new \Exception("Erro ao gravar arquivo no local");
    }

    /**
     * @param string $file
     * @param string $pathDestination
     * @param string $newName
     * @return bool
     * @throws \Exception
     */
    public function put(string $file, string $pathDestination = null, string $newName = null)
    {
        $nameFileLocal = basename($file);
        $content = $this->getContent($file);
        $pathDestination = $this->storage->getWorkdirRemote() . Tools::directorySeparator($pathDestination);

        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        if ($data = file_put_contents($pathDestination . PS_DS . $nameFileLocal, $content) !== false) {
            $this->tempFile = file_get_contents($pathDestination . PS_DS . $nameFileLocal);
            return true;
        }
        throw new \Exception("Erro ao gravar arquivo no destino");
    }

    /**
     * @param string $file
     * @return string
     * @throws \Exception
     */
    public function getContent($file)
    {
        $remotePath = $this->storage->getWorkdirRemote();
        $file = $remotePath . PS_DS . $file;
        $content = file_get_contents($file);
        if ($content !== false) {
            return $content;
        }
        throw new \Exception("Error fetching the contents of ".$file." file");
    }

    /**
     * @param string $file
     * @param string $content
     * @return bool
     * @throws \Exception
     */
    public function putContent($file, $content)
    {
        $remotePath = $this->storage->getWorkdirRemote();
        $file = $remotePath . PS_DS . $file;
        if (file_put_contents($file, $content)) {
            return true;
        }
        throw new \Exception("Error while writing the contents of ".$file." file");
    }

    /**
     * @param string $file
     * @param string $path
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
     * @return mixed
     */
    public function getContentTempFile()
    {
        return $this->tempFile;
    }

    /**
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public function delete(string $file)
    {
        $path = $this->storage->getWorkdirRemote();
        $file = $path.PS_DS.$file;

        if (unlink($file)) {
            return true;
        } else {
            throw new \Exception("Error delete file ".$file);
        }
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }
}
