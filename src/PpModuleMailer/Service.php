<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer;

class Service
{    
    protected $table;
    protected $config;
    
    public function __construct(\PpModuleMailer\Model\MailerTable $table, $config) {
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
     * @return type
     */
    public function processQueue($queue_name) {
        $success = 0;
        $error = 0;
        
        $transport = new \Zend\Mail\Transport\Smtp();
        if ($this->config['smtp'])
            $transport->setOptions(new SmtpOptions($this->config['smtp']));
        
        while ($mail = $this->table->getWaitingFromQueue($queue_name)) {
            $to = function() use ($mail) {
                foreach ($mail->mail->getTo() as $tmp) {
                    return $tmp->getEmail().'; ';
                }                
            };
            try {
                $transport->send($mail->mail);
                $success++;
                file_put_contents('php://stdout', 'Mail sent to: '.$to()."\n",FILE_APPEND);
            }
            catch (\Exception $e) {
                $error++;
                file_put_contents('php://stderr', 'Error while sending e-mail to: '.$to()."\n",FILE_APPEND);
            }
        }    
        
        if ($success>0) file_put_contents('php://stdout', $success." mail sended.\n",FILE_APPEND);
        if ($error>0) file_put_contents('php://stderr', $error." mails not sended.\n",FILE_APPEND);
    }
}

 
