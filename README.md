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