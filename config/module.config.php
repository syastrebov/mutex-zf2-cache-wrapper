<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Mutex' => 'Application\Controller\MutexController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'mutex_generate_map' => array(
                    'options' => array(
                        'route'    => 'mutex generate map',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Mutex',
                            'action'     => 'generateMap',
                        ),
                    ),
                ),
                'mutex_clear_profiler_log' => array(
                    'options' => array(
                        'route'    => 'mutex clear profiler log',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Mutex',
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