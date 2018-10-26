# Storage

Cliente em PHP para comunicacao com o storage

## Exemplo
```php
<?php
use Proner\Storage\Storage;
$storage = new Storage();
$storage->setHost('172.20.15.999');
$storage->setLogin('usuario','senha');

$storage->get('Downloads/Nova/texte.txt');
$storage->put('texte.txt','Downloads');
$storage->put('texte.txt');
$storage->put('texte.txt','Downloads/Nova','texte2.txt');
$content = $storage->getContent('Storage/temp.txt');
$storage->put('conteudo','Storage/temp.txt');
```
## Exemplo Statico
Para user os recursos estaticos é preciso estar com as variaveis de ambiente definidas
```php
<?php
use Proner\Storage\Facades\Storage;
Storage::get('Downloads/Nova/texte.txt');
Storage::put('texte.txt','Downloads/Nova');
$content = Storage::getContent('temp.txt');
Storage::put('conteudo','Storage/temp.txt');
```

## Metodos
Metodo: **fileExists** localiza um arquivo no diretorio passado
```php
<?php
$file = 'teste.txt'; //Arquivo que está procurando
$path = 'pasta'; //Diretorio que está procurando o arquivo

$storage->fileExists($file, $path);

//OU
Storage::fileExists($file, $path);
```

## Variaveis de ambiente
Variavel para difinir o driver(ftp default)
```
PSTORAGE_DRIVER
```
Variavel para difinir o host da conexao
```
PSTORAGE_HOST
```
Variavel para difinir o usuario da conexao com o host
```
PSTORAGE_USER
```
Variavel para difinir a senha da conexao com o host
```
PSTORAGE_PASS
```
Variavel para difinir o diretorio local para o download dos arquivos
```
PSTORAGE_WORKDIR_LOCAL
```
Variavel para difinir o diretorio remoto para o download dos arquivos
```
PSTORAGE_WORKDIR_REMOTE
```