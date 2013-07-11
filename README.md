PpModuleMailer
==============

Version 0.0.1 Created by Tomek Kobyliński

Introduction
------------

PpModuleMailer is a simple ZF2 module that allows you to create and send mail 
queue with sql database.

Installation
------------

### Main Setup

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "cobyl/PpModuleMailer": "master"
    }
    ```

2. Now tell composer to download __PpModuleMailer__ by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'PpModuleMailer',
        ),
        // ...
    );
    ```

2. Add table to database.

Please check sql/PpModuleMailer.mysql
This module needs database with enabled transactions. 

# How to use _PpModuleMailer_

Adding mail to queue "registration" from controller:

    ```php
    $mail = new \Zend\Mail\Message();
    $mail->addTo('to@domain.com');
    $mail->addFrom('from@domain.com');
    $mail->setSubject('Subject');
    $mail->setBody('Body');

    $this->getServiceLocator()->get('PpModuleMailer')->add('registration',$mail);
    ```

    or

    ```php
    $this->getServiceLocator()
        ->get('PpModuleMailer')
        ->addMail('registration','to@d.com','Subject','Body','from@d.com');
    ```

Sending queue "registration" from console:

    ```bash
    $ php public/index.php mailer process registration
    ```

# Configuration

The default configuration is setup to use the system SMTP configuration.
For other ways check module.config.php

That's it!
=======
PpModuleMailer is a simple Zend Framework 2 module that allows you to create mail queue in sql database.
