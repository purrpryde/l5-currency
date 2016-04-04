<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency;

use HitzMedia\Currency\Exceptions\NotSupportedStoreException;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/config' => config_path()
        ], 'config');

        $this->publishes([
            dirname(__DIR__) . '/database/migrations' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register bindings in the container.
     *
     * @throws \HitzMedia\Currency\Exceptions\NotSupportedStoreException
     * @return void
     */
    public function register()
    {
        if (is_array(config('currency'))) {
            $store = config('currency.store');

            if (!in_array($store, ['database'])) {
                throw new NotSupportedStoreException($store);
            }

            $storeClass = ucfirst($store);

            $this->app->bind(
                "HitzMedia\\Currency\\Contracts\\StoreContracts",
                "HitzMedia\\Currency\\Stores\\{$storeClass}"
            );

            $this->app->singleton('HitzMedia\Currency', function($app) {
                return new CurrencyLib($app->make('HitzMedia\Currency\Contracts\StoreContracts'));
            });
        }
    }
}