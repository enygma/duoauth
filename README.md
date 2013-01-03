duoauth
=======

[![Build Status](https://secure.travis-ci.org/enygma/duoauth.png?branch=master)](http://travis-ci.org/enygma/duoauth)

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

```
{
    "require": {
        "enygma/duoauth": "dev-master"
    }
}
```

### More information...

For more information on the functionality, see the wiki: https://github.com/enygma/duoauth/wiki/
