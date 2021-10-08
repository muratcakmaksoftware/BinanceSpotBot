<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Binance;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command';

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
     * @return int
     */
    public function handle()
    {
       $api = new Binance\API("NE2zfaJ3DeUi3E8slgkRp8tuzBjsQIqGXOJKPUtSSNkn3YhzQ2WIazskyb20m8nI", "fMhRLVEPFYe510tl4eAeQqUjSLW4igAwyLqKgiLA8bCkdpCgnmMbM0oAXe9MT8T4", true);
       $ticker = $api->prices();// tüm para birimlerini alma
       dd(print_r($ticker));
    }
}
