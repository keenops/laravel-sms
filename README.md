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
php artisan vendor:publish --tag=laravel-sms
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

use Keenops\LaravelSms\LaravelSms;

class TextController extends Controller
{
    public function index()
    {
        //sending sms
        $message = "Hello Word"; //String
        
        $recipients = [255717599994, '+255 717 599-994', '0717  599  994']; 
        //recipients is an array of receiver phone number in integers like 255717599994, and 717599994 
        //or strings like '255717599994', '0717599994', '717599999', '+255-717-599-994', and '0717 599 994',
        
        //create a new instance of the class LaravemSms class and pass the message and recipients.
        $sms = new LaravelSms();
        $sms->send($message, $recipients);
    
        return $sms;
    }
}
```
### Checking SMS Balance

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\LaravelSms\LaravelSms;

class TextController extends Controller
{
    public function index()
    {        
        //create a new instance of the class LaravemSms class and call the method viewBalance on that instance.
        $sms = new LaravelSms();
        
        return $sms->viewBalance();
    }
}
```
Output of viewBalance:

```bash
<integer value of the remaining balance> E.g 210
```
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

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
