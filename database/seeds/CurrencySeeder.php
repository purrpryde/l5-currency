<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency\Seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        DB::table('currencies')->insert([
            'code' => 'usd',
            'title' => 'US Dollar',
            'symbol_left' => '$',
            'symbol_right' => null,
            'decimal_place' => 2,
            'decimal_point' => '.',
            'thousand_point' => ',',
            'value' => 1.00000000,
            'enabled' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}