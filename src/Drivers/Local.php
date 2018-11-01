<?php
namespace Proner\Storage\Drivers;

use Exception;
use Proner\Storage\StorageTrait;

class Ftp implements DriversInterface
{
    use StorageTrait;

    private $workdirLocal;
    private $workdirRemote;
    protected $conection;

    public function __construct($workdirLocal, $workdirRemote)
    {
        $this->workdirLocal = $workdirLocal;
        $this->workdirRemote = $workdirRemote;
    }

    /**
     * @param $host
     * @throws Exception
     */
    public function connect($host)
    {
        @$this->conection = ftp_connect($host);
        if ($this->conection === false) {
            throw new Exception("Falha ao conectar com o host");
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
            throw new Exception("Credenciais nao aceitas");
        }
        ftp_pasv($this->conection, true);
    }

    /**
     * @param $file
     * @param $path
     * @param $name
     * @param bool $absolutePath
     * @return bool
     * @throws Exception
     */
    public function get($file, $path, $name, $absolutePath = false)
    {
        $fileRemote = $this->workdirRemote . '/' . $file;

        $nameFileLocal = basename($file);
        if ($name !== null) {
            $nameFileLocal = $name;
        }

        $pathFileLocal = $this->workdirLocal. DS;
        if ($absolutePath === true) {
            $pathFileLocal = "";
        }
        $fileLocal = $pathFileLocal . $nameFileLocal;
        if ($path !== null) {
            $fileLocal = $pathFileLocal . $this->directorySeparator($path) . DS . $nameFileLocal;
        }

        file_put_contents($fileLocal, '');

        if (@ftp_get($this->conection, $fileLocal, $fileRemote, FTP_BINARY)) {
            return true;
        } else {
            if (file_exists($fileLocal)) {
                unlink($fileLocal);
            }
            throw new Exception("Erro ao baixar o arquivo ".$file);
        }
    }

    /**
     * @param $file
     * @param $path
     * @param $name
     * @param bool $absolutePath
     * @return bool
     * @throws Exception
     */
    public function put($file, $path, $name, $absolutePath = false)
    {
        $pathFileLocal = $this->workdirLocal. DS;
        if ($absolutePath === true) {
            $pathFileLocal = "";
        }
        $fileLocal = $pathFileLocal . $file;


        $nameFileRemote = $file;
        if ($name !== null) {
            $nameFileRemote = $name;
        }
        $fileRemote = $this->workdirRemote . '/' . $nameFileRemote;
        if ($path !== null) {
            $fileRemote = $this->workdirRemote . '/' . $path . '/' . $nameFileRemote;
        }

        if (@ftp_put($this->conection, $fileRemote, $fileLocal, FTP_BINARY)) {
            return true;
        } else {
            throw new Exception("Erro ao enviar o arquivo");
        }
    }

    /**
     * @param $file
     * @param $path
     * @return bool
     */
    public function fileExists($file, $path)
    {
        $files = ftp_nlist($this->conection, $path);
        foreach ($files as $f) {
            if ($file == basename($f)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     */
    public function close()
    {
        ftp_close($this->conection);
    }
}
