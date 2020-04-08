# workos-php-laravel

Laravel SDK to conveniently access the [WorkOS API](https://workos.com).

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

### SSO
The package offers the following convenience functions to utilize WorkOS SSO.

First we'll want to generate an OAuth 2.0 Authorization URL to initiate the SSO workflow with:

```php
$url = (new \WorkOS\SSO())->getAuthorizationUrl(
    'foo-corp.com',
    'http://my.cool.co/auth/callback',
    ['things' => 'gonna get this back'],
    null // Pass along provider if we don't have a domain
);
```

After directing the user to the Authorization URL and successfully completing the SSO workflow, use 
the code passed back from WorkOS to grab the profile of the authenticated user to verify all is good:

```php
$profile = (new \WorkOS\SSO())->getProfile($code);
```
