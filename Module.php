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

namespace ErlCache;

use ErlCache\Adapter\ZendCache;
use ErlCache\Model\Logger;
use ErlCache\Model\ProfilerStorage;
use ErlCache\Service\CacheWrapper;
use ErlMutex\Adapter\Dummy;
use ErlMutex\Adapter\Memcached;
use ErlMutex\Adapter\Socket;
use ErlMutex\Service\Mutex;
use ErlMutex\Service\Profiler;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\Cache as Cache;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'MutexService' => function(ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $config = $config['erl']['cache'];

                    /** @var Request $request */
                    $request   = $serviceManager->get('Request');
                    /** @var Adapter $dbAdapter */
                    $dbAdapter = $serviceManager->get($config['db']['adapter']);

                    /** @var Cache\Storage\StorageInterface $cache */
                    $cache = $serviceManager->get('Cache');

                    $mutexAdapter = new Dummy();
                    switch ($config['adapter']) {
                        case 'memcached':
                            $mutexAdapter = new Memcached(new \Memcached());
                            break;
                        case 'socket':
                            $mutexAdapter = new Socket();
                            break;
                        case 'zend_cache':
                            $mutexAdapter = new ZendCache($cache);
                            break;
                    }

                    $requestUri = $request instanceof Request ? $request->getUriString() : 'console';
                    $mutex = new Mutex($mutexAdapter);
                    $mutex
                        ->setLogger(new Logger(new TableGateway($config['db']['logger'], $dbAdapter)))
                        ->establishConnection();

                    if ($config['profiler']) {
                        $mutex
                            ->setProfiler(new Profiler($requestUri))
                            ->getProfiler()
                            ->setStorage(new ProfilerStorage(new TableGateway($config['db']['profiler'], $dbAdapter)));
                    }

                    return $mutex;
                },
                'MutexCache' => function(ServiceManager $serviceManager) {
                    /** @var Cache\Storage\StorageInterface $cache */
                    $cache = $serviceManager->get('Cache');
                    /** @var Mutex $mutex */
                    $mutex = $serviceManager->get('MutexService');

                    return new CacheWrapper($cache, $mutex);
                },
            ),
        );
    }

    public function getConsoleUsage()
    {
        return [
            'mutex generate map'       => 'Создание карты вызова блокировок',
            'mutex clear profiler log' => 'Очистка таблицы профайлера',
        ];
    }
} 