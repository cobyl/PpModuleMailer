<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController 
{

    public function processAction()
    {
        $queue = $this->getRequest()->getParam('queue');
        file_put_contents('php://stdout', 'Processing queue '.$queue.".\n",FILE_APPEND);
        
        $mailer = $this->getServiceLocator()->get('PpModuleMailer');
        $mailer->processQueue($queue);
    }
}