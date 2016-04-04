<?php

/*
 * This file is part of Currencies package by HitzMedia
 *
 * (c) Vitaly Drozhdin <purrpryde@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HitzMedia\Currency\Commands;

use Illuminate\Console\Command;

class UpdateCurrenciesValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-values';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currencies values from external storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!config('currency.autoupdate')) {
            $this->error("Auto-updating disabled in configuration.\n");
            return false;
        }

        $currency = app()->make('HitzMedia\Currency');
        $currency->setActualValues();

        $this->info('Currencies values successfully updated.');
    }
}
