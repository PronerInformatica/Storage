<?php
namespace Proner\Storage\Drivers;

use Proner\Storage\StorageTrait;

class Ftp implements DriversInterface
{
    use StorageTrait;

    protected $conection;

    public function connect($host)
    {
        @$this->conection = ftp_connect($host);
        if($this->conection === false){
            throw new \Exception("Falha ao conectar com o host");
        }
    }

    public function login($login,$password)
    {
        if(@ftp_login($this->conection, $login, $password) === false){
            throw new \Exception("Credenciais nao aceitas");
        }
        ftp_pasv($this->conection, TRUE);
    }

    public function get($file,$path,$name,$absolutePath = false)
    {
        $fileRemote = $_ENV['PSTORAGE_WORKDIR_REMOTE'] . '/' . $file;

        $nameFileLocal = basename($file);
        if( $name !== null){
            $nameFileLocal = $name;
        }

        $pathFileLocal = $_ENV['PSTORAGE_WORKDIR_LOCAL']. DIRECTORY_SEPARATOR;
        if( $absolutePath === true){
            $pathFileLocal = "";
        }
        $fileLocal = $pathFileLocal . $nameFileLocal;
        if( $path !== null){
            $fileLocal = $pathFileLocal . $this->directorySeparator($path) . DIRECTORY_SEPARATOR . $nameFileLocal;
        }

        file_put_contents($fileLocal, '');

        if (@ftp_get($this->conection, $fileLocal, $fileRemote, FTP_BINARY)) {
            return true;
        } else {
            if (file_exists($fileLocal)) {
                unlink($fileLocal);
            }
            throw new \Exception("Erro ao baixar o arquivo");
        }
    }

    public function put($file,$path,$name,$absolutePath = false)
    {
        $pathFileLocal = $_ENV['PSTORAGE_WORKDIR_LOCAL']. DIRECTORY_SEPARATOR;
        if( $absolutePath === true){
            $pathFileLocal = "";
        }
        $fileLocal = $pathFileLocal . $file;


        $nameFileRemote = $file;
        if( $name !== null){
            $nameFileRemote = $name;
        }
        $fileRemote = $_ENV['PSTORAGE_WORKDIR_REMOTE'] . '/' . $nameFileRemote;
        if( $path !== null){
            $fileRemote = $_ENV['PSTORAGE_WORKDIR_REMOTE'] . '/' . $path . '/' . $nameFileRemote;
        }

        if (@ftp_put($this->conection, $fileRemote, $fileLocal, FTP_ASCII)) {
            return true;
        } else {
            throw new \Exception("Erro ao enviar o arquivo");
        }
    }

    public function close()
    {
        ftp_close($this->conection);
    }
}