<?php
declare(strict_types = 1);
namespace Proner\Storage\Drivers;

interface DriversInterface
{
    public function connect(string $url);
    public function login(string $login, string $password);
    public function get($file, $path, $name);
    public function getContent($file);
    public function put(string $file, string $path, string $name);
    public function putContent($file, $content);
    public function fileExists($file, $path);
    public function delete(string $file);
    public function close();
}
