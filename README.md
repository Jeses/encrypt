# laravel-ssapi
A simple laravel server to server api sign and verify class.


## Installation

Pull this package in through Composer.

```js

    {
        "require": {
            "zhengcai/encrypt": "1.*"
        }
    }

```

or run in terminal:
`composer require zhengcai/encrypt`

then copy the config file

`php artisan vendor:publish --provider="Zhengcai\Encrypt\EncryptServiceProvider"`

## Usage

### Laravel usage

```php

    use Zhengcai\Encrypt\Facades\Encrypt;

    

```