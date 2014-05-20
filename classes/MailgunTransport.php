<?php

namespace Codewrangler77\MailerMailgun;

use Mailgun\Mailgun;
use \Swift_Transport;
use \Swift_Mime_Message;
use \Swift_Events_EventListener;

/*
 * (c) 2014 Dave West <dave@unleashed-software.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Pretends messages have been sent, but just ignores them.
 *
 * @package mailer-mailgun 
 * @author  Dave West
 */
class MailgunTransport implements Swift_Transport
{
    private $api_key;
    private $domain;
    private $mailgun;



    /**
     * Constructor.
     */
    public function __construct($api_key, $domain) 
    {
        $this->api_key = $api_key;
        $this->domain = $domain;

        $this->mailgun = new Mailgun($this->api_key);
    }

    /**
     * Tests if this Transport mechanism has started.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Starts this Transport mechanism.
     */
    public function start()
    {
    }

    /**
     * Stops this Transport mechanism.
     */
    public function stop()
    {
    }

    /**
     * Sends the given message.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return integer The number of sent emails
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {

        $mgMessage = array(  
                'from'    => implode(',', array_keys($message->getFrom())), 
                 'subject' => $message->getSubject());


        $to = $message->getTo();
        if( is_array($to)) {
            $mgMessage['to']      = implode(',', array_keys($to));
        }

        $cc = $message->getCc();
        if( is_array($cc)) {
            $mgMessage['cc']      = implode(',', array_keys($cc));
        }

        $bcc = $message->getBcc();
        if( is_array($bcc)) {
            $mgMessage['bcc']      = implode(',', array_keys($bcc));
        }
         

        $contentType = $message->getContentType(); 

        if( $contentType == 'text/plain' ) {
            $mgMessage['text']  = $message->getBody();
        } else if( $contentType == 'text/html' ) {
            $mgMessage['html']  = $message->getBody();
        }


        $result = $this->mailgun->sendMessage($this->domain, $mgMessage);

        if( !empty($result->http_response_body->id )) {
            return 1;
        }  

        // How to handle errors here?
        return 0;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
    }
}
