duoauth
=======

PHP Library for easy integration with Duo Security's Two-Factor REST API

The Duo Security service provides easy integration with your current authentication methods
to drop in two-factor authentication (cell phone or other device).

They have a "developer" plan that's free and allows for up to 10 users on the application/account.

Find out more here: http://duosecurity.com


Example Usage
=================

```php
<?php

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

?>
```

Methods
==================

**ping**

*Parameters:*

No parameters, makes a "heartbeat" request to the Duo Security API

**validateCode**

*Parameters:*

- username (string)
- code (string)
