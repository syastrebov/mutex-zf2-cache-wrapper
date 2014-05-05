<?php


require __DIR__ . '/../vendor/autoload.php';

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * Class Bootstrap
 */
class Bootstrap
{
    /**
     * @var ServiceManager
     */
    private static $serviceManager;

    /**
     * Инициализация zf2
     */
    public static function init()
    {
        self::$serviceManager = new ServiceManager(new ServiceManagerConfig());
    }

    /**
     * Получить ссылку на сервис менеджер
     *
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
    }
}

Bootstrap::init();