<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

return array(
    'PpModuleMailer' => array(
        'table'=>'mailer',
        'default_from'=>'default@domain.com',
//        //Zend\Mail\Transport\SmtpOptions        
//        'smtp'=> array(
//            'name'              => 'localhost.localdomain',
//            'host'              => '127.0.0.1',
//            'connection_class'  => 'plain',
//            'connection_config' => array(
//                'username' => 'user',
//                'password' => 'pass',
//            ),
//         )         
    ),
    
    'controllers' => array(
        'invokables' => array(
            'PpModuleMailer\Controller\Console' => 'PpModuleMailer\Controller\ConsoleController',
        ),
    ),
    
    
    'console' => array(
        'router' => array(
            'routes' => array(
                'my-first-route' => array(
                    'options' => array(
                        'route'    => 'mailer process <queue>',
                        'defaults' => array(
                            'controller' => 'PpModuleMailer\Controller\Console',
                            'action'     => 'process'
                        )
                    )
                )
                
            )
        )
    ),
    
);
