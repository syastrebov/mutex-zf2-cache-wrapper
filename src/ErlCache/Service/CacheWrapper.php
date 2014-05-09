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

namespace ErlCache\Service;

use ErlMutex\Service\Mutex;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\StorageInterface;
use Traversable;
use Exception;

/**
 * Обертка для кеша с использованием mutex'a
 *
 * Class MutexCache
 * @package Application\Mutex
 */
class CacheWrapper implements StorageInterface
{
    /**
     * Текущие заблокированные ключи
     *
     * @var array
     */
    private $acquiredKeys = array();

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $storage;

    /**
     * @var \ErlMutex\Service\Mutex
     */
    private $mutex;

    /**
     * Constructor
     *
     * @param StorageInterface $storage
     * @param Mutex            $mutex
     */
    public function __construct(StorageInterface $storage, Mutex $mutex)
    {
        $this->storage = $storage;
        $this->mutex   = $mutex;
    }

    /**
     * Set options.
     *
     * @param array|Traversable $options
     * @return StorageInterface Fluent interface
     */
    public function setOptions($options)
    {
        $this->storage->setOptions($options);
        return $this;
    }

    /**
     * Get options
     *
     * @return object
     */
    public function getOptions()
    {
        return $this->storage->getOptions();
    }

    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool    $success
     * @param  mixed   $casToken
     *
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $this->acquire($key);
        $item = $this->storage->getItem($key, $success, $casToken);
        if ($success) {
            $this->release($key);
        }

        return $item;
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItems(array $keys)
    {
        $mutexKey = $this->getArrayMutexKey($keys);

        $this->acquire($mutexKey);
        $items = $this->storage->getItems($keys);
        $this->release($mutexKey);

        return $items;
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItem($key)
    {
        $this->acquire($key);
        return $this->storage->hasItem($key);
    }

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @return array Array of found keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItems(array $keys)
    {
        $this->acquire($this->getArrayMutexKey($keys));
        return $this->storage->hasItems($keys);
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItem($key, $value)
    {
        $this->acquire($key);
        $result= $this->storage->setItem($key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItems(array $keyValuePairs)
    {
        $mutexKey = $this->getArrayMutexKey($keyValuePairs);

        $this->acquire($mutexKey);
        $result = $this->storage->setItems($keyValuePairs);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItem($key, $value)
    {
        $this->acquire($key);
        $result = $this->storage->addItem($key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItems(array $keyValuePairs)
    {
        $mutexKey = $this->getArrayMutexKey($keyValuePairs);

        $this->acquire($mutexKey);
        $result = $this->storage->addItems($keyValuePairs);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItem($key)
    {
        $this->acquire($key);
        $result = $this->storage->removeItem($key);
        $this->release($key);

        return $result;
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItems(array $keys)
    {
        $mutexKey = $this->getArrayMutexKey($keys);

        $this->acquire($mutexKey);
        $result = $this->storage->removeItems($keys);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItem($key, $value)
    {
        $this->acquire($key);
        $result = $this->storage->replaceItem($key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItems(array $keyValuePairs)
    {
        $mutexKey = $this->getArrayMutexKey($keyValuePairs);

        $this->acquire($mutexKey);
        $result = $this->storage->replaceItems($keyValuePairs);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key)
    {
        $this->acquire($key);
        $result = $this->storage->touchItem($key);
        $this->release($key);

        return $result;
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItems(array $keys)
    {
        $this->acquire(serialize($keys));
        $result = $this->storage->touchItems($keys);
        $this->release(serialize($keys));

        return $result;
    }

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItem($key, $value)
    {
        $this->acquire($key);
        $result = $this->storage->incrementItem($key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItems(array $keyValuePairs)
    {
        $mutexKey = $this->getArrayMutexKey($keyValuePairs);

        $this->acquire($mutexKey);
        $result = $this->storage->incrementItems($keyValuePairs);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItem($key, $value)
    {
        $this->acquire($key);
        $result = $this->storage->decrementItem($key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItems(array $keyValuePairs)
    {
        $mutexKey = $this->getArrayMutexKey($keyValuePairs);

        $this->acquire($mutexKey);
        $result = $this->storage->decrementItems($keyValuePairs);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value)
    {
        $this->acquire($key);
        $result = $this->storage->checkAndSetItem($token, $key, $value);
        $this->release($key);

        return $result;
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @return array|bool Metadata on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadata($key)
    {
        $this->acquire($key);
        $result = $this->storage->getMetadata($key);
        $this->release($key);

        return $result;
    }

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @return array Associative array of keys and metadata
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadatas(array $keys)
    {
        $mutexKey = serialize(array_keys($keys));

        $this->acquire($mutexKey);
        $result = $this->storage->getMetadatas($keys);
        $this->release($mutexKey);

        return $result;
    }

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
        return $this->storage->getCapabilities();
    }

    /**
     * Текущие ключи блокировок
     *
     * @return array
     */
    public function getActiveKeys()
    {
        return $this->acquiredKeys;
    }

    /**
     * Получить ключ для массива
     *
     * @param array $keys
     * @return string
     */
    private function getArrayMutexKey(array $keys)
    {
        return md5(serialize(array_keys($keys)));
    }

    /**
     * Установить блокировку
     *
     * @param string $key
     */
    private function acquire($key)
    {
        if (!in_array($key, $this->acquiredKeys, true)) {
            $this->mutex->get($key);
            $this->mutex->acquire($key);

            $this->acquiredKeys[] = $key;
        }
    }

    /**
     * Снять блокировку
     *
     * @param string $key
     * @throws \Exception
     */
    private function release($key)
    {
        if (in_array($key, $this->acquiredKeys, true)) {
            foreach ($this->acquiredKeys as $existKeyNum => $existKey) {
                if ($existKey === $key) {
                    unset($this->acquiredKeys[$existKeyNum]);
                }
            }

            $this->mutex->release($key);
        } else {
            throw new Exception(sprintf('Невозможно снять незанятую блокировку `%s`', $key));
        }
    }
} 