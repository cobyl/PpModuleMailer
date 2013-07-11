<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Console\Console;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * PpModuleMailer Module
 */
class Module implements ConsoleUsageProviderInterface
{

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(AdapterInterface $console){
        return array(
            'mailer process <name>'             => 'Process queue <name>',
        );
    }
         
    /**
     * Returns module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    
    public function getServiceConfig()
    {
        
        return array(
            'factories' => array(
                'PpModuleMailer' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Mailer());
                    $tableGateway =  new TableGateway($this->getConfig()['PpModuleMailer']['table'], $dbAdapter, null, $resultSetPrototype);
                    
                    //TODO inject config in more proper way?
                    $table = new Model\MailerTable($tableGateway,$this->getConfig()['PpModuleMailer']);                    
                    return new Service($table,$this->getConfig()['PpModuleMailer']);
                }
        ));
    }

    /**
     * Returns the Auto loader configuration for the module
     *
     * @return array
     */
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
    
    /**
     * Listen to the bootstrap event
     *
     * @param \Zend\EventManager\EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        // Listener have only sense when request is via http.
        if (!Console::isConsole()) {
            $em->attach($sm->get('AsseticBundle\Listener'));
        }
    }
    
}
