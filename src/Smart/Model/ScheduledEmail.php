<?php

namespace Smart\Model;

/**
 * Class ScheduledEmail
 */
class ScheduledEmail
{

    /**
     * A unique identifier
     * @var int $id
     */
    protected $id;

    /**
     * The email to be used
     * @var $email_address
     */
    protected $email_address;

    /**
     * The subject to be emailed
     * @var string $subject
     */
    protected $subject;

    /**
     * The content to be emailed
     * @var string $content
     */
    protected $content;

    /**
     * @param string $email_address
     * @throws \Exception
     */
    public function __construct($email_address)
    {
        if (!is_string($email_address))
        {
            throw new \Exception('Given email address is not a string. ' .
                'I can\'t wait for PHP7 where I don\'t have to defensively code like this anymore');
        }
        if (!filter_var($email_address, FILTER_VALIDATE_EMAIL))
        {
            throw new \Exception('The email address "' . $email_address . '" is invalid');
        }
        $this->email_address = $email_address;
    }

    /**
     * Set the details to be used when dispatching this email
     * @param $subject
     * @param $content
     */
    public function setDetails($subject, $content)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Get the recipient
     * @return string
     */
    public function getRecipient()
    {
        return $this->email_address;
    }

    /**
     * Set a unique identifier for this record
     * @param $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Get the unique ID
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return an array representation of this object
     * @return array
     */
    public function toArray()
    {
        return array(
            'recipient' => $this->email_address,
            'subject' => $this->subject,
            'content' => $this->content
        );
    }
}