duoauth
=======

PHP Library for easy integration with Duo Security's Two-Factor REST API

The Duo Security service provides easy integration with your current authentication methods
to drop in two-factor authentication (cell phone or other device).

They have a "developer" plan that's free and allows for up to 10 users on the application/account.

Find out more here: http://duosecurity.com

REST docmentation: https://www.duosecurity.com/docs/duorest

### Creating an Account

To create an application, you'll need to make an account with Duo Security. Once you're in
you'll need to:

1. Click on the "Integrations" item in the sidebar and click "New Application"
2. For the Integration type, choose "REST API" and give it a name
3. Once it's created, click on its name to get to the detail page. Here's where you'll find the keys
   you'll need to access the API (integration, secret and the API hostname)

### Installation via Composer:

Include in your `composer.json` file:

`
{
    "require": {
        "enygma/duoauth": "dev-master"
    }
}
`

### Example Usage - Auth

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

### Example Usage - Admin

```php
<?php
$auth = new \DuoAuth\Admin($apiHostname, $secretKey, $intKey);

// "preauth" a user to see if they're valid
$result = $auth->preauth('ccornutt'); 
var_dump($result);

// get a list of the current account's users
$result = $auth->getUsers();
var_dump($result);

// get the detail of the given user
$result = $auth->getUser('ccornutt');
var_dump($result);
?>
```

### Methods - Auth

**ping**

*Parameters:*

No parameters, makes a "heartbeat" request to the Duo Security API

**preauth**

*Parameters:*

- username (string)

**validateCode**

*Parameters:*

- username (string)
- code (string)

### Methods - Admin

**getUsers**

*Parameters:*

No parameters, gets full user list with details


**getUser**

*Parameters:*

- username (string)

