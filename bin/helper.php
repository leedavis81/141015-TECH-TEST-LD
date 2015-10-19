<?php

/**
 *  MISC FUNCTIONS FOR HANDLING THE DATA POPULATION
 *  This doesn't need to be abstracted into classes at this point, it's simple convenience methods, out of test scope
 *  @todo: This would be tidied up or binned for a production environment
 *
 */

/**
 * Create a random string
 * @param $length
 * @param string $characters
 * @return string
 */
function getRandomString($length, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Parse the CLI arguments and get the required number of entries
 * @return array
 */
function parsePopulateCliArguments()
{
    $parameters = array();
    $shortOptions = 'n::h::';
    $longOptions = array(
        'number::',
        'help::'
    );

    $options = getopt($shortOptions, $longOptions);
    if (isset($options['h']) || isset($options['help']) || empty($options))
    {
        printHelp();
        exit();
    }

    if ((!isset($options['n']) && !isset($options['number'])) || (empty($options['n']) && empty($options['number'])))
    {
        echo PHP_EOL . "\033[31m ERROR: Please supply a number parameter in format -n{int}. For example: -n5 \033[0m" .  PHP_EOL . PHP_EOL;
        printHelp();
        exit();
    }

    if (isset($options['n']) || isset($options['number']))
    {
        $number = (isset($options['n'])) ? $options['n'] : $options['number'];
        // Just cast it to an integer
        $parameters['number'] = (int) $number;
    }
    return $parameters;
}

/**
 * Parse the CLI arguments and get the required number of entries
 * @return array
 */
function parseProcessCliArguments()
{
    $parameters = array();
    $shortOptions = 'n::h::';
    $longOptions = array(
        'number::',
        'help::'
    );

    $options = getopt($shortOptions, $longOptions);
    if (isset($options['h']) || isset($options['help']))
    {
        printHelp();
        exit();
    }

    if ((!isset($options['n']) && !isset($options['number'])) || (empty($options['n']) && empty($options['number'])))
    {
        // Process all
        $parameters['number'] = -1;
    }

    if (isset($options['n']) || isset($options['number']))
    {
        $number = (isset($options['n'])) ? $options['n'] : $options['number'];
        // Just cast it to an integer
        $parameters['number'] = (int) $number;
    }
    return $parameters;
}