<?php

namespace Proner\Storage\Drivers;

interface DriversInterface
{
    public function connect($url);
    public function login($login, $password);
    public function get($file, $path, $name, $absolutePath);
    public function put($file, $path, $name, $absolutePath);
    public function fileExists($file, $path);
    public function close();
}
