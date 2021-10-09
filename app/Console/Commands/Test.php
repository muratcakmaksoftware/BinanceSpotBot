<?php

namespace App\Console\Commands;

use App\Helpers\BinanceHelper;
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
        $binanceHelper =  new BinanceHelper(true);
        $fee = $binanceHelper->getCommission(1,"BNBBUSD", true);
        $this->info($fee);
    }
}
