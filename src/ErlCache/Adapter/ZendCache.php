<?php

/**
 * PHP-Erlang erl
 * Сервис блокировок для обработки критических секций
 *
 * @category erl
 * @package  erl
 * @author   Sergey Yastrebov <serg.yastrebov@gmail.com>
 * @link     https://github.com/syastrebov/erl
 */

namespace ErlCache\Adapter;

use ErlMutex\Adapter\AbstractCache;
use Zend\Cache\Storage\StorageInterface;

/**
 * Адаптер для zend cache
 *
 * Class ZendCache
 * @package ErlCache\Adapter
 */
class ZendCache extends AbstractCache
{
    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $adapter;

    /**
     * Constructor
     *
     * @param StorageInterface $adapter
     */
    public function __construct(StorageInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Установить значение в кеш
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $timeout
     *
     * @return bool
     */
    protected function setCache($key, $value, $timeout = null)
    {
        return $this->adapter->setItem($key, $value);
    }

    /**
     * Получить значение из кеша
     *
     * @param string $key
     * @return mixed
     */
    protected function getCache($key)
    {
        return $this->adapter->getItem($key);
    }

    /**
     * Удалить значение из кеша
     *
     * @param $key
     * @return bool
     */
    protected function deleteCache($key)
    {
        return $this->adapter->removeItem($key);
    }
}