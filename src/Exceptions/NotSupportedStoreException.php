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

class NotSupportedStoreException extends Exception
{
    /**
     * Construct the exception.
     *
     * @param string $store
     */
    public function __construct($store)
    {
        $this->message = sprintf('Not supported data storage: %s', $store);
    }
}
