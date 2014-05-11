<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ErlCache\Controller\Mutex' => 'ErlCache\Controller\MutexController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'mutex_generate_map' => array(
                    'options' => array(
                        'route'    => 'mutex generate map',
                        'defaults' => array(
                            'controller' => 'ErlCache\Controller\Mutex',
                            'action'     => 'generateMap',
                        ),
                    ),
                ),
                'mutex_clear_profiler_log' => array(
                    'options' => array(
                        'route'    => 'mutex clear profiler log',
                        'defaults' => array(
                            'controller' => 'ErlCache\Controller\Mutex',
                            'action'     => 'clearProfilerLog',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'erl' => array(
        'cache' => array(
            'db' => array(
                'adapter'  => 'Zend\Db\Adapter\Adapter',
                'profiler' => 'mutex_profile',
                'logger'   => 'mutex_log',
            ),
        ),
    ),
);