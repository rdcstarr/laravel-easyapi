# Laravel EasyAPI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rdcstarr/laravel-easyapi.svg?style=flat-square)](https://packagist.org/packages/rdcstarr/laravel-easyapi)
[![Tests](https://img.shields.io/github/actions/workflow/status/rdcstarr/laravel-easyapi/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rdcstarr/laravel-easyapi/actions)
[![Code Style](https://img.shields.io/github/actions/workflow/status/rdcstarr/laravel-easyapi/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rdcstarr/laravel-easyapi/actions)
[![Downloads](https://img.shields.io/packagist/dt/rdcstarr/laravel-easyapi.svg?style=flat-square)](https://packagist.org/packages/rdcstarr/laravel-easyapi)

> Lightweight package for managing **API keys** in Laravel — with middleware protection, logging and simple CLI tools.

---

## ✨ Features

- 🔑 API key management — generate, list, reveal and delete API keys via an artisan command or programmatically.
- 🛡 Middleware protection — a lightweight middleware validates Bearer tokens on incoming requests.
- 📈 Usage metrics — each key tracks an access count and API access is logged to `api_logs`.
- 🔒 Secure keys — keys are generated using SHA-256 with unique identifiers to avoid collisions.
- ⚙️ Facade & manager — programmatic API via the `EasyApi` facade or the `EasyApiManager` service.
- 🧪 Test-friendly — models and factories included to make testing straightforward.
- 📦 Migrations included — package ships migrations for `api` and `api_logs` tables and can be published.

---

## 📦 Installation

```bash
composer require rdcstarr/laravel-easyapi
```

Publish the migrations (optional) and migrate:

```bash
php artisan vendor:publish --provider="Rdcstarr\EasyApi\EasyApiServiceProvider"
php artisan migrate
```

The package registers a singleton manager and a console command. It will also load the package migrations and register route groups if you provide `routes/api.php` or `routes/web.php`.

## 🔑 Usage

Facade examples (programmatic):

```php
use Rdcstarr\EasyApi\Facades\EasyApi;

// Generate a new API key (returns the Api model)
$api = EasyApi::createKey();
$fullKey = $api->key; // show and store this securely

// Validate a key (returns bool)
$isValid = EasyApi::validateKey($fullKey);

// Delete a key
EasyApi::deleteKey($fullKey);
```

Middleware usage:

- The package provides `Rdcstarr\EasyApi\Middleware\EasyApiMiddleware` which checks for a Bearer token and validates it against the `api` table. If valid, it logs the request and increments the access count.

Apply it to a route or route group:

```php
Route::middleware([\Rdcstarr\EasyApi\Middleware\EasyApiMiddleware::class])->group(function () {
    Route::get('/protected', function () {
        return ['ok' => true];
    });
});
```

Database schema:

- `api` table: id, key (unique), access_count, timestamps
- `api_logs` table: id, api_id, endpoint, ip_address, user_agent, timestamps

Artisan CLI:

The package exposes a single console command: `php artisan easyapi` with the following actions:

- generate — create a new API key
- delete --key=KEY — delete an API key (confirmation required)
- list — display stored API keys (masked) with access counts
- reveal --id=ID — reveal the full API key for a given id


Examples:

```bash
php artisan easyapi generate
php artisan easyapi list
php artisan easyapi delete --key="qwerty_..."
php artisan easyapi reveal --id=1
```

Notes:

- Generated keys must be stored securely when created — the `generate` command shows the full key once.
- The command output masks keys in listings for safety; use `reveal` to show the full value when necessary.

## 🧪 Testing

Run the package tests:

```bash
composer test
```

The package provides models (`Api`, `ApiLog`) and factories to make writing tests simpler.

## 📖 Resources
 - [Changelog](CHANGELOG.md) for more information on what has changed recently.

## 👥 Credits
 - [Rdcstarr](https://github.com/rdcstarr)

## 📜 License
 - [License](LICENSE.md) for more information.
