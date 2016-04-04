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

use HitzMedia\Currency\Contracts\StoreContract;
use HitzMedia\Currency\Exceptions\CurrencyAlreadyExistsException;
use HitzMedia\Currency\Exceptions\CurrencyNotFoundException;
use HitzMedia\Currency\Exceptions\DefaultCurrencyNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyLib
{
    /**
     * Key for currencies list cache.
     */
    const CACHE_CURRENCIES_KEY = 'hitzmedia.currencies';

    /**
     * Default decimal point.
     */
    const DEFAULT_DECIMAL_POINT = '.';

    /**
     * Default thousand point.
     */
    const DEFAULT_THOUSAND_POINT = ' ';

    /**
     * Array of package configuration.
     * 
     * @var array
     */
    protected $config;

    /**
     * Default currency code.
     *
     * @var string
     */
    protected $defaultCode;

    /**
     * Store implementation.
     *
     * @var \HitzMedia\Currency\Contracts\StoreContract
     */
    protected $store;

    /**
     * Currencies from data store.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $currencies = [];

    /**
     * Class constructor.
     *
     * @param \HitzMedia\Currency\Contracts\StoreContract $store
     *
     * @throws \HitzMedia\Currency\Exceptions\DefaultCurrencyNotFoundException
     */
    public function __construct(StoreContract $store)
    {
        $this->config = config('currency');
        $this->defaultCode = $this->config['default'];
        $this->store = $store;

        $this->setCurrencies($this->store->fetchCurrencies());

        if (!$this->has($this->defaultCode)) {
            throw new DefaultCurrencyNotFoundException($this->defaultCode);
        }
    }

    /**
     * Current currency code getter.
     *
     * @return string
     */
    public function getDefaultCode()
    {
        return $this->defaultCode;
    }

    /**
     * Get supported currencies and they data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->currencies;
    }

    /**
     * Set currencies data.
     *
     * @param  $currencies
     * @return void
     */
    protected function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * Checking that we have currency with passed code.
     *
     * @param  string  $code
     * @return boolean
     */
    public function has($code)
    {
        return $this->currencies->contains('code', $code);
    }

    /**
     * Get currency data.
     *
     * @param $code
     * @return mixed
     */
    public function get($code)
    {
        return $this->currencies->find($code);
    }

    /**
     * Get actual convertation value.
     *
     * @param  string $code
     * @return float
     */
    public function getValue($code = null)
    {
        if ($code != null) {
            $value = ($this->has($code)) ? $this->get($code)->value : 1;
        } else {
            $value = $this->get($this->config['default'])->value;
        }

        return $value;
    }

    /**
     * Add new currency to current store.
     *
     * @param string       $code
     * @param string       $title
     * @param array        $symbols
     * @param float|string $value
     * @param array        $points
     * @param int          $decimalPlace
     * @param bool         $enabled
     *
     * @throws \HitzMedia\Currency\Exceptions\CurrencyAlreadyExistsException
     * @return \Illuminate\Support\Collection
     */
    public function add($code, $title, array $symbols, $value = 'auto', array $points = [], $decimalPlace = 2, $enabled = true) {
        if ($this->has($code)) {
            throw new CurrencyAlreadyExistsException("Already have currency {$code}");
        }

        $this->store->addCurrency([
            'code'           => $code,
            'title'          => $title,
            'symbol_left'    => isset($symbols['left']) ? $symbols['left'] : null,
            'symbol_right'   => isset($symbols['right']) ? $symbols['right'] : null,
            'decimal_place'  => $decimalPlace,
            'decimal_point'  => isset($points['decimal']) ? $points['decimal'] : self::DEFAULT_DECIMAL_POINT,
            'thousand_point' => isset($points['thousand']) ? $points['thousand'] : self::DEFAULT_THOUSAND_POINT,
            'value'          => ($value != 'auto') ? $value : 1.00,
            'enabled'        => $enabled,
        ]);

        $this->recache();

        if ($value == 'auto') {
            $this->setActualValues($code, true);
        }

        return $this->get($code);
    }

    /**
     * Update currency data.
     *
     * @param string $code
     * @param array  $data
     * 
     * @throws \HitzMedia\Currency\Exceptions\CurrencyNotFoundException
     * @return \Illuminate\Support\Collection
     */
    public function update($code, array $data) {
        if (!$this->has($code)) {
            throw new CurrencyNotFoundException("Currency {$code} not found.");
        }
        
        $currency = $this->get($code);
        
        $updatedData = [
            'title'          => (isset($data['title'])) ? $data['title'] : $currency->title,
            'symbol_left'    => (isset($data['symbols']['left'])) ? $data['symbols']['left'] : $currency->symbol_left,
            'symbol_right'   => (isset($data['symbols']['right'])) ? $data['symbols']['right'] : $currency->symbol_right,
            'decimal_place'  => (isset($data['decimal_place'])) ? $data['decimal_place'] : $currency->decimal_place,
            'decimal_point'  => (isset($data['points']['decimal'])) ? $data['points']['decimal'] : $currency->decimal_point,
            'thousand_point' => (isset($data['points']['thousand'])) ? $data['points']['thousand'] : $currency->thousand_point,
            'enabled'        => (isset($data['enabled'])) ? $data['enabled'] : $currency->enabled,
        ];
        
        if (isset($data['value']) && $data['value'] != 'auto') {
            $updatedData['value'] = $data['value'];
        }

        $this->store->updateCurrency($code, $updatedData);

        $this->recache();

        if (isset($data['value']) && $data['value'] == 'auto') {
            $this->setActualValues($code, true);
        }

        return $this->get($code);
    }

    /**
     * Remove currency.
     *
     * @param $code
     * 
     * @throws \HitzMedia\Currency\Exceptions\CurrencyNotFoundException
     * @return void
     */
    public function remove($code)
    {
        if (!$this->has($code)) {
            throw new CurrencyNotFoundException("Currency {$code} not found.");
        }
        
        $this->store->removeCurrency($code);
        $this->recache();
    }

    /**
     * Get formatted string for passed or default currency.
     *
     * @param  integer     $number
     * @param  string      $code
     * @param  string|null $convertFrom
     * 
     * @return string
     */
    public function format($number, $code = '', $convertFrom = null)
    {
        $code         = ($code != '' && $this->has($code)) ? $code : $this->defaultCode;
        $currency     = $this->get($code);
        $value        = ($convertFrom != null) ? $this->convert($number, $code, $convertFrom) : $number;
        $resultString = '';
        
        if ($currency->symbol_left !== null) {
            $resultString .= $currency->symbol_left;
        }
        
        $resultString .= number_format(
            round($value, (int)$currency->decimal_place),
            (int)$currency->decimal_place,
            $currency->decimal_point,
            $currency->thousand_point
        );
        
        if ($currency->symbol_right !== null) {
            $resultString .= $currency->symbol_right;
        }
        
        return $resultString;
    }

    /**
     * Convert value to passed currency.
     *
     * @param  integer $value
     * @param  string  $to
     * @param  string  $from
     * 
     * @return integer|float
     */
    public function convert($value, $to, $from = null)
    {
        return $value * ($this->getValue($to) / $this->getValue($from));
    }

    /**
     * Get actual values from external source for currency passed in
     * $code param, or for all currencies if $code is null.
     *
     * @param  null|string $code
     * @param  bool        $getFromStorage
     * 
     * @throws \HitzMedia\Currency\Exceptions\CurrencyNotFoundException
     * @return void
     */
    public function setActualValues($code = null, $getFromStorage = false)
    {
        if (!extension_loaded('curl')) {
            Log::error('curl extension disabled, so currencies auto-refreshing is cancelled.');
            return;
        }

        if ($code === null) {
            $data = [];

            if ($getFromStorage) {
                $currencies = $this->store->fetchCurrencies();
            } else {
                $currencies = $this->getAll()->reject(function($curr) {
                    if (
                        $curr->code == $this->config['default'] ||
                        in_array($curr->code, $this->config['autoupdate_exclude'])
                    ) {
                        return true;
                    }

                    return false;
                });
            }

            if ($currencies->isEmpty()) {
                return;
            }

            foreach ($currencies as $currency) {
                $data[] = $this->config['default'] . $currency->code . '=X';
            }

            $dataString = implode(',', $data);
        } else {
            $currency = (!$getFromStorage) ? $this->get($code) : $this->store->getCurrency($code);

            if ($currency === null) {
                throw new CurrencyNotFoundException($code);
            }

            $dataString = $this->config['default'] . $currency->code . '=X';
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "http://download.finance.yahoo.com/d/quotes.csv?s={$dataString}&f=sl1&e=.csv",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $lines = explode("\n", trim($response));

        foreach ($lines as $line) {
            $code = mb_substr($line, 4, 3);
            $value = mb_substr($line, 11, 6);

            if ((float)$value) {
                $this->update($code, [
                    'value' => $value,
                ]);
            }
        }

        if ($this->config['cache']) {
            $this->recache();
        }
    }

    /**
     * Forget cached currencies data. 
     * 
     * @return void
     */
    public function recache()
    {
        if ($this->config['cache']) {
            Cache::forget(self::CACHE_CURRENCIES_KEY);
        }

        $this->setCurrencies($this->store->fetchCurrencies());
    }
}
