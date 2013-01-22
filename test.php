<?php

require_once 'vendor/autoload.php';

// Finding all users
$user = new \DuoAuth\User();
echo 'find all: '; var_dump($user->findAll());

// Finding a single user
$user = new \DuoAuth\User();
if ($user->findByUsername('ccornutt') == true) {
    echo 'find by username: '; var_dump();
    var_dump($user);
}

// or, alternatively
if ($user->find('/admin/v1/users', '\\DuoAuth\\User', array('username' => 'ccornutt'), '\DuoAuth\User') == true) {
    echo 'find by username (direct): '; var_dump($user);
    var_dump($user);
}

// Validating a user's inputted code
$user = new \DuoAuth\User();
if ($user->findByUsername('ccornutt') == true) {
    echo 'validate code: '; var_dump($user->validateCode('user-inputted-code'));
}


// Getting the user's phones
$user = new \DuoAuth\User();
if ($user->findByUsername('ccornutt') == true) {
    echo 'get phones: '; var_dump($user->getPhones());
}

// Associating a phone with a user
$user = new \DuoAuth\User();
if ($user->findByUsername('ccornutt') == true) {
    $phones = $user->getPhones();
    echo 'associate phone: '; var_dump($phones[0]->associate($user)); // yes, I know this just reassigns the phone to the same user...
}
