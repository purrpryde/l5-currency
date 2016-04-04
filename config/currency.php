<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This value determines the default application currency that will be
    | used by default for formatting and conversion.
    |
    */

    'default' => 'usd',

    /*
    |--------------------------------------------------------------------------
    | Data Storage
    |--------------------------------------------------------------------------
    |
    | This value determines the source of the information used, from which
    | will be taken of the currency information. While it can only take the
    | "database" value.
    |
    */

    'store' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Data Caching
    |--------------------------------------------------------------------------
    |
    | Cache list of currencies?
    |
    */

    'cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Autoupdate values
    |--------------------------------------------------------------------------
    |
    | Enable auto-update rates from external sources?
    | Currently implemented auto-update only through the service Yahoo! Finance
    |
    */

    'autoupdate' => false,

    /*
    |--------------------------------------------------------------------------
    | Autoupdate values
    |--------------------------------------------------------------------------
    |
    | If you have enabled auto-updating currency values, you can specify a
    | list of currencies that are not automatically updated.
    |
    */

    'autoupdate_exclude' => [],

];
