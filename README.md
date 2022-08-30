# Beem.africa SMS package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keenops/laravel-sms.svg?style=flat-square)](https://packagist.org/packages/keenops/laravel-sms)
[![Total Downloads](https://img.shields.io/packagist/dt/keenops/laravel-sms.svg?style=flat-square)](https://packagist.org/packages/keenops/laravel-sms)

This package makes it easy to send messages using Beem.africa with Laravel.It  is basicly a wrapper around their API. 

## Installation

You can install the package via composer:

```bash
composer require keenops/laravel-sms
```
After installation, publish the configuration files by running the command:

```bash
php artisan vendor:publish --tag=laravel-beem-sms
```

## Usage

Add beem.africa API credentials. Can be obtained [here](https://beem.africa/beem-api/)

```bash
    BEEM_API_KEY=
    BEEM_API_SECRET=
    BEEM_SENDER_NAME=
```
### Sending SMS

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\Sms\Facades\Sms;

class SMSController extends Controller
{
    public function index()
    {
        $message = "Hello Word"; //String
        
        $recipients = ['255701000001', '255701000002', '25570100003']; //array
 
        return Sms::send($message, $recipients);
        //returns a json value with sent status

    }
}
```
### Checking SMS Balance

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\Sms\Facades\Sms;

class SMSController extends Controller
{
    public function index()
    {
 
        return Sms::viewBalance();
        //returns Integer value of the remaining balance E.g 210
    }
}
```

### List sender names

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\Sms\Facades\Sms;

class SMSController extends Controller
{
    public function index()
    {
 
        return Sms::senderNames();
        //returns json list of registered sender name E.g 210
    }
}
```

### Request a new sender name

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\Sms\Facades\Sms;

class SMSController extends Controller
{
    public function index()
    {
 
        return Sms::requestNewSenderName('API TEST','This is test api for new sender name using laravel beem package');
        //return json value of the new requested sender name

    }
}
```

### Errors
Specific error codes may be displayed within parenthesis when send or receive operations fail. The most common of these error codes are specified on beem.africa [API Documetation](https://docs.beem.africa/#api-_)

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email git@kimwalu.com instead of using the issue tracker.

## Credits

-   [Kimwalu](https://kimwalu.com)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
