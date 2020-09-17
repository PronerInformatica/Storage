<?php
declare(strict_types = 1);
namespace Proner\Storage;

use Exception;
use Proner\Storage\Caches\Redis;
use Proner\Storage\Drivers\Ftp;
use Proner\Storage\Drivers\Local;

class Storage
{
    private $driver;
    private $host;
    private $login;
    private $password;
    private $workdirLocal = "." .PS_DS;
    private $workdirRemote = ".";
    private $cache;
    private $cacheEnable = false;
    private $cacheHost;
    private $cachePort;
    private $cacheSecurity;
    private $cacheLogin;
    private $cachePassword;
    private $cacheTtl = 604800;

    /**
     * Storage constructor.
     * @param string $driver
     */
    public function __construct(string $driver)
    {
        //TRATAMENTO DO WORKDIR
        if (isset($_ENV['PSTORAGE_WORKDIR_LOCAL'])) {
            $this->setWorkdirLocal($_ENV['PSTORAGE_WORKDIR_LOCAL']);
        }

        if (isset($_ENV['PSTORAGE_WORKDIR_REMOTE'])) {
            $this->setWorkdirRemote(".". PS_DS . $_ENV['PSTORAGE_WORKDIR_REMOTE']);
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
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param string $login
     * @param string $password
     */
    public function setLogin(string $login, string $password)
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
     * @param string $workdir
     */
    public function setWorkdirLocal($workdir)
    {
        $this->workdirLocal = Tools::directorySeparator($workdir);
    }

    /**
     * @return string
     */
    public function getWorkdirRemote()
    {
        return $this->workdirRemote;
    }

    /**
     * @param string $workdir
     */
    public function setWorkdirRemote(string $workdir)
    {
        $this->workdirRemote = Tools::directorySeparator($workdir);
    }

    /**
     * @param boolean $cache
     */
    public function setCacheEnable($cache)
    {
        $this->cacheEnable = $cache;
    }

    /**
     * @param string $host
     * @param int $port
     * @param string|null $security
     * @param string|null $login
     * @param string|null $password
     */
    public function cacheConnect(
        string $host,
        int $port,
        $security = null,
        $login = null,
        $password = null
    ) {
        $this->cacheHost = $host;
        $this->cachePort = $port;
        $this->cacheSecurity = $security;
        $this->cacheLogin = $login;
        $this->cachePassword = $password;
        $this->cache = new Redis();
        $this->cache->connect(
            $this->cacheHost,
            $this->cachePort,
            $this->cacheSecurity,
            $this->cacheLogin,
            $this->cachePassword
        );
        $this->setCacheEnable(true);
    }

    /**
     * @param string $file
     * @return string
     */
    private function generateCacheKey($file)
    {
        $key = $this->cacheHost;
        $key .= "_".$this->getWorkdirRemote();
        $key .= "_".implode("_", explode("/", $file));
        return $key;
    }

    /**
     * @param integer $seconds
     */
    public function setCacheTtl($seconds)
    {
        $this->cacheTtl = $seconds;
    }

    /**
     * @param string $file
     * @param string $pathDestination
     * @param string $newName
     * @return bool
     * @throws Exception
     */
    public function get($file, $pathDestination = null, $newName = null)
    {
        $cacheKey = null;

        if ($this->cacheEnable === true) {
            $cacheKey = $this->generateCacheKey($file);
            $content = $this->cache->get($cacheKey);
            if ($content !== null) {
                $nameFileLocal = basename($file);
                if ($newName !== null) {
                    $nameFileLocal = $newName;
                }

                $pathFileLocal = $this->getWorkdirLocal();
                $fileLocal = $pathFileLocal . $nameFileLocal;
                if ($pathDestination !== null) {
                    $fileLocal = $pathFileLocal . Tools::directorySeparator($pathDestination) . PS_DS . $nameFileLocal;
                }
                file_put_contents($fileLocal, $content);
                return true;
            }
        }

        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $return = $this->driver->get($file, $pathDestination, $newName);
            $this->driver->close();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->cacheEnable === true) {
            $nameFileLocal = basename($file);
            if ($newName !== null) {
                $nameFileLocal = $newName;
            }

            $pathFileLocal = $this->getWorkdirLocal();
            $fileLocal = $pathFileLocal . $nameFileLocal;
            if ($pathDestination !== null) {
                $fileLocal = $pathFileLocal . Tools::directorySeparator($pathDestination) . PS_DS . $nameFileLocal;
            }
            $content = file_get_contents($fileLocal);
            $this->cache->set($cacheKey, $content, $this->cacheTtl);
        }

        return $return;
    }

    /**
     * @param string $file
     * @return false|string
     * @throws Exception
     */
    public function getContent($file)
    {
        $cacheKey = null;

        if ($this->cacheEnable === true) {
            $cacheKey = $this->generateCacheKey($file);
            $content = $this->cache->get($cacheKey);
            if ($content !== null) {
                return $content;
            }
        }

        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $content = $this->driver->getContent($file);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->cacheEnable === true) {
            $this->cache->set($cacheKey, $content, $this->cacheTtl);
        }

        return $content;
    }

    /**
     * @param string $file
     * @param string $pathDestination
     * @param string $newName
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
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->cacheEnable === true) {
            $content = $this->driver->getContentTempFile();
            if ($newName !== null) {
                $key = $this->generateCacheKey($pathDestination.'/'.$newName);
            } else {
                $key = $this->generateCacheKey($pathDestination.'/'.$file);
            }
            $this->cache->set($key, $content, $this->cacheTtl);
        }

        return $return;
    }

    /**
     * @param string $file
     * @param string $content
     * @return bool
     * @throws Exception
     */
    public function putContent($file, $content)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            $return = $this->driver->putContent($file, $content);
            $this->driver->close();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->cacheEnable === true) {
            $this->cache->set($this->generateCacheKey($file), $content, $this->cacheTtl);
        }

        return $return;
    }

    /**
     * @param string $file
     * @param string $path
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
     * @param string $file
     * @return string
     * @throws Exception
     */
    public function getImage(string $file)
    {
        $extension = Tools::getExtensionByName($file);
        $tempFile = md5((string)rand(0, 99999999)).'.'.$extension;
        $pathAux = $this->getWorkdirLocal();
        $this->setWorkdirLocal(null);

        try {
            $this->get($file, PS_TMP_DIR, $tempFile);
            $this->setWorkdirLocal($pathAux);
            $content = base64_encode(file_get_contents(PS_TMP_DIR . PS_DS . $tempFile));
            if (file_exists(PS_TMP_DIR . PS_DS . $tempFile)) {
                unlink(PS_TMP_DIR . PS_DS . $tempFile);
            }
            return "data:image/$extension;base64, ". $content;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $file
     * @return bool
     * @throws Exception
     */
    public function delete(string $file)
    {
        try {
            $this->driver->connect($this->host);
            $this->driver->login($this->login, $this->password);
            return $this->driver->delete($file);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
