# CustomChart
**Malath SMS** is a Laravel package that provides a method to use malath API services, with a few simple lines of code.

[![downloads](https://badgen.net//packagist/dt/devhereco/LaravelCustomChart)](https://packagist.org/packages/devhereco/custom-chart)
[![stars](https://badgen.net/github/stars/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![contributors](https://badgen.net/github/contributors/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![releases](https://badgen.net/github/releases/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![issues](https://badgen.net/github/open-issues/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![latest-release](https://badgen.net/packagist/v/devhereco/LaravelCustomChart/latest)](https://packagist.org/packages/devhereco/custom-chart)

---

## Simple Usage

### 1. Generate Chart Data
This function generate the data you will need to use for any chart.

__Controller__:
```php
use Devhereco\CustomChart\CustomChart;

// ...

$chart = CustomChart::create(
    User::class, 
    'Registered Users Per Day', 
    'count', 
    'filter_days' => 7
);

return $chart;
```

__Sample Outpot__
```json
{
    "name": "Registered Users Per Day",
    "data": {
        "2022-12-19": 12,
        "2022-12-20": 15,
        "2022-12-21": 23,
        "2022-12-22": 52,
        "2022-12-23": 41,
        "2022-12-24": 12,
        "2022-12-25": 15,
        "2022-12-26": 73
    }
}
```

---

## Installation

### 1. Require with [Composer](https://getcomposer.org/)
```sh
- composer require devhereco/custom-chart
```

### 2. Add Service Provider (Laravel 5.4 and below)

Latest Laravel versions have auto dicovery and automatically add service provider - if you're using 5.4.x and below, remember to add it to `providers` array at `/app/config/app.php`:

```php
// ...
Devhereco\CustomChart\ServiceProvider::class,
```

---

## Available Reports and Options

Currently package support three types of charts/reports: 

- `model` (required) - name of Eloquent model, where to take the data from;
- `title` (optional) - just a text title that will be passed with the data for cleaner and easier usage;
- `aggregate_function` (optional) - you can view not only amount of records, but also their `SUM()` or `AVG()`. Possible values: "count" (default), "avg", "sum".
- `aggregate_field` (optional) - see `aggregate_function` above, the name of the field to use in `SUM()` or `AVG()` functions. Irrelevant for `COUNT()`.
- `filter_days` (optional) - see `filter_field` above - show only last `filter_days` days of that field. Example, last __30__ days by `created_at` field.
- `show_total` (optional) - this attribute will give the total values for all days selected in `filter_days`, and it takes a boolen value (True, False);
- `date_format` (optional) - add the date format, by default: American format Y-m-d

### NOTE: From Laravel 8, all its models are placed in a folder called Models (App\Models\)

__Example with all options__

__Controller__:
```php
use Devhereco\CustomChart\CustomChart;

// ...

$chart = CustomChart::create(
    'model' => User::class, 
    'title' => 'Registered Users Per Day', 
    'aggregate_function' => 'count', 
    'aggregate_field' => 'id',
    'filter_days' => 7, // Show last 7 days
    'show_total' => true,
    'date_format' => 'Y-m-d'
);

return $chart;
```

__Sample Outpot__
```json
{
    "name": "Registered Users Per Day",
    "data": {
        "2022-12-19": 12,
        "2022-12-20": 15,
        "2022-12-21": 23,
        "2022-12-22": 52,
        "2022-12-23": 41,
        "2022-12-24": 12,
        "2022-12-25": 15,
        "2022-12-26": 73
    },
    "total": 243
}
```
