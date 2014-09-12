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

namespace ErlCache\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use ErlMutex\ProfilerStorageInterface;
use ErlMutex\Entity\Profiler\Stack as ProfilerStackEntity;
use DateTime;

/**
 * Хранилище карты вызова блокировок
 *
 * CREATE  TABLE `mutex_zf2`.`mutex_profile` (
 * `id` INT NOT NULL AUTO_INCREMENT ,
 * `request_uri` VARCHAR(255) NOT NULL ,
 * `request_hash` VARCHAR(255) NOT NULL ,
 * `filename` VARCHAR(255) NULL ,
 * `class` VARCHAR(255) NULL ,
 * `method` VARCHAR(255) NULL ,
 * `line` INT NULL ,
 * `key` VARCHAR(255) NULL ,
 * `action` VARCHAR(20) NULL ,
 * `response` VARCHAR(255) NULL ,
 * `datetime` DATETIME NULL ,
 * PRIMARY KEY (`id`) ;
 *
 * Class ProfilerStorage
 * @package Application\Mutex
 */
class ProfilerStorage implements ProfilerStorageInterface
{
    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * Constructor
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Очистить хранилище
     *
     * @return bool
     */
    public function truncate()
    {
        /** @var Adapter $adapter */
        $adapter = $this->tableGateway->getAdapter();
        $adapter->query('TRUNCATE TABLE '. $this->tableGateway->getTable(), Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Сохранить запись
     *
     * @param ProfilerStackEntity $model
     * @return bool
     */
    public function insert(ProfilerStackEntity $model)
    {
        return $this->tableGateway->insert(array(
            'request_uri'  => $model->getRequestUri(),
            'request_hash' => $model->getRequestHash(),
            'filename'     => $model->getFile(),
            'class'        => $model->getClass(),
            'method'       => $model->getMethod(),
            'line'         => $model->getLine(),
            'key'          => $model->getKey(),
            'action'       => $model->getAction(),
            'response'     => $model->getResponse(),
            'datetime'     => $model->getDateTimeFormat(),
        ));
    }

    /**
     * Получить список записей
     *
     * @return array
     */
    public function getList()
    {
        $result = array();
        $rowSet = $this->tableGateway->select();
        foreach ($rowSet as $row) {
            $result[] = new ProfilerStackEntity(
                $row['request_uri'],
                $row['request_hash'],
                $row['filename'],
                $row['line'],
                $row['class'],
                $row['method'],
                $row['key'],
                $row['action'],
                $row['response'],
                new DateTime($row['datetime'])
            );
        }

        return $result;
    }
}