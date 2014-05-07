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
            'key1' => 'val1',
            'key2' => 'val2',
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
            'key1' => 'val1',
            'key2' => 'val2',
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
            'key1' => 'val1',
            'key2' => 'val2',
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
            'key1' => 'val1',
            'key2' => 'val2',
        );

        $this->erlCache->removeItems(array_keys($values));
        $this->assertEquals(array_keys($values), $this->erlCache->replaceItems($values));
        $this->assertEmpty($this->erlCache->setItems($values));
        $this->assertEmpty($this->erlCache->replaceItems($values));
        $this->assertEmpty($this->erlCache->removeItems(array_keys($values)));
    }
} 