<?php

namespace Proner\Storage\Drivers;

interface DriversInterface
{
    public function connect($url);
    public function login($login, $password);
    public function get($file, $path, $name);
    public function getContent($file);
    public function put($file, $path, $name);
    public function putContent($file, $content);
    public function fileExists($file, $path);
    public function close();
}
