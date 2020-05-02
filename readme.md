# Storage

Cliente em PHP para comunicacao com o storage

## Instalação
```bash
composer require proner/storage
```

## Exemplo
```php
<?php
use Proner\Storage\Storage;
$storage = new Storage('ftp');
$storage->setHost('172.20.15.999');
$storage->setLogin('usuario','senha');

$storage->setWorkdirLocal('arquivos');
$storage->setWorkdirRemote('arquivos');

//conectar e habilitar o cache
$storage->cacheConnect('172.20.15.990', 6379);
```
 > É possível usar a *Facade*(Métodos estaticos) da lib, mas para isso é preciso estar com as variaveis de ambiente definidas.

## Métodos

### Método: **get**
Baixa o arquivo do servidor
```php
<?php
$file = 'pasta/teste.txt'; //Arquivo que vai ser baixado
$path = 'pasta'; //Diretorio a onde o arquivo será salvo locamente

$storage->get($file, $path);

//OU COM MÉTODO ESTATICO

Storage::get($file, $path);
```

### Método: **getContent**
Pega o conteúdo do arquivo do servidor
```php
<?php
$file = 'pasta/teste.txt'; //Arquivo que está procurando

$storage->getContent($file);

//OU COM MÉTODO ESTATICO

Storage::getContent($file);
```

### Método: **put**
Enviar o arquivo para o servidor
```php
<?php
$file = 'pasta/teste.txt'; //Arquivo que vai ser enviado
$path = 'pasta'; //Diretorio a onde o arquivo será salvo locamente

$storage->put($file, $path);

//OU COM MÉTODO ESTATICO

Storage::put($file, $path);
```

### Método: **putContent**
Cria um arquivo no servidor
```php
<?php
$file = 'pasta/teste.txt'; //Arquivo que vai ser enviado
$content = 'pasta'; //Conteúdo do novo arquivo

$storage->putContent($file, $content);

//OU COM MÉTODO ESTATICO

Storage::putContent($file, $content);
```

### Método: **fileExists**
Localiza um arquivo no diretorio passado
```php
<?php
$file = 'teste.txt'; //Arquivo que está procurando
$path = 'pasta'; //Diretorio que está procurando o arquivo

$storage->fileExists($file, $path);

//OU COM MÉTODO ESTATICO

Storage::fileExists($file, $path);
```

### Método: **getImage**
Retorna o conteudo da imagem em base64 pronta para o html
```php
<?php
$file = 'teste.jpg'; //Imagem

$storage->getImage($file); // data:image/jpg;base64, /9j/4AA.....

//OU COM MÉTODO ESTATICO

Storage::getImage($file); // data:image/jpg;base64, /9j/4AA.....
```

## Variaveis de ambiente
```bash
//CONEXAO
PSTORAGE_DRIVER //Define o driver(ftp default)
PSTORAGE_HOST //Define o host da conexao
PSTORAGE_USER //Define o usuario da conexao com o host
PSTORAGE_PASS //Define a senha da conexao com o host
PSTORAGE_WORKDIR_LOCAL //Define o diretorio local a onde todas as ações serão execultadas por padrão.
PSTORAGE_WORKDIR_REMOTE //Define o diretorio remoto a onde todas as ações serão execultadas por padrão.

//CACHE
PSTORAGE_CACHE //Habilita o cache (true ou false)
PSTORAGE_CACHE_HOST //Define o host do serviço de cache
PSTORAGE_CACHE_PORT //Define a porta do serviço de cache;
PSTORAGE_CACHE_SECURITY //Habilita conexão segura com o serviço de cache
PSTORAGE_CACHE_LOGIN //Define a senha do serviço de cache;
PSTORAGE_CACHE_PASSWORD //Define a senha do serviço de cache
PSTORAGE_CACHE_TTL //Define o tempo de vida do cache
```