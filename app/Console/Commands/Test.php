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
        $coinName = strtoupper($this->argument("coin")); //Ex: MATIC
        $currency = strtoupper($this->argument("currency")); //Ex: TRY

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

            $binanceHelper = new BinanceHelper($coinId);
            $whileCounter = 1;
            while(true){
                $uniqueId = uniqid(rand(), true);
                /*$this->info("");
                LogHelper::orderLog("","", $uniqueId);

                $this->info($whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"));
                LogHelper::orderLog("",$whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"), $uniqueId);

                $this->info($whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...");
                LogHelper::orderLog("", $whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...", $uniqueId);

                $binanceHelper->waitOpenOrders($spot); //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                $fee = $binanceHelper->getCommission($spot);
                $this->info($whileCounter."-Komisyon: ". $fee);
                LogHelper::orderLog("", $whileCounter."-Komisyon: ". $fee, $uniqueId);

                //Cüzdan daki para bilgisi alınıyor
                $walletCurrency = $binanceHelper->getWalletCurrency($currency);

                //Test için en düşük 20 dolardan alım yapılabilir.
                $walletCurrency = 160;
                $this->info($whileCounter."-Cüzdandaki ".$currency.": ". $walletCurrency);
                LogHelper::orderLog("",  $whileCounter."-Cüzdandaki ".$currency.": ". $walletCurrency, $uniqueId);


                $this->info($whileCounter."-Stabiletesi kontrol ediliyor...");
                LogHelper::orderLog("",$whileCounter."-Stabiletesi kontrol ediliyor...", $uniqueId);
                $buyPrice = $binanceHelper->getPaymentCoinAmount($this, $spot, $coinPurchase, 30, true);

                $this->info($whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice);
                LogHelper::orderLog("",$whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice, $uniqueId);


                $coinDigit = pow(10, $binanceHelper->getCoinPriceDigit($buyPrice)); //Coinin küsürat sayısının öğrenilmesi ve üstü alınarak sellPrice da düzeltme yapılması.


                // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BAŞLANGIÇ] ##################

                $commissionPercent = $fee; //Binance Komisyon
                $buyPiece = floor($walletCurrency / $buyPrice); //alınacak adet.

                // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BİTİŞ] ##################


                // ############# [SATIN ALMA BAŞLANGIÇ] #############
                $this->info($whileCounter."-Satın Alınacak Fiyat: ". $buyPrice);
                LogHelper::orderLog("Satın Alma",$whileCounter."-Satın Alınacak Fiyat: ". $buyPrice, $uniqueId);
                $this->info($whileCounter."-Satın Alınacak Adet: ". $buyPiece);
                LogHelper::orderLog("Satın Alma",$whileCounter."-Satın Alınacak Adet: ". $buyPiece, $uniqueId);
                $totalBuyPrice = $buyPrice * $buyPiece;
                $this->info($whileCounter."-Satın Alımında Toplam Ödenecek ".$currency.": ". $totalBuyPrice);
                LogHelper::orderLog("Satın Alma",$whileCounter."-Satın Alımında Toplam Ödenecek ".$currency.": ". $totalBuyPrice, $uniqueId);

                $this->info($whileCounter."-Satın Alma Limiti Koyma = Başlatıldı!");
                LogHelper::orderLog("Satın Alma Limit Koyma",$whileCounter."-Satın Alma Limiti Koyma = Başlatıldı!", $uniqueId);
                */
                $buyOrderId = $binanceHelper->buyCoin("XRPUSDT", 10, 0.50);

                $this->info($whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!");
                LogHelper::orderLog("Satın Alma Limit Koyma",$whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!", $uniqueId, $buyOrderId);


                dd("okk");
            }
        }else{
            LogHelper::log(1, "", "Coin Select", "Coin bulunamadı!");
        }
    }
}
