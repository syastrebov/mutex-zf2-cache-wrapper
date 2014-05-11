<?php

namespace ErlCache\Controller;

use ErlMutex\Service\Mutex;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class MutexController
 * @package Application\Controller
 */
class ConsoleController extends AbstractActionController
{
    /**
     * Карта вызова блокировок
     */
    public function generateMapAction()
    {
        /** @var Mutex $mutexService */
        $mutexService = $this->getServiceLocator()->get('MutexService');
        $mutexService
            ->getProfiler()
            ->setMapOutputLocation('.')
            ->generateHtmlMapOutput();
    }

    /**
     * Очистить лог
     */
    public function clearProfilerLogAction()
    {
        /** @var Mutex $mutexService */
        $mutexService = $this->getServiceLocator()->get('MutexService');
        $mutexService
            ->getProfiler()
            ->getStorage()
            ->truncate();
    }
} 