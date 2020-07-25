<?php
declare(strict_types = 1);
namespace Proner\Storage\Drivers;

use Proner\Storage\Tools;

class Ftp implements DriversInterface
{
    private $storage;
    private $conection;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $host
     * @throws \Exception
     */
    public function connect(string $host)
    {
        @$this->conection = ftp_connect($host);
        if ($this->conection === false) {
            throw new \Exception("Failed to connect to host");
        }
    }

    /**
     * @param string $login
     * @param string $password
     * @throws \Exception
     */
    public function login(string $login, string $password)
    {
        if (@ftp_login($this->conection, $login, $password) === false) {
            throw new \Exception("Credentials Not Accepted");
        }
        ftp_pasv($this->conection, true);
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
        $fileRemote = $this->storage->getWorkdirRemote() . '/' . $file;

        $nameFileLocal = basename($file);
        if ($newName !== null) {
            $nameFileLocal = $newName;
        }

        $pathFileLocal = $this->storage->getWorkdirLocal();
        $fileLocal = $pathFileLocal . $nameFileLocal;
        if ($pathDestination !== null) {
            $fileLocal = $pathFileLocal . Tools::directorySeparator($pathDestination) . PS_DS . $nameFileLocal;
        }

        file_put_contents($fileLocal, '');

        if (@ftp_get($this->conection, $fileLocal, $fileRemote, FTP_BINARY)) {
            return true;
        } else {
            if (file_exists($fileLocal)) {
                unlink($fileLocal);
            }
            throw new \Exception("Error downloading file ".$file);
        }
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
        $pathFileLocal = $this->storage->getWorkdirLocal();
        $fileLocal = $pathFileLocal . $file;

        $nameFileRemote = basename($file);
        if (Tools::containsFile($pathDestination)) {
            $nameFileRemote = basename($pathDestination);
        }
        if ($newName !== null) {
            $nameFileRemote = $newName;
        }

        $fileRemote = $this->storage->getWorkdirRemote() . '/' . $nameFileRemote;
        if ($pathDestination !== null) {
            if (Tools::containsFile($pathDestination)) {
                $fileRemote = $this->storage->getWorkdirRemote() . '/' . $pathDestination;
            } else {
                $fileRemote = $this->storage->getWorkdirRemote() . '/' . $pathDestination . '/' . $nameFileRemote;
            }
        }

        if (@ftp_put($this->conection, $fileRemote, $fileLocal, FTP_BINARY)) {
            return true;
        } else {
            throw new \Exception("Erro ao enviar o arquivo");
        }
    }

    /**
     * @param string $file
     * @return false|string
     * @throws \Exception
     */
    public function getContent($file)
    {
        $pathAux = $this->storage->getWorkdirLocal();
        $this->storage->setWorkdirLocal(null);
        $path = __DIR__ . PS_DS . '..' . PS_DS . '..' . PS_DS . 'temp';
        try {
            $this->get($file, $path);
            $this->storage->setWorkdirLocal($pathAux);
            $content = file_get_contents($path. PS_DS .basename($file));
            if (file_exists($path. PS_DS .basename($file))) {
                unlink($path. PS_DS .basename($file));
            }
            return $content;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $file
     * @param string $content
     * @return bool
     * @throws \Exception
     */
    public function putContent($file, $content)
    {
        $pathAux = $this->storage->getWorkdirLocal();
        $this->storage->setWorkdirLocal(null);

        $tempFile = md5((string)rand(0, 99999999));
        $pathTemp = __DIR__ . PS_DS . '..' . PS_DS . '..' . PS_DS . 'temp';
        file_put_contents($pathTemp . PS_DS . $tempFile, $content);

        try {
            $this->put($pathTemp . PS_DS . $tempFile, $file);
            $this->storage->setWorkdirLocal($pathAux);
            if (file_exists($pathTemp. PS_DS . $tempFile)) {
                unlink($pathTemp. PS_DS . $tempFile);
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $file
     * @param string $path
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
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public function delete(string $file)
    {
        $path = $this->storage->getWorkdirLocal();
        $file = $path.PS_DS.$file;

        if (@ftp_delete($this->conection, $file)) {
            return true;
        } else {
            throw new \Exception("Error delete file ".$file);
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function close()
    {
        if (@ftp_close($this->conection) === false) {
            throw new \Exception("Error disconnecting");
        }
        return true;
    }
}
