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

use ErlMutex\Adapter\AdapterInterface;
use Zend\Cache\Storage\StorageInterface;

/**
 * Адаптер для zend cache
 *
 * Class ZendCache
 * @package ErlCache\Adapter
 */
class ZendCache implements AdapterInterface
{
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
}