<?php

// Set the default timezone
date_default_timezone_set('Europe/London');



require '../vendor/autoload.php';
require 'helper.php';


$service = new \Smart\Service\Email(
    new \Smart\Service\Adapter\PdoAdapter(parse_ini_file('../conf/db.ini'))
);


$options = parseProcessCliArguments();
$service->processSchedule(
    new \Smart\Manager\Email(parse_ini_file('../conf/mandrill.ini')),
    $options['number']
);


/**
 * Print out help information for CLI
 */
function printHelp()
{
    echo "\033[33m";
    echo <<<EOF
\n
******************************************************************************
**                        TOOL TO PROCESS SCHEDULED EMAILS                  **
**                        CheckoutSmart                                     **
**                        Build: 0.0.1alpha                                 **
******************************************************************************

Usage: php cli.php [-m]<months> [options]

    -n, --number            Number of emails to send
    -h, --help              Show this help menu

Examples:

    // Process five scheduled emails
    php process.php -n5
\n
EOF;
    echo "\033[0m";
}