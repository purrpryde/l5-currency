<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->char('code',3);
            $table->string('title');
            $table->string('symbol_left')->nullable();
            $table->string('symbol_right')->nullable();
            $table->smallInteger('decimal_place')->unsigned();
            $table->string('decimal_point', 1)->nullable();
            $table->string('thousand_point', 1)->nullable();
            $table->decimal('value', 15, 8);
            $table->boolean('enabled')->default(1);
            $table->timestamps();

            $table->primary('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
