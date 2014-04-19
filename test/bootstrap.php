<?php

chdir(dirname(__DIR__ . '/../../../../public'));

require __DIR__ . '/../../../../init_autoloader.php';

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
        self::$serviceManager
            ->setService('ApplicationConfig', include __DIR__ . '/../../../../config/application.config.php')
            ->get('ModuleManager')
            ->loadModules();
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