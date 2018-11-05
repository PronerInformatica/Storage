# Storage

Cliente em PHP para comunicacao com o storage

## Exemplo
```php
<?php
use Proner\Storage\Storage;
$storage = new Storage('ftp');
$storage->setHost('172.20.15.999');
$storage->setLogin('usuario','senha');

$storage->setWorkdirLocal('arquivos');
$storage->setWorkdirRemote('arquivos');
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

## Variaveis de ambiente
Variavel para difinir o driver(ftp default)
```bash
PSTORAGE_DRIVER //ftp
```
Variavel para difinir o host da conexao
```
PSTORAGE_HOST //172.20.15.999
```
Variavel para difinir o usuario da conexao com o host
```
PSTORAGE_USER //user
```
Variavel para difinir a senha da conexao com o host
```
PSTORAGE_PASS //password
```
Variavel para difinir o diretorio local a onde todas as ações serão execultadas por padrão.
```
PSTORAGE_WORKDIR_LOCAL //arquivos
```
Variavel para difinir o diretorio remoto a onde todas as ações serão execultadas por padrão.
```
PSTORAGE_WORKDIR_REMOTE //arquivos
```