# Malath SMS 
**Malath SMS** is a Laravel package that provides a method to use malath API services, with a few simple lines of code.

[![downloads](https://badgen.net//packagist/dt/devhereco/LaravelCustomChart)](https://packagist.org/packages/devhereco/LaravelCustomChart)
[![stars](https://badgen.net/github/stars/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![contributors](https://badgen.net/github/contributors/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![releases](https://badgen.net/github/releases/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![issues](https://badgen.net/github/open-issues/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![latest-release](https://badgen.net/packagist/v/devhereco/LaravelCustomChart/latest)](https://packagist.org/packages/devhereco/LaravelCustomChart)

## Installation

### 1. Require with [Composer](https://getcomposer.org/)
```sh
- composer require devhereco/custom-chart
```

### 2. Add Service Provider (Laravel 5.4 and below)

Latest Laravel versions have auto dicovery and automatically add service provider - if you're using 5.4.x and below, remember to add it to `providers` array at `/app/config/app.php`:

```php
// ...
Devhereco\LaravelCustomChart\ServiceProvider::class,
```

## Usages

### 1. Generate Chart Data
This function generate the data you will need to use for any chart.

Examples:
```php
use Devhereco\CustomChart\CustomChart;

CustomChart::create(Transaction::class, 'sum', 'amount', '30');
```
