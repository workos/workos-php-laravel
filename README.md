# WorkOS PHP Laravel Library

The WorkOS library for Laravel provides convenient access to the WorkOS API from applications written in Laravel.

## Documentation

See the [API Reference](https://workos.com/docs/reference/client-libraries) for Laravel usage examples.

## Installation

To install via composer, run the following:

```bash
composer require workos/workos-php-laravel
```

For Laravel 5.0-5.4, add the WorkOS ServiceProvider in your `config/app.php`:

```php
"providers" => array(
    // ...
    WorkOS\Laravel\WorkOSServiceProvider::class
)
```

For Laravel 5.5 and up, 6.x and 7.x... you're all set!

## Configuration

Create a WorkOS configuration file by running the following:

```bash
php artisan vendor:publish --provider="WorkOS\Laravel\WorkOSServiceProvider"
```

The package will need to be configured with your [api key](https://dashboard.workos.com/api-keys) and [project id](https://dashboard.workos.com/sso/configuration).
By default, the package will look for a `WORKOS_API_KEY` and `WORKOS_CLIENT_ID` environment variable.

## More Information

* [Single Sign-On Guide](https://workos.com/docs/sso/guide)
* [Directory Sync Guide](https://workos.com/docs/directory-sync/guide)
* [Admin Portal Guide](https://workos.com/docs/admin-portal/guide)
* [Magic Link Guide](https://workos.com/docs/magic-link/guide)
