<?php

namespace App\Console\Commands;

use App\Helpers\BinanceHelper;
use App\Helpers\LogHelper;
use App\Models\Coin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \Binance;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {coin} {currency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mind';

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
        $coinName = $this->argument("coin"); //Ex: MATIC
        $currency = $this->argument("currency"); //Ex: TRY

        $coin = Coin::where("name", $coinName)->first();
        if(isset($coin)){
            $coinId = $coin->id;
            $coinName = $coin->name;
            $spot = $coinName.$currency; //MATICTRY
            $coinProfit = $coin->profit; // Satışta alacağımız kazanç miktarı
            $coinPurchase = $coin->purchase; // satın alma aralık coin para birimi artı ve eksi aralığını belirler.
            $this->info("Coin ID: ".$coinId);
            LogHelper::orderLog("Coin ID", $coinId);

            $this->info("Alınacak Coin Adı: ".$coinName);
            LogHelper::orderLog("Alınacak Coin Adı", $coinName);

            $this->info("Satılacak Para Birimi: ".$currency);
            LogHelper::orderLog("Satılacak Para Birimi", $currency);

            $this->info("SPOT: ". $spot);
            LogHelper::orderLog("SPOT", $spot);

            $this->info("Min kâr: ".$coinProfit. " ". $currency);
            LogHelper::orderLog("Min kâr", $coinProfit. " ". $currency);

            $this->info("Coin Sürkülasyon Aralığı: ".$coinPurchase);
            LogHelper::orderLog("Coin Sürkülasyon Aralığı", $coinPurchase);

            $binanceHelper =  new BinanceHelper($coinId, $testStatus);
            $whileCounter = 1;
            while(true){
                $uniqueId = uniqid(rand(), true);
                $this->info("");
                LogHelper::orderLog("","", $uniqueId);

                $this->info($whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"));
                LogHelper::orderLog("",$whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"), $uniqueId);

                $this->info($whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...");
                LogHelper::orderLog("", $whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...", $uniqueId);

                if($binanceHelper->openOrdersByPass($spot)) { //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                    dd("asdasd");
                }
            }
        }else{
            LogHelper::log(1, "", "Coin Select", "Coin bulunamadı!");
        }


        $fee = $binanceHelper->getCommission(1,"BNBBUSD", $testStatus);
        $this->info($fee);
    }
}
