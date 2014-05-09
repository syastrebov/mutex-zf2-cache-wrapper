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

namespace ErlCache\test\ErlCacheTest\Service;

use ErlCache\Service\CacheWrapper;
use ErlMutex\Service\Mutex;
use Zend\Cache\Storage\Adapter\Memcached;

/**
 * Class CacheWrapperTest
 * @package ErlCache\test\ErlCacheTest\Service
 */
class CacheWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheWrapper
     */
    private $erlCache;

    /**
     * Получить ссылку на объект кеша с использованием мьютекса
     * Используется memcached
     */
    public function setUp()
    {
        $options = array(
            'namespace' => 'erl-cache-test',
            'ttl'       => '2',
            'servers'   => array(
                array('localhost', 11211),
            ),
        );

        $this->erlCache = new CacheWrapper(new Memcached($options), new Mutex());
    }

    /**
     * Удалить ссылку на кеш
     */
    public function tearDown()
    {
        $this->erlCache = null;
    }

    /**
     * Тестирование удаления и добавления элемента
     */
    public function testRemoveItem()
    {
        $this->assertFalse($this->erlCache->removeItem(__FUNCTION__));
        $this->assertTrue($this->erlCache->addItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->removeItem(__FUNCTION__));
    }

    /**
     * Тестирование удаления и добавления элементов
     */
    public function testRemoveAddItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $this->erlCache->removeItems(array_keys($values));
        $this->assertEmpty($this->erlCache->addItems($values));
        $this->assertEmpty($this->erlCache->removeItems(array_keys($values)));
        $this->assertEmpty($this->erlCache->hasItems(array_keys($values)));
    }

    /**
     * Тестирование добавления элемента в кеш
     */
    public function testRemoveAddItem()
    {
        $this->erlCache->removeItem(__FUNCTION__);
        $this->assertFalse($this->erlCache->hasItem(__FUNCTION__));

        $this->assertTrue($this->erlCache->addItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->hasItem(__FUNCTION__));

        $this->assertFalse($this->erlCache->addItem(__FUNCTION__, 'my val'));
    }

    /**
     * Тестирование удаление и установка элемента
     */
    public function testRemoveSetItem()
    {
        $this->erlCache->removeItem(__FUNCTION__);
        $this->assertFalse($this->erlCache->hasItem(__FUNCTION__));

        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->hasItem(__FUNCTION__));

        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
    }

    /**
     * Тестирование удаления и добавления элементов
     */
    public function testRemoveSetItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $this->assertEmpty($this->erlCache->setItems($values));
        $this->assertEmpty($this->erlCache->setItems($values));
        $this->assertEmpty($this->erlCache->removeItems(array_keys($values)));
        $this->assertEmpty($this->erlCache->hasItems(array_keys($values)));
    }

    /**
     * Тестирование получения элемента
     */
    public function testGetItem()
    {
        $this->erlCache->removeItem(__FUNCTION__);
        $this->assertNull($this->erlCache->getItem(__FUNCTION__));
        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        $this->assertEquals('my val', $this->erlCache->getItem(__FUNCTION__));
    }

    /**
     * Тестирование получения элементов
     */
    public function testGetItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $this->assertEmpty($this->erlCache->getItems(array_keys($values)));
        $this->assertEmpty($this->erlCache->setItems($values));
        $this->assertEquals($values, $this->erlCache->getItems(array_keys($values)));
    }

    /**
     * Тестирование замены элемента
     */
    public function testReplaceItem()
    {
        $this->assertFalse($this->erlCache->removeItem(__FUNCTION__));
        $this->assertFalse($this->erlCache->replaceItem(__FUNCTION__, 'my val'));
        $this->assertFalse($this->erlCache->removeItem(__FUNCTION__));
        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->replaceItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->removeItem(__FUNCTION__));
    }

    /**
     * Тестирование замены элементов
     */
    public function testReplaceItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $this->erlCache->removeItems(array_keys($values));
        $this->assertEquals(array_keys($values), $this->erlCache->replaceItems($values));
        $this->assertEmpty($this->erlCache->setItems($values));
        $this->assertEmpty($this->erlCache->replaceItems($values));
        $this->assertEmpty($this->erlCache->removeItems(array_keys($values)));
    }

    /**
     * Тестирование сбрасывания времени жизни элемента
     */
    public function testTouchItem()
    {
        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->hasItem(__FUNCTION__));
        sleep(2);
        $this->assertFalse($this->erlCache->touchItem(__FUNCTION__));
        $this->assertFalse($this->erlCache->hasItem(__FUNCTION__));

        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        sleep(1);
        $this->assertTrue($this->erlCache->touchItem(__FUNCTION__));
        sleep(1);
        $this->assertTrue($this->erlCache->hasItem(__FUNCTION__));
    }

    /**
     * Тестирование сбрасывания времени жизни элементов
     */
    public function testTouchItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $this->erlCache->removeItems(array_keys($values));
        $this->assertEquals(array_keys($values), $this->erlCache->touchItems(array_keys($values)));
        $this->assertEmpty($this->erlCache->setItems($values));
        sleep(2);
        $this->assertEquals(array_keys($values), $this->erlCache->touchItems(array_keys($values)));
        $this->assertEmpty($this->erlCache->setItems($values));
        sleep(1);
        $this->assertEmpty($this->erlCache->touchItems(array_keys($values)));
        sleep(1);
        $this->assertEquals(array_keys($values), $this->erlCache->hasItems(array_keys($values)));
    }

    /**
     * Тестирование увеличения значения элемента
     */
    public function testIncrementItem()
    {
        $this->assertEquals(5, $this->erlCache->incrementItem(__FUNCTION__, 5));
        $this->assertEquals(10, $this->erlCache->incrementItem(__FUNCTION__, 5));
    }

    /**
     * Тестирование увеличения значения нечислового элемента
     *
     * @expectedException \Zend\Cache\Exception\RuntimeException
     */
    public function testIncrementNotNumberItem()
    {
        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val'));
        $this->erlCache->incrementItem(__FUNCTION__, 5);
    }

    /**
     * Тестирование увеличения значения нечисловых элементов
     */
    public function testIncrementNotNumberItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 'val1',
            __FUNCTION__ . 'key2' => 'val2',
        );

        $expected = array(
            __FUNCTION__ . 'key1' => 0,
            __FUNCTION__ . 'key2' => 0,
        );

        $this->erlCache->incrementItems($values);
        $this->assertEquals($expected, $this->erlCache->getItems(array_keys($values)));
    }

    /**
     * Тестирование увеличения значения нечисловых элементов
     */
    public function testIncrementItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 5,
            __FUNCTION__ . 'key2' => 5,
        );

        $this->erlCache->incrementItems($values);
        $this->assertEquals(5, $this->erlCache->getItem(__FUNCTION__ . 'key1'));
    }

    /**
     * Тестирование уменьшения значения элемента
     */
    public function testDecrementItem()
    {
        $this->assertEquals(-5, $this->erlCache->decrementItem(__FUNCTION__, 5));
    }

    /**
     * Тестирование уменьшения значения элементов
     */
    public function testDecrementItems()
    {
        $values = array(
            __FUNCTION__ . 'key1' => 5,
            __FUNCTION__ . 'key2' => 5,
        );

        $expected = array(
            __FUNCTION__ . 'key1' => -5,
            __FUNCTION__ . 'key2' => -5,
        );

        $this->assertEquals($expected, $this->erlCache->decrementItems($values));
    }

    /**
     * Тестирование метода установки элемента, если он не был изменен
     */
    public function testCheckAndSetItem()
    {
        $success = false;
        $token   = null;

        $this->assertNull($this->erlCache->getItem(__FUNCTION__, $success, $token));
        $this->assertNull($token);

        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val 1'));
        $this->assertEquals('my val 1', $this->erlCache->getItem(__FUNCTION__, $success, $token));

        $this->assertTrue($this->erlCache->setItem(__FUNCTION__, 'my val 2'));
        $this->assertFalse($this->erlCache->checkAndSetItem($token, __FUNCTION__, 'my val'));

        $this->assertEquals('my val 2', $this->erlCache->getItem(__FUNCTION__, $success, $token));
        $this->assertTrue($this->erlCache->checkAndSetItem($token, __FUNCTION__, 'my val'));
    }

    /**
     * Сброс незанятой блокировки
     *
     * @expectedException \Exception
     */
    public function testReleaseNotAcquired()
    {
        $this->callPrivateMethod($this->erlCache, 'release', __FUNCTION__);
    }

    /**
     * Тестируем приватные методы
     *
     * @param object $object
     * @param string $methodName
     *
     * @return mixed
     */
    private function callPrivateMethod($object, $methodName)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        $params = array_slice(func_get_args(), 2);
        return $reflectionMethod->invokeArgs($object, $params);
    }
} 