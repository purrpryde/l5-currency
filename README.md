# Currencies for Laravel 5

[![Packagist](https://img.shields.io/packagist/l/hitzmedia/l5-currency.svg)](https://github.com/purrpryde/l5-currency)
[![Packagist](https://img.shields.io/packagist/v/hitzmedia/l5-currency.svg)](https://github.com/purrpryde/l5-currency)
[![Packagist](https://img.shields.io/packagist/dt/hitzmedia/l5-currency.svg)](https://github.com/purrpryde/l5-currency)

Work with currencies easily and efficiently in your application.

## Installation

Add the following requirement to your `composer.json`: `"hitzmedia/l5-currency": "1.0.*"`
or simply use command `composer require hitzmedia/l5-currency:1.0.*`.

Then run `composer update`.

Next, add package's Service Provider to `app/config/app.php` in providers array:

``` PHP
'providers' => [
    // other providers...
    HitzMedia\Currency\CurrencyServiceProvider::class,
],
```

After that run artisan commands:

``` BASH
$ php artisan vendor:publish --provider="HitzMedia\Currency\CurrencyServiceProvider"
$ php artisan migrate
$ php artisan db:seed --class=HitzMedia\Currency\Seeds\CurrencySeeder
```

And finally, you can configure a package in the `config/currency.php` file.

## Usage

To use the library you need to get instance:

``` PHP
$currency = app()->make('HitzMedia\Currency');
// or shorter
$currency = app('HitzMedia\Currency');
```

Then you can use the public methods:

#### convert : integer|float
Convert value to passed currency.
If `$to` currency not exists, method will be return `$value`. If `$from` is null, default currency will be used.

``` PHP
$currency->convert($value, $to, $from = null)
```

#### format : string
Format `$number` accorting to `$code` or default currency.
If `$code` currency not exists, default currency will be used.

``` PHP
$currency->format($number, $code = '', $convertFrom = null);
// For example, returns $1 000.00
```

##### getDefaultCode : string
Get code of default currency.

``` PHP
$currency->getDefaultCode();
```

##### getAll : \Illuminate\Support\Collection
Get all currencies.

``` PHP
$currency->getAll();
```

##### get : HitzMedia\Currency\Models\Currency
Get currency by ISO 4217 code in lower case.

``` PHP
$currency->get($code);
```

##### getValue : float
Get value for passed currency, or for default if $code is null.

``` PHP
$currency->getValue($code = null);
```

##### has : boolean
Check is currency exists.

``` PHP
$currency->has($code);
```

#### add : HitzMedia\Currency\Models\Currency
Add new currency to current storage.
Throws `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if currency with `$code` not founded and `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if `$data` items fails validation.

``` PHP
$currency->add($code, $title, array $symbols, $value = 'auto', array $points = [], $decimalPlace = 2, $enabled = true);
```

Parameters:

- `$code` - Currency code in ISO 4217 format. _Example: usd_;
- `$title` - Currency title. _Example: US Dollar_;
- `$symbols` - Array with keys `left` or\and `right` keys. _Example: `['left' => $]`_;
- `$value` - Currency value. Default `auto` determines thar value will be set according to external source (Yahoo Finance). In passed numeric value - it will be used;
- `$points` - Sets dividers for thousands and decimal points, can contain `thousand` and `decimal` keys. Default value is `['thousand' => ' ', 'decimal' => '.']`. Used in formatting for get result, for example: `$10 000.00`;
- `$decimalPlace` - Sets the number of fractional part zeros;
- `$enabled` - You can disable the use of currency.

#### update : HitzMedia\Currency\Models\Currency
Update currency data in current storage.
Throws `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if currency with `$code` not founded and `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if `$data` items fails validation.

``` PHP
// All params of $data array is optional.
$currency->remove($code, $data = [
    'title'          => ...,
    'symbol_left'    => ...,
    'symbol_right'   => ...,
    'decimal_place'  => ...,
    'decimal_point'  => ...,
    'thousand_point' => ...,
    'enabled'        => ...
]);
```

#### remove : void
Remove currency from current storage.
Throws `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if currency with `$code` not founded.

``` PHP
$currency->remove($code);
```

#### setActualValues : void
Update currencies values according to external source data. (Currently only Yahoo Finance)
Throws `HitzMedia\Currency\Exceptions\CurrencyNotFoundException` if currency with `$code` not founded.

``` PHP
$currency->setActualValues($code = null, $getFromStorage = false);
```

#### recache : void
Drop currencies cache.

``` PHP
$currency->remove($code);
```

If `$code` is null, all currencies except default and included to `autoupdate_except` array will be updated.

## Console Commands
You can interact with package using artisan.

#### clear-cache
This command clear's currencies cache.

To use this command add her to `$commands` array on `app/Console/Kernel.php` file:

``` PHP
protected $commands = [
    // other commands...
    \HitzMedia\Currency\Commands\ClearCache::class,
];

// then use in command line: php artisan currency:clear-cache
```

#### update-values
This command update currencies values from external source. It can be used for scheduled updating.

To use this command add her to `$commands` array on `app/Console/Kernel.php` file:

``` PHP
protected $commands = [
    // other commands...
    \HitzMedia\Currency\Commands\UpdateCurrenciesValues::class,
];

// then use in command line: php artisan currency:update-values
```

And if you want configure scheduled updating, configure scheduled calling in `schedule` method.
Nice tutorial how you can do it, read in [laravel official docs](https://laravel.com/docs/master/artisan).

## Credits

This code is principally developed and maintained by [Vitaly Drozhdin](https://github.com/purrpryde).

## License

This package is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.