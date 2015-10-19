Here is the test for you. Please spend no more than about 2 hours on it and feel free to put comments / tests in where 
you might improve on certain functionality if you had more time to do so. 
Hopefully you can learn something from the test as I've tried to keep it interesting by integrating with the 
3rd party Mandrill API that we already use for our email services. This is the back-end that drives MailChimp 
if you are not familiar with it.

Instructions
------------

run composer install

    php composer.phar install

Set up a database with the following schematic:

    
    CREATE DATABASE IF NOT EXISTS `smart` /*!40100 DEFAULT CHARACTER SET utf8 */;
    
    USE `smart`;
    
    DROP TABLE IF EXISTS `scheduled_email`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!40101 SET character_set_client = utf8 */;
    CREATE TABLE `scheduled_email` (
      `id` int(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `recipient` VARCHAR(255) NOT NULL,
      `subject` VARCHAR(255) NOT NULL DEFAULT '',
      `content` TEXT NOT NULL DEFAULT ''
      COLLATE utf8_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    /*!40101 SET character_set_client = @saved_cs_client */;


Ensure the DB credentials are in the conf/db.ini file

Run the populate tool to add some records to the DB (this currently just uses my email address)

    php bin/populate.php --help
    
    php bin/populate.php -n5


Run the process tool to send the records to the Madrill App

    php bin/process.php --help
    
    // Process them all
    php bin/process.php
    
    // Process just 5
    php bin/process.php -n5


That's it.


Considerations
--------------


- No frameworks used. As this was solely a CLI task, I thought the use of a framework was an unnecessary overhead.
- Processing is completely atomic, it'll fail or succeed. DB should never be left in an unstable state.

To test this add an exception into Smart\Service\Email after a certain number of records have been processed. Eg.

                if ($count == 3)
                {
                    throw new \Exception('enough!');
                }
                
You'll notice that the delete statements are rolled back, and the record will remain, and can be reprocessed. 
This will have an effect should the Mandrill API SDK throw an exception at any point. Notice the DB is of engine type InnoDB.
                

- I considered adding a count flag to see how many emails were in the queue, but figured this was outside the scope of the test
- I considered adding unit tests, however noticed that I'd already gone over the dedicated 2 hour mark.
- Hesitated putting db.ini and mandrill.ini configuration settings into the codebase (sensitive credentials). 
However for the sake of this test I figured it was acceptable
- I considered adding a 'throttle' option in the process cli tool, but felt it unnecessary and part of the Madrill API remit.