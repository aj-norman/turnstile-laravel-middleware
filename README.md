# Turnstile Laravel Middleware
## ⚠️ Documentation is incomplete and may be inaccurate. ⚠️

![Static Badge](https://img.shields.io/badge/packagist-turnstile_laravel_middleware-orange?style=flat-square&link=https%3A%2F%2Fpackagist.org%2Fpackages%2Fajnorman%2Fturnstile-laravel-middleware)
![Static Badge](https://img.shields.io/badge/license-MIT-blue?style=flat-square&link=https%3A%2F%2Fgithub.com%2Faj-norman%2Fturnstile-laravel-middleware%2Fblob%2Fmain%2FLICENSE.md)

___

## Table of Contents
- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)

---

## Introduction

This package provides a piece middleware for Laravel that integrates with Cloudflare Turnstile to protect your application
from unwanted bots.

---

## Requirements

- PHP >= 8.2
- Laravel >= 11.x
- A Cloudflare account with Turnstile enabled and a `site_key` and `secret_key` generated. View the 
[Cloudflare Turnstile documentation](https://developers.cloudflare.com/turnstile) for more information.

---

## Installation

You can install the package via composer:

```bash
composer require aj-norman/turnstile-laravel-middleware
```

Add the following environment variables to your `.env` file:

```dotenv
CF_TURNSTILE_SITE_KEY=<your-site-key>
CF_TURNSTILE_SECRET_KEY=<your-secret-key>
```

(Optional) Publish the configuration file:

```bash
php artisan vendor:publish --provider="AJNorman\Turnstile\TurnstileServiceProvider" --tag="config"
```
> If you customize the configuration file, you may need to manually update the `config/turnstile.php` file in the 
> future if the package is updated.

Add `'turnstile' => AJNorman\Turnstile\Middleware\TurnstileMiddleware::class` middleware to the `alias()` array within the 
`withMiddleware()` method in your `bootstrap/app.php` file:

```php
return Application::configure(basePath: dirname(__DIR__))
    //...
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            //...
            'turnstile' => \AJNorman\Turnstile\Middleware\TurnstileMiddleware::class,
        ]);
    })
    //...
    ->create();
```
> The `'turnstile'` key can be changed to any key you prefer.

---

## Usage

You can apply the Turnstile middleware to any route or group of routes by adding `'turnstile'` to the middleware array:

```php
Route::post('/protected-route', function () {
    return 'This route is protected by Turnstile!';
})->middleware('turnstile');
```
