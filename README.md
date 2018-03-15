WDNA Users Roles
========

[![Latest Stable Version](https://poser.pugx.org/wdna/users/v/stable)](https://packagist.org/packages/wdna/users) [![Total Downloads](https://poser.pugx.org/wdna/users/downloads)](https://packagist.org/packages/wdna/users-roles)

[![Join the chat at https://gitter.im/wdna/users](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/wdna/users?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

A simple way of authenticating your APIs with API keys using Laravel. This package uses the following libraries:

- philsturgeon's [Fractal](https://github.com/thephpleague/fractal)
- maximebeaudoin's [api-response](https://github.com/ellipsesynergie/api-response)

## Laravel 5.3, 5.4 and 5.5 is finally supported!

**Laravel 5.5.x onwards: `~5.*`


## Quick start

### Installation for Laravel 5.3 to 5.4

Run `composer require wdna/users 1.1`

In your `config/app.php` add `wdna\users\Providers\UsersServiceProvider` to the end of the `providers` array

```php
'providers' => array(

    ...
    wdna\users\Providers\UsersServiceProvider::class,
),
```

Now publish the migration and configuration files for users-roles:

    $ php artisan vendor:publish --provider="wdna\users\Providers\UsersServiceProvider"

Then run the migration:

    $ php artisan migrate

