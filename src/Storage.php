<?php

namespace Proner\Storage;

use Exception;
use Proner\Storage\Drivers\Ftp;
use Proner\Storage\Drivers\Local;

class Storage
{
    use StorageTrait;

    private $driver;
    private $host;
    private $login;
    private $password;
    private $workdirLocal = "." .DS;
    private $workdirRemote = ".";

    /**
     * Storage constructor.
     * @param string $driver
     */
    public function __construct($driver)
    {
        //TRATAMENTO DO WORKDIR
        if (@$_ENV['PSTORAGE_WORKDIR_LOCAL'] !== null) {
            $this->workdirLocal = $this->directorySeparator($_ENV['PSTORAGE_WORKDIR_LOCAL']);
        }

        if (@$_ENV['PSTORAGE_WORKDIR_REMOTE'] !== null) {
            $this->workdirRemote = ".". DS . $_ENV['PSTORAGE_WORKDIR_REMOTE'];
        }

        $this->driver = $driver;
        switch ($driver) {
            case null:
            case 'ftp':
                $this->driver = new Ftp($this);
                break;
            case 'local':
                $this->driver = new Local($this);
                break;
            default:
                echo "driver não encontrato";
        }
    }

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param $login
     * @param $password
     */
    public function setLogin($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getWorkdirLocal()
    {
        return $this->workdirLocal;
    }

    /**
     * @param $workdir
     */
    public function setWorkdirLocal($workdir)
    {
        if ($workdir === null) {
            $this->workdirLocal = '';
        } else {
            $this->workdirLocal = $this->directorySeparator($workdir) . DS;
        }
    }

    /**
     * @return string
     */
    public function getWorkdirRemote()
    {
        return $this->workdirRemote;
    }

    /**
     * @param $workdir
     */
    public function setWorkdirRemote($workdir)
    {
        $this->workdirRemote = $this->directorySeparator($workdir);
    }

    /**
     * @param $file
     * @param null $pathDestination
     * @param null $newName
     * @return bool
     * @throws Exception
     */
    public function get($file, $pathDestination = null, $newName = null)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $return = $this->driver->get($file, $pathDestination, $newName);
            $this->driver->close();
            return $return;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @return false|string
     * @throws Exception
     */
    public function getContent($file)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            return $this->driver->getContent($file);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param null $pathDestination
     * @param null $newName
     * @return bool
     * @throws Exception
     */
    public function put($file, $pathDestination = null, $newName = null)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $return = $this->driver->put($file, $pathDestination, $newName);
            $this->driver->close();
            return $return;
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
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            return $this->driver->putContent($file, $content);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param $path
     * @return bool
     * @throws Exception
     */
    public function fileExists($file, $path = null)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $return = $this->driver->fileExists($file, $path);
            $this->driver->close();
            return $return;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function getImage($file)
    {
        $extension = $this->getExtensionByName($file);
        $tempFile = md5(rand(0, 99999999)).'.'.$extension;
        $pathAux = $this->getWorkdirLocal();
        $this->setWorkdirLocal(null);
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            if (!$this->driver->get($file, TMP_DIR, $tempFile)) {
                $this->driver->close();
                return false;
            }
            $this->setWorkdirLocal($pathAux);
            $this->driver->close();
            $content = base64_encode(file_get_contents(TMP_DIR . DS . $tempFile));
            if (file_exists(TMP_DIR . DS . $tempFile)) {
                unlink(TMP_DIR . DS . $tempFile);
            }
            return "data:image/$extension;base64, ". $content;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
