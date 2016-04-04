<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency\Contracts;

interface StoreContract
{
    /**
     * Fetch currencies.
     *
     * @return mixed
     */
    public function fetchCurrencies();

    /**
     * Get currency by code.
     *
     * @param  string $code
     *
     * @return mixed
     */
    public function getCurrency($code);

    /**
     * Add new currency.
     *
     * @param  array $data
     *
     * @throws \HitzMedia\Currency\Exceptions\InvalidParameterException
     * @return \HitzMedia\Currency\Models\Currency
     */
    public function addCurrency(array $data);

    /**
     * Update currency by code.
     *
     * @param  string $code
     * @param  array  $data
     *
     * @throws \HitzMedia\Currency\Exceptions\InvalidParameterException
     * @return \HitzMedia\Currency\Models\Currency
     */
    public function updateCurrency($code, array $data);

    /**
     * Remove currency from store.
     *
     * @param  string $code
     *
     * @return void
     */
    public function removeCurrency($code);
}
