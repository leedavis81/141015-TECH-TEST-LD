<?php
namespace Smart\Manager;

use Mandrill;
use Smart\Model\ScheduledEmail;

class Email
{

    /**
     * @var Mandrill $mandrillClient
     */
    protected $mandrillClient;

    /**
     * A configuration array for mandrill
     * @var array $config
     */
    protected $config;

    public function __construct($config)
    {
        if (!isset($config['key']))
        {
            throw new \Exception('You need an API Key for mandrill');
        }
        $this->config = $config;
    }


    /**
     * Send a scheduled email. Let exceptions bubble up to allow DB transaction to be rolled back
     * @param ScheduledEmail $scheduledEmail
     * @return bool - true on success
     */
    public function send(ScheduledEmail $scheduledEmail)
    {
        $details = $scheduledEmail->toArray();
        $message = array(
            'to' => array(
                array(
                    'email' => $details['recipient'],
                    'name' => $details['recipient'],
                    'type' => 'to'
                )
            ),
            'from_email' => (isset($this->config['from_email'])) ? $this->config['from_email'] : 'default_from@address.com',
            'subject' => $details['subject'],
            'content' => $details['content']
        );

        // Happy to leave the other options as defaults. IP pool, time to send etc are outside scope of this test.
        $response = $this->getMandrillClient()->messages->send($message);

        return ($response[0]['status'] === 'sent' || $response[0]['status'] === 'queued')
            ? true
            : false;
    }

    /**
     * Get the mandrill client
     * @return Mandrill
     */
    public function getMandrillClient()
    {
        if (!$this->mandrillClient instanceof Mandrill)
        {
            $this->mandrillClient = new Mandrill($this->config['key']);
        }
        return $this->mandrillClient;
    }
}