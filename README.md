mutex-zf2-cache-wrapper
=====

PHP-Erlang Mutex (Zend Framework 2 cache wrapper module)

Реализация кеша в ZF2 с использованием mutex сервиса в виде отдельного модуля.
Класс является оберткой для использования любого вида кеширования.
Позволяет блокировать одновременное обращение к кешируемым областям кода, тем самым снижая нагрузку на сервер.

=====

##Установка zf2 модуля отдельно##

```json
{
    "name": "erl/ErlCache",
    "description": "Mutex ErlCache for ZF2",
    "license": "BSD-3-Clause",
    "require": {
        "php": ">=5.3.3",
        "zendframework/zendframework": "2.3.*",
        "twig/twig": "1.*",
        "leafo/lessphp": "0.4.0",
        "erl/mutex": "0.1.0"
    },
    "repositories": [{
        "type": "package",
        "package": {
            "name": "erl/mutex",
            "version": "0.1.0",
            "source": {
                "url": "https://github.com/syastrebov/mutex.git",
                "type": "git",
                "reference": "master"
            },
            "autoload": {
                "psr-0": {
                    "ErlMutex\\": "src/",
                    "ErlCache\\": "/"
                }
            }
        }
    }]
}
```

=====

##Установка для zf2 (установка вместе с zend skeleton) ##

```json
{
    "name": "zendframework/skeleton-application",
    "description": "Skeleton Application for ZF2",
    "license": "BSD-3-Clause",
    "keywords": [
        "framework",
        "zf2"
    ],
    "homepage": "http://framework.zend.com/",
    "require": {
        "php": ">=5.3.3",
        "zendframework/zendframework": "2.3.*",
        "twig/twig": "1.*",
        "leafo/lessphp": "0.4.0",
        "erl/mutex": "0.1.0",
        "erl/ErlCache": "0.1.0"
    },
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "erl/mutex",
                "version": "0.1.0",
                "source": {
                    "url": "https://github.com/syastrebov/mutex.git",
                    "type": "git",
                    "reference": "master"
                },
                "autoload": {
                    "psr-0": {
                        "ErlMutex\\": "src/"
                    }
                }
            }
        },
        {
            "type": "package",
            "package": {
                "name": "erl/ErlCache",
                "version": "0.1.0",
                "source": {
                    "url": "https://github.com/syastrebov/mutex-zf2-cache-wrapper.git",
                    "type": "git",
                    "reference": "master"
                },
                "autoload": {
                    "psr-0": {
                        "ErlCache\\": "../"
                    }
                }
            }
        }
    ]
}
```

=====

##Пример использования##


Настройка кеша, Application/Module.php:

```php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Cache as Cache;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

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
                'Cache' => function() {
                    return Cache\StorageFactory::factory(array(
                        'adapter' => array(
                            'name' => 'filesystem'
                        ),
                        'plugins' => array(
                            'exception_handler' => array(
                                'throw_exceptions' => true
                            ),
                        )
                    ));
                },
            ),
        );
    }
}
```

Application/Controller/IndexController.php:

```php
namespace Application\Controller;

use ErlCache\Service\CacheWrapper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /** @var CacheWrapper $mutexCache */
        $mutexCache = $this->getServiceLocator()->get('MutexCache');
        $key = 'A';

        $clear = (bool)$this->params()->fromQuery('clear', false);
        if ($clear) {
            if ($mutexCache->hasItem($key)) {
                $mutexCache->removeItem($key);
            }
        }
        if ($mutexCache->hasItem($key)) {
            var_dump('from cache');
            $value = $mutexCache->getItem($key);
        } else {
            var_dump('from db');
            $value = 'my val';
            sleep(10);
            $mutexCache->setItem($key, $value);
        }

        var_dump($value);
        return new ViewModel();
    }
}
```