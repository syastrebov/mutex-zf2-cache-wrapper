mutex-zf2-cache-wrapper
=====

PHP-Erlang Mutex (Zend Framework 2 cache wrapper module)

Реализация кеша в ZF2 с использованием mutex сервиса в виде отдельного модуля.
Класс является оберткой для использования любого вида кеширования.
Позволяет блокировать одновременное обращение к кешируемым областям кода, тем самым снижая нагрузку на сервер.
