<?php

require_once 'vendor/autoload.php';

$intKey = 'your-integration-key';
$secretKey = 'your-secret-key';
$apiHostname = 'your-api-hostname';

$auth = new \DuoAuth\Auth($apiHostname, $secretKey, $intKey);

// ping the API
$result = $auth->ping();
echo 'ping: '.var_export($result, true) . "\n";

// validate a code given as an input to the script
$result = $auth->validateCode('ccornutt', $_SERVER['argv'][1]);
echo 'validate: '.var_export($result, true) . "\n";

if ($result == false) {
    print_r($auth->getErrors());
}


