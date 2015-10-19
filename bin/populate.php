<?php
/**
 * Send in some entries to the database
 * Quick and dirty, this isn't really part of the test, just a requirement to get up and running
 */

// Set the default timezone
date_default_timezone_set('Europe/London');

require '../vendor/autoload.php';
require 'helper.php';


$service = new \Smart\Service\Email(
    new \Smart\Service\Adapter\PdoAdapter(parse_ini_file('../conf/db.ini'))
);

$options = parsePopulateCliArguments();
while($options['number'] > 0)
{
    $scheduledEmail = new \Smart\Model\ScheduledEmail('leedavis81@hotmail.com');
    $scheduledEmail->setDetails(
        'smart subject line',
        'smart content'
    );
    --$options['number'];
    $service->save($scheduledEmail);
}


/**
 * Print out help information for CLI
 */
function printHelp()
{
    echo "\033[33m";
    echo <<<EOF
\n
******************************************************************************
**                        TOOL TO POPULATE SCHEDULED EMAILS                 **
**                        CheckoutSmart                                     **
**                        Build: 0.0.1alpha                                 **
******************************************************************************

Usage: php cli.php [-m]<months> [options]

    -n, --number            Number of emails to populate
    -h, --help              Show this help menu

Examples:

    // Add 5 dummy entries
    php populate.php -n5
\n
EOF;
    echo "\033[0m";
}