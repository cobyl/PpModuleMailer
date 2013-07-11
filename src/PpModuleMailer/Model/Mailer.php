<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer\Model;

class Mailer {
    public $id;
    public $queue_name;
    
    /**
     *
     * @var \Zend\Mail\Message
     */
    public $mail;
    
    public $created;
    public $status;
    
    public function exchangeArray($data)
    {
       foreach($this as $key => $value) {
           if ($key !='mail')
            $this->$key       = (isset($data[$key]))      ? $data[$key]       : null;
           else 
               $this->$key = unserialize($data[$key]);
               
       }
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
