# workos-php-laravel

Laravel SDK to conveniently access the [WorkOS API](https://workos.com).

For more information on our API and WorkOS, check out our docs [here](https://docs.workos.com).

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

## Getting Started

Create a WorkOS configuration file by running the following:
```bash
php artisan vendor:publish --provider="WorkOS\Laravel\WorkOSServiceProvider"
```

The package will need to be configured with your [api key](https://dashboard.workos.com/api-keys) and [project id](https://dashboard.workos.com/sso/configuration).
By default, the package will look for a `WORKOS_API_KEY` and `WORKOS_PROJECT_ID` environment variable.

## Usage

Underneath it all, the Laravel SDK utilizes the WorkOS PHP SDK. For more information on usage, check out the docs [here](https://docs.workos.com/sdk/laravel) and the PHP SDK [here](https://github.com/workos-inc/workos-php).