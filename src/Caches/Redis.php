<?php

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
    public function connect($host, $port, $security = null, $login = null, $password = null)
    {
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
     * @return string
     */
    public function get($key)
    {
        $value = base64_decode($this->redis->get($key));
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
    public function set($key, $value, $expire = null)
    {
        if (empty($expire)) {
            $this->redis->set($key, base64_encode($value));
        } else {
            $this->redis->setEx($key, $expire, base64_encode($value));
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return (boolean)$this->redis->del($key);
    }
}
