<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Mint extends Command
{
    protected $signature = 'command:mint';

    protected $description = 'Darphanem';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $now = Carbon::now()->format("d.m.Y H:i:s");
        $this->info("Time::: ". $now);
        //Log::info("Test test test :");

        //return 0;
    }
}
