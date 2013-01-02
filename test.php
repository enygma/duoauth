<?php

require_once 'vendor/autoload.php';

// Finding all users
$user = new \DuoAuth\User();
var_dump($user->findAll());

// Finding a single user
$user = new \DuoAuth\User();
var_dump($user->findByUsername('ccornutt'));
// or, alternatively
var_dump($user->find(array('username' => 'ccornutt')));

// Validating a user's inputted code
$user = new \DuoAuth\User();
var_dump($user->findByUsername('ccornutt')->validateCode('user-inputted-code'));

// Getting the user's phones
$user = new \DuoAuth\User();
var_dump($user->findByUsername('ccornutt')->getPhones());

// Associating a phone with a user
$user = new \DuoAuth\User();
$phones = $user->findByUsername('ccornutt')->getPhones();
var_dump($phone[0]->associate($user)); // yes, I know this just reassigns the phone to the same user...

