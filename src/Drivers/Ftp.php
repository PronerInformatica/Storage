<?php
namespace Proner\Storage\Drivers;

use Exception;
use Proner\Storage\StorageTrait;

class Ftp implements DriversInterface
{
    use StorageTrait;

    private $storage;
    private $conection;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param $host
     * @throws Exception
     */
    public function connect($host)
    {
        @$this->conection = ftp_connect($host);
        if ($this->conection === false) {
            throw new Exception("Failed to connect to host");
        }
    }

    /**
     * @param $login
     * @param $password
     * @throws Exception
     */
    public function login($login, $password)
    {
        if (@ftp_login($this->conection, $login, $password) === false) {
            throw new Exception("Credentials Not Accepted");
        }
        ftp_pasv($this->conection, true);
    }

    /**
     * @param $file
     * @param $pathDestination
     * @param $newName
     * @return bool
     * @throws Exception
     */
    public function get($file, $pathDestination, $newName = null)
    {
        $fileRemote = $this->storage->getWorkdirRemote() . '/' . $file;

        $nameFileLocal = basename($file);
        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        $pathFileLocal = $this->storage->getWorkdirLocal();
        $fileLocal = $pathFileLocal . $nameFileLocal;
        if ($pathDestination !== null) {
            $fileLocal = $pathFileLocal . $this->directorySeparator($pathDestination) . DS . $nameFileLocal;
        }

        file_put_contents($fileLocal, '');

        if (@ftp_get($this->conection, $fileLocal, $fileRemote, FTP_BINARY)) {
            return true;
        } else {
            if (file_exists($fileLocal)) {
                unlink($fileLocal);
            }
            throw new Exception("Error downloading file ".$file);
        }
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
        $pathFileLocal = $this->storage->getWorkdirLocal();
        $fileLocal = $pathFileLocal . $file;


        $nameFileRemote = basename($file);
        if ($this->containsFile($pathDestination)) {
            $nameFileRemote = basename($pathDestination);
        }
        if ($newName !== null) {
            $nameFileRemote = $newName;
        }

        $fileRemote = $this->storage->getWorkdirRemote() . '/' . $nameFileRemote;
        if ($pathDestination !== null) {
            if ($this->containsFile($pathDestination)) {
                $fileRemote = $this->storage->getWorkdirRemote() . '/' . $pathDestination;
            } else {
                $fileRemote = $this->storage->getWorkdirRemote() . '/' . $pathDestination . '/' . $nameFileRemote;
            }
        }

        if (@ftp_put($this->conection, $fileRemote, $fileLocal, FTP_BINARY)) {
            return true;
        } else {
            throw new Exception("Erro ao enviar o arquivo");
        }
    }

    /**
     * @param $file
     * @return false|string
     * @throws Exception
     */
    public function getContent($file)
    {
        $pathAux = $this->storage->getWorkdirLocal();
        $this->storage->setWorkdirLocal(null);
        $path = __DIR__ . DS . '..' . DS . '..' . DS . 'temp';
        try {
            $this->get($file, $path);
            $this->storage->setWorkdirLocal($pathAux);
            $content = file_get_contents($path. DS .basename($file));
            if (file_exists($path. DS .basename($file))) {
                unlink($path. DS .basename($file));
            }
            return $content;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param $content
     * @return bool
     * @throws Exception
     */
    public function putContent($file, $content)
    {
        $pathAux = $this->storage->getWorkdirLocal();
        $this->storage->setWorkdirLocal(null);

        $tempFile = md5(rand(0, 99999999));
        $pathTemp = __DIR__ . DS . '..' . DS . '..' . DS . 'temp';
        file_put_contents($pathTemp . DS . $tempFile, $content);

        try {
            $this->put($pathTemp . DS . $tempFile, $file);
            $this->storage->setWorkdirLocal($pathAux);
            if (file_exists($pathTemp. DS . $tempFile)) {
                unlink($pathTemp. DS . $tempFile);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param $path
     * @return bool
     */
    public function fileExists($file, $path)
    {
        $path = $this->storage->getWorkdirRemote() . '/' . $path;
        $files = ftp_nlist($this->conection, $path);
        foreach ($files as $f) {
            if ($file == basename($f)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function close()
    {
        if (@ftp_close($this->conection) === false) {
            throw new Exception("Error disconnecting");
        }
        return true;
    }
}
