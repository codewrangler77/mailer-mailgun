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
                 'subject' => $message->getSubject());

        $fromHeader = $message->getHeaders()->get('From');
        $toHeader = $message->getHeaders()->get('To');
        $ccHeader = $message->getHeaders()->get('Cc');
        $bccHeader = $message->getHeaders()->get('Bcc');
        $subjectHeader = $message->getHeaders()->get('Subject');

        if (!$toHeader) {
            throw new Swift_TransportException(
                'Cannot send message without a recipient'
            );
        }

        $mgMessage['from'] = $fromHeader->getFieldBody();

        if( !empty($toHeader) ) {
            $to = $toHeader->getFieldBody();
            if(!empty($to)) {
                $mgMessage['to'] = $to;
            } 
        }

        if( !empty($ccHeader) ) {
            $cc = $ccHeader->getFieldBody();
            if(!empty($cc)) {
                $mgMessage['cc'] = $cc;
            } 
        }

        if( !empty($bccHeader) ) {
            $bcc = $bccHeader->getFieldBody();
            if(!empty($bcc)) {
                $mgMessage['bcc'] = $bcc;
            } 
        }


        $mgMessage['subject'] = $subjectHeader ? $subjectHeader->getFieldBody() : '';


        if( $message->getContentType() == 'text/html' ) {
            $mgMessage['html']  = $message->getBody();
        }

        $mgMessage['text']  = $message->toString();


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
