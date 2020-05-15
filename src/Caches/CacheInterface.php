<?php
declare(strict_types = 1);
namespace Proner\Storage\Caches;

interface CacheInterface
{
    public function connect(string $host, int $port, string $security, string $login, string $password);
    public function get(string $key);
    public function set(string $key, string $value, string $expire);
    public function delete(string $key);
}
