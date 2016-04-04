<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency\Exceptions;

use Exception;

class CurrencyNotFoundException extends Exception
{
    /**
     * Construct the exception.
     *
     * @param string $code
     */
    public function __construct($code)
    {
        $this->message = sprintf('Currency with code \'$s\' not found.', $code);
    }
}
