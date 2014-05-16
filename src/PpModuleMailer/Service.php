<?php
/**
 * PpModuleMailer
 *
 * @link      https://github.com/cobyl/PpModuleMailer
 * @copyright Copyright (c) www.pracowici-programisci.pl
 */

namespace PpModuleMailer;

use PpModuleMailer\Model\MailerTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mail\Message;
use Zend\Mail\Transport;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Service
{
    /**
     * @var Model\MailerTable
     */
    protected $table;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translate = null;

    /**
     * @var array
     */
    protected $config;

    public function __construct(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->config = $sm->get('config')['PpModuleMailer'];

        if ($sm->has('translator')) $this->translate = $sm->get('translator');

        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model\Mailer());
        $tableGateway = new TableGateway($this->config['table'], $dbAdapter, null, $resultSetPrototype);

        //TODO inject config in more proper way?
        $this->table = new Model\MailerTable($tableGateway, $this->config);
    }
    
    /**
     * @param string $queue_name
     * @param \Zend\Mail\Message $mail
     */
    public function add($queue_name, \Zend\Mail\Message $message)
    {
        if (!count($message->getFrom())) $message->addFrom($this->config['default_from']);
        $this->table->add($queue_name, $message);
    }

    /**
     * @param string $queue_name
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param null $from
     * @param true $html
     */
    public function addMail($queue_name,$to,$subject,$body,$from=null,$html=false) {
        $message = new \Zend\Mail\Message();
        $message->setTo($to);
        $message->setSubject(($this->translate ? $this->translate->translate($subject) : $subject));

        if($html){
            $bodyPart = new \Zend\Mime\Message();
            $bodyMessage = new \Zend\Mime\Part($body);
            $bodyMessage->type = 'text/html';
            $bodyPart->setParts(array($bodyMessage));
            $message->setBody($bodyPart);
        } else {
            $message->setBody($body);
        }

        $message->setEncoding("UTF-8");
        if ($from) $message->setFrom($from);
        else $message->setFrom($this->config['default_from']);
        $this->table->add($queue_name, $message);
    }
 
    /**
     * @param string $queue_name
     */
    public function processQueue($queue_name) {
        $success = 0;
        $error = 0;
        
        $transport = new Smtp();
        if (isset($this->config['smtp']))
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
                //TODO process each To: separately
                $transport->send($mail->mail);
                $success++;
                file_put_contents('php://stdout', 'Mail sent to: '.$to()."\n",FILE_APPEND);
                $this->table->markAsSent($mail);
            }
            catch (\Exception $e) {
                $error++;
                $this->table->markAsError($mail);
                file_put_contents('php://stderr', 'Error while sending e-mail to: ' . $to() . " (" . $e->getCode() . " - " . $e->getMessage() . ")\n", FILE_APPEND);
            }
        }    
        
        if ($success>0) file_put_contents('php://stdout', "Sent mails: ".$success."\n",FILE_APPEND);
        if ($error>0) file_put_contents('php://stderr', "Unsent mails: ".$error.".\n",FILE_APPEND);
    }
}

 
