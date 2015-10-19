<?php
namespace Smart\Service;

use Smart\Model\ScheduledEmail;

/**
 * Service layer for fetching/storing email models from our persistence layer
 * Class Email
 * @package Smart\Service
 */
class Email
{

    const TABLE_NAME = 'scheduled_email';

    /**
     * Persistence adapter (should be swappable)
     * @var Adapter\AdapterInterface $adapter
     */
    protected $adapter;

    /**
     * Sender service (manager)
     * @var \Smart\Manager\Email $senderService
     */
    protected $senderService;

    /**
     * Persistence adapter
     * @param Adapter\AdapterInterface $adapter
     */
    public function __construct(Adapter\AdapterInterface $adapter)
    {
        // The idea here is that the service layer has no knowledge of the underlying storage mechanism
        // These adapters should be swappable.
        $this->adapter = $adapter;
    }

    /**
     * Process the queue of emails
     * @param \Smart\Manager\Email $emailManager
     * @param int $number
     */
    public function processSchedule(\Smart\Manager\Email $emailManager, $number = -1)
    {
        /** @var Adapter\PdoAdapter $adapter */
        $adapter = $this->adapter;
        $count = 0;
        try
        {
            foreach ($adapter->getAll(self::TABLE_NAME) as $scheduled_email)
            {
                $adapter->startTransaction();

                if ($number === 0)
                {
                    // Get out of here, we've done enough
                    break;
                }

                /** @var ScheduledEmail $scheduled_email */
                $scheduled_email = $this->hydrateToObject($scheduled_email);

                // Send this off to the 3rd party service
                echo "\033[32m Sending Scheduled Email: " . $scheduled_email->getRecipient() . "\033[0m" .  PHP_EOL;

                // If an exception is thrown, then the entire transaction should be rolled back
                if ($emailManager->send($scheduled_email))
                {
                    // Delete the entry after successful processing
                    $adapter->delete(self::TABLE_NAME, $scheduled_email->getId());
                }

                // We to do this explicitly as we're running an atomic operation.
                $adapter->commitTransaction();

                if ($number > -1)
                {
                    --$number;
                }
                ++$count;
            }
        } catch (\Exception $e)
        {
            // We should probably log some of this stuff!
            echo "\033[31m ERROR: processing the rest of the emails, they've not been removed from the DB. \033[0m" .  PHP_EOL;
            $adapter->rollbackTransaction();
        }
        echo PHP_EOL . "\033[32m .....Completed Processing " . $count . " Scheduled Email(s) \033[0m" .  PHP_EOL;
    }

    /**
     * Hydrate to an an object
     * @param $values
     * @return ScheduledEmail $email
     */
    protected function hydrateToObject($values)
    {
        $email = new ScheduledEmail($values['recipient']);
        $email->setDetails($values['subject'], $values['content']);
        $email->setId($values['id']);
        return $email;
    }

    /**
     * Save a scheduled email instance to the persistence later
     * @param ScheduledEmail $scheduledEmail
     */
    public function save(ScheduledEmail $scheduledEmail)
    {
        $this->adapter->insert(self::TABLE_NAME, $scheduledEmail->toArray());
    }

}