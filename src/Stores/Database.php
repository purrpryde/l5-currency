<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency\Stores;

use HitzMedia\Currency\Exceptions\InvalidParameterException;
use Illuminate\Support\Facades\Cache;
use HitzMedia\Currency\CurrencyLib as Library;
use HitzMedia\Currency\Models\Currency as CurrencyModel;
use HitzMedia\Currency\Contracts\StoreContract;
use Illuminate\Support\Facades\Validator;

class Database implements StoreContract
{
    /**
     * Fetch currencies.
     *
     * @return mixed
     */
    public function fetchCurrencies()
    {
        if (config('currency.cache') == true) {
            return Cache::rememberForever(Library::CACHE_CURRENCIES_KEY, function() {
                return CurrencyModel::where('enabled', 1)->get();
            });
        } else {
            return CurrencyModel::where('enabled', 1)->get();
        }
    }

    /**
     * Get currency by code.
     *
     * @param  string $code
     * 
     * @return mixed
     */
    public function getCurrency($code)
    {
        if (config('currency.cache') == true) {
            $data = Cache::get(Library::CACHE_CURRENCIES_KEY, function() use($code) {
                return CurrencyModel::where('code', $code)->first();
            });

            $dataObjectNamespace = explode('\\', get_class($data));

            return (end($dataObjectNamespace) == 'Collection') ? $data->where('code', $code)->first() : $data;
        } else {
            return CurrencyModel::where('code', $code)->first();
        }
    }

    /**
     * Add new currency.
     *
     * @param  array $data
     * 
     * @throws \HitzMedia\Currency\Exceptions\InvalidParameterException
     * @return \HitzMedia\Currency\Models\Currency
     */
    public function addCurrency(array $data)
    {
        $validator = Validator::make($data, [
            'code'           => 'required',
            'title'          => 'required',
            'symbol_left'    => 'required_without:symbol_right',
            'symbol_right'   => 'required_without:symbol_left',
            'decimal_place'  => 'required|integer',
            'decimal_point'  => 'string',
            'thousand_point' => 'string',
            'value'          => 'numeric',
            'enabled'        => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors());
        }

        return CurrencyModel::create($data);
    }

    /**
     * Update currency by code.
     *
     * @param  string $code
     * @param  array  $data
     * 
     * @throws \HitzMedia\Currency\Exceptions\InvalidParameterException
     * @return \HitzMedia\Currency\Models\Currency
     */
    public function updateCurrency($code, array $data)
    {
        $validator = Validator::make($data, [
            'title'          => 'required',
            'symbol_left'    => 'required_without:symbol_right',
            'symbol_right'   => 'required_without:symbol_left',
            'decimal_place'  => 'required|integer',
            'decimal_point'  => 'string',
            'thousand_point' => 'string',
            'value'          => 'numeric',
            'enabled'        => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors());
        }
        
        CurrencyModel::where('code', $code)->update($data);
    }

    /**
     * Remove currency from store.
     *
     * @param  string $code
     *
     * @return void
     */
    public function removeCurrency($code)
    {
        CurrencyModel::destroy($code);
    }
}
