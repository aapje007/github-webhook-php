<?php
/*
 * config.php
 * 
 * default config file for the webhook, defines a config 
 * array with the required values
 */

$config = array();

// define the filename of the log file
$config['logfile'] = 'webhook.log';

// define the repository url, if not defined, the url from the github payload is used. 
// This can be used to access a private repositury with cedentials
// $config['repository_url'] = 'https://username:password@github.com/user/repo.git';
?>