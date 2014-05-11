<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ErlCache\Controller\Console' => 'ErlCache\Controller\ConsoleController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'mutex_generate_map' => array(
                    'options' => array(
                        'route'    => 'mutex generate map',
                        'defaults' => array(
                            'controller' => 'ErlCache\Controller\Console',
                            'action'     => 'generateMap',
                        ),
                    ),
                ),
                'mutex_clear_profiler_log' => array(
                    'options' => array(
                        'route'    => 'mutex clear profiler log',
                        'defaults' => array(
                            'controller' => 'ErlCache\Controller\Console',
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