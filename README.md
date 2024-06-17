# CustomChart
**CustomChart** is a Laravel package that allows you to generate customizable chart data from your Eloquent models. It supports various aggregate functions and flexible date filtering options.

[![stars](https://badgen.net/github/stars/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![contributors](https://badgen.net/github/contributors/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![releases](https://badgen.net/github/releases/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)
[![issues](https://badgen.net/github/open-issues/devhereco/LaravelCustomChart)](https://github.com/devhereco/LaravelCustomChart)

---

## Simple Usage

### 1. Generate Chart Data
This function generates the data you will need to use for any chart.

__Controller__:
```php
use Devhereco\CustomChart\CustomChart;

// ...

$chart = CustomChart::create(
    User::class, 
    'Registered Users Per Day', 
    'count', 
    'id', 
    7,
    'days'
);

return $chart;
```

__Sample Output:__
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
    "this_month": 243,
    "last_month": 200,
    "this_year": 2430,
    "last_year": 2000,
    "percentage_change": {
        "this_month": {
            "value": "21.50%",
            "status": "positive"
        },
        "last_month": {
            "value": "10.00%",
            "status": "positive"
        },
        "this_year": {
            "value": "21.50%",
            "status": "positive"
        }
    },
    "total": 243
}
```

--- 
## Installation

### 1. Require with Composer
```sh
composer require devhereco/custom-chart
```

### 2. Add Service Provider (Laravel 5.4 and below)
Latest Laravel versions have auto-discovery and automatically add the service provider. If you're using Laravel 5.4.x and below, remember to add it to the providers array in /app/config/app.php:
```php
// ...
Devhereco\CustomChart\ServiceProvider::class,
```

---
## Available Reports and Options
The package currently supports various types of charts/reports:

| Option                | Description                                                                                          |
|-----------------------|------------------------------------------------------------------------------------------------------|
| `model` (required)    | The name of the Eloquent model to take data from.                                                     |
| `title` (optional)    | A text title that will be passed with the data for cleaner and easier usage.                          |
| `aggregate_function`  | You can view not only the count of records but also their `SUM()` or `AVG()`. Possible values: "count" (default), "avg", "sum". |
| `aggregate_field`     | The name of the field to use in `SUM()` or `AVG()` functions. Irrelevant for `COUNT()`.               |
| `filter_count`        | Show only the last `filter_count` intervals (e.g., days, weeks, months, years) of the specified field. |
| `filter_interval`     | The interval for filtering data. Possible values: "days" (default), "weeks", "months", "years".       |
| `show_total`          | Boolean value to show the total values for all selected intervals.                                    |
| `date_format`         | The date format, by default: American format Y-m-d.                                                   |

**NOTE:** From Laravel 8, all models are placed in a folder called `Models` (e.g., `App\Models\...`).

---
## Example with all options

### Controller:
```php
use Devhereco\CustomChart\CustomChart;

// ...

$chart = CustomChart::create(
    User::class, 
    'Registered Users Per Day', 
    'count', 
    'id', 
    7, // Show last 7 days/weeks/months/years
    'days', // Interval type
    true, // Show total
    'Y-m-d'
);

return $chart;
```
### Sample Output:
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
    "this_month": 243,
    "last_month": 200,
    "this_year": 2430,
    "last_year": 2000,
    "percentage_change": {
        "this_month": {
            "value": "21.50%",
            "status": "positive"
        },
        "last_month": {
            "value": "10.00%",
            "status": "positive"
        },
        "this_year": {
            "value": "21.50%",
            "status": "positive"
        }
    },
    "total": 243
}
```
