# Laravel Message Interceptor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mozammil/laravel-message-interceptor.svg?style=flat-square)](https://packagist.org/packages/mozammil/laravel-message-interceptor)
[![Build Status](https://img.shields.io/travis/com/mozammil/laravel-message-interceptor.svg?style=flat-square)](https://travis-ci.org/mozammil/laravel-message-interceptor)
[![Quality Score](https://img.shields.io/scrutinizer/g/mozammil/laravel-message-interceptor.svg?style=flat-square)](https://scrutinizer-ci.com/g/mozammil/laravel-message-interceptor)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)


This Laravel package helps to intercept e-mails and sends them to another recipient (or recipients) in a local environment.

Laravel ships with one way of [intercepting emails in a local environment](https://laravel.com/docs/5.7/mail#mail-and-local-development). However, this packages gives you a little bit more flexibility, such as whitelisting emails and domains in a local environment, preserving `CC` and `BCC` recipients, etc.

This hopefully helps to make the testing experience in email-centric workflows a little less painful for developers working locally.

## Install

Via Composer

``` bash
$ composer require mozammil/laravel-message-interceptor --dev
```

Because of package auto-discovery, this should automatically register your `ServiceProvider`.

## Configuration

To publish the config file, you should do the following:

``` bash
$ php artisan vendor:publish
```

The config file will be copied to `config/message-interceptor.php`. It should give you an idea of what's possible with the package.

``` php
<?php

return [

    /**
     * Whether or not the emails being sent should
     * be intercepted.
     */
    'enabled' => env('MESSAGE_INTERCEPTOR_ENABLED', false),

    /**
     * The intercepted emails will be sent
     * to this address instead. If the address
     * is not send, an exception will be thrown
     */
    'to' => [
        'address' => env('MESSAGE_INTERCEPTOR_ADDRESS', ''),
        'name' => env('MESSAGE_INTERCEPTOR_NAME', '')
    ],

    /**
     * By default, we will clear the recipients that are
     * cc'ed in the message. If you want to preserve the
     * cc'ed recipients, set this to true
     */
    'preserveCc' => env('MESSAGE_INTERCEPTOR_PRESERVE_CC', false),

    /**
     * By default, we will clear the recipients that are
     * bcc'ed in the message. If you want to preserve the
     * bcc'ed recipients, set this to true
     */
    'preserveBcc' => env('MESSAGE_INTERCEPTOR_PRESERVE_BCC', false),

    /**
     * The following addresses will also be CC'ed
     * when the email is intercepted and sent.
     */
    'cc' => [],

    /**
     * The following addresses will also be BCC'ed
     * when the email is intercepted and sent.
     */
    'bcc' => [],

    /**
     * Whitelisted email recipients.
     */
    'whitelist' => [
        /**
         * Email addresses in the list below will
         * not be filtered out
         */
        'emails' => [],

        /**
         * Email address matching the domain in
         * the list below will not be filtered out
         */
        'domains' => []
    ]
];

```

For the package to work appropriately, it is fundamental that the following is configured in your `.env` file. By default, no emails will be intercepted.

```
MESSAGE_INTERCEPTOR_ADDRESS=hello@example.com
MESSAGE_INTERCEPTOR_ENABLED=true
```

## Additional Information

Whenever an e-mail is intercepted, a `Mozammil\LaravelMessageInterceptor\Events\MessageIntercepted::class` event is also dispatched. You could then retrieve the original underlying `Swift_Message` from the event.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email [hello@moz.im](mailto:hello@moz.im) instead of using the issue tracker.

## Credits

- [Mozammil Khodabacchas](https://twitter.com/mozammil_k)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.