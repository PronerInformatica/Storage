<?php

namespace Proner\Storage\Caches;

interface CacheInterface
{
    public function connect($host, $port, $security, $login, $password);
    public function get($key);
    public function set($key, $value, $expire);
    public function delete($key);
}
