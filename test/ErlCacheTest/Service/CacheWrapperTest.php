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

    public function setUp()
    {
        $this->erlCache = \Bootstrap::getServiceManager()->get('MutexCache');
    }

    public function tearDown()
    {
        $this->erlCache = null;
    }

    /**
     * Тестирование удаления элемента
     */
    public function testRemoveItem()
    {
        $this->assertFalse($this->erlCache->removeItem(__FUNCTION__));
        $this->assertTrue($this->erlCache->addItem(__FUNCTION__, 'my val'));
        $this->assertTrue($this->erlCache->removeItem(__FUNCTION__));
    }

    /**
     * Тестирование удаления элементов
     */
    public function testRemoveAddItems()
    {
        $values = array(
            'key1' => 'val1',
            'key2' => 'val2',
        );

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
} 