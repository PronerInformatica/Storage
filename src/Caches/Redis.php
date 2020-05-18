<?php
declare(strict_types = 1);
namespace Proner\Storage\Caches;

class Redis implements CacheInterface
{
    private $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
    }

    /**
     * @param string $host
     * @param integer $port
     * @param string $security
     * @param string $login
     * @param string $password
     */
    public function connect(
        string $host,
        int $port,
        string $security = null,
        string $login = null,
        string $password = null
    ) {
        if (!empty($security)) {
            $this->redis->connect("tls://".$host, $port);
        } else {
            $this->redis->connect($host, $port);
        }
        if (!empty($password)) {
            $this->redis->auth($password);
        }
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function get(string $key)
    {
        $value = base64_decode((string)$this->redis->get($key));
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $expire
     */
    public function set(string $key, string $value, string $expire = null)
    {
        if (empty($expire)) {
            $this->redis->set($key, base64_encode((string)$value));
        } else {
            $this->redis->setEx($key, $expire, base64_encode((string)$value));
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key)
    {
        return (boolean)$this->redis->del($key);
    }
}
