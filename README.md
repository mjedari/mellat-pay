# Iranian Mellat bank dedicated gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/:vendor_slug/:package_slug/run-tests?label=tests)](https://github.com/:vendor_slug/:package_slug/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/:vendor_slug/:package_slug/Check%20&%20fix%20styling?label=code%20style)](https://github.com/:vendor_slug/:package_slug/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)

Dedicated laravel package for Behpadakht Mellat bank payment service.



## Features

- Event calls
- Log on channels
- builtin rate limiter


## Installation

You can install the package via composer:

```bash
composer require mjedari/mellat-pay
```

You can publish and run the migrations and all assets with:

```bash
php artisan vendor:publish --tag="mellat-pay"
php artisan migrate
```

You can publish assets separately:

```bash
php artisan vendor:publish --tag="mellat-pay-config"

php artisan vendor:publish --tag="mellat-pay-views"

php artisan vendor:publish --tag="mellat-pay-lang"
```

This is the contents of the published config file that you should customize it. Its essential to set  `credentials` before any testing. `callback` is your default callback. You can change package tables name by modifying `table`. All exceptions an messages will be under your package `local` language.

```php
return [

    /*
    * Language for errors and messages:
    *
    */
    'local' => 'fa', //en

    /*
    * Description of credentials:
    *
    */
    'credentials' => [
        'username'     => '',
        'password'     => '',
        'terminalId'   => 0000000,
    ],

    /*
    * Gateway's default callback:
    *
    */
    'callback' => '/callback',

    /*
    * Description of table name:
    *
    */
    'table' => 'mellat_transactions',

];
```

There is one view file that is bank redirector. Optionally, you can publish that and modify for any customizing usage by using

```bash
php artisan vendor:publish --tag="mellat-pay"
```

## Simple Usage

There are there main steps. First you should initiate gateway in this way:
```php
// initiate transaction and redirect to bank

$gateway = MellatPay::price(10000)
    ->callback('/payment/callback')
    ->ready();

return $gateway->redirect();
```
If every this was ok, it redirects you to bank payment page.
Then you should define an callback route and wait for bank callback request:

```php
// payment callback route

$gateway = MellatPay::confirm()
    ->then(function ($response) {
        // transaction succeeded and response is transaction full info:
        return $response;
    })->catch(function ($e) {
        // you can get error occurred in transaction verify process:
        return $e->getMessage();
    });
```

## Advance Usage
### Transaction payable relation
You can specify transaction to other models with `payable` method on `MellatPay` facade. But before that make sure you added `payable` trait to your related model:
```php
// in your related modal

namespace App\Models;

use Mjedari\MellatPay\Traits\Payable;

class Product extends Model
{
    use payable; // <--- add this trait
    ...
    ...
    ...

}

```
And in your pay controller
```php
// initiate route

$product = Product::find(1);

$gateway = MellatPay::payable($product)->price(10000)->ready();

```
### Set optional values
there are some options in payment request. you can set `description`, `callback`, `payer`, `mobile`, `payable` for each transaction.

**Important:**
- If you do not specify `callback` url, the default one will be used.
- `mobile` is useful to send bank and it will be used to autocomplete gateway inputs according to the user's pervious payment card info.
```php
// initiate route full example

    $product = Product::find(245);
    
    $gateway = MellatPay::payable($product)
    ->price(10000) // <--- price in IRR
    ->description("This is a description") // <--- send to bank and store
    ->callback("https://example.dev/callback/product/245") // <--- callback to etch request 
    ->payer(1) // <--- used as user id 
    ->mobile("989102128582") // <--- used for gateway page autocomplete
    ->ready();

    return $gateway->redirect();

```

### Passing custom variables into redirect page
If you want to modify the redirector file, after publishing the view file you can pass any variable into that file by calling this method when redirecting.
```php
// initiate and redirect route

$wallet = Wallet::find(1);
$product = Product::find(1);

$gateway->with($product, $wallet)->redirect();
// or
$gateway->with(['product' => $product, 'wallet' => $wallet]) ...
//or
$gateway->with($product) ...

```
**Note that transaction info is already accessible by `$transaction`.*

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Mahdi Jedari](https://github.com/mjedari)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
