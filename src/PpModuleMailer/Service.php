<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer;

use \Zend\Mail\AddressList;
use \Zend\Mail\Message;
use \Zend\Mail\Transport;
use \Zend\Mail\Transport\Smtp;
use \Zend\Mail\Transport\SmtpOptions;

use \PpModuleMailer\Model\MailerTable;

class Service
{
    /**
     * @var Model\MailerTable
     */
    protected $table;

    protected $config;
    
    public function __construct(MailerTable $table, $config) {
        $this->table = $table;
        $this->config = $config;
    }
    
    /**
     * @param string $queue_name
     * @param \Zend\Mail\Message $mail
     */
    public function add($queue_name,\Zend\Mail\Message $mail) {
        $this->table->add($queue_name, $mail);
    }
    
    public function addMail($queue_name,$to,$subject,$body,$from=null) {
        $mail = new \Zend\Mail\Message();
        $mail->setTo($to);
        $mail->setSubject($subject);
        $mail->setBody($body);
        if ($from) $mail->setFrom($from);
        else $mail->setFrom($this->config['default_from']);
        $this->table->add($queue_name, $mail);
    }
 
    /**
     * @param string $queue_name
     */
    public function processQueue($queue_name) {
        $success = 0;
        $error = 0;
        
        $transport = new Smtp();
        if ($this->config['smtp'])
            $transport->setOptions(new SmtpOptions($this->config['smtp']));
        
        while ($mail = $this->table->getWaitingFromQueue($queue_name)) {
            $to = function() use ($mail) {
                $result = array();
                foreach ($mail->mail->getTo() as $tmp) {
                    /**
                     * @var $tmp \Zend\Mail\Address
                     */
                    $result[] = $tmp->getEmail();
                }
                return join('; ',$result);
            };
            try {
                $transport->send($mail->mail);
                $success++;
                file_put_contents('php://stdout', 'Mail sent to: '.$to()."\n",FILE_APPEND);
                $this->table->markAsSent($mail);
            }
            catch (\Exception $e) {
                $error++;
                file_put_contents('php://stderr', 'Error while sending e-mail to: '.$to()."\n",FILE_APPEND);
            }
        }    
        
        if ($success>0) file_put_contents('php://stdout', "Sent mails: ".$success."\n",FILE_APPEND);
        if ($error>0) file_put_contents('php://stderr', "Unsent mails: ".$error.".\n",FILE_APPEND);
    }
}

 
