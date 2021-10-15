<?php

namespace App\Console\Commands;

use App\Helpers\BinanceHelper;
use App\Helpers\LogHelper;
use App\Models\Coin;
use App\Models\Order;
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
        $test = true;
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
                $this->info("-------------------------------");
                LogHelper::orderLog("","-------------------------------", $uniqueId);

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

                $buyPrice = $binanceHelper->getStabilizationPrice($this, $spot, $coinPurchase, 30, $test); //stabil fiyat alınıyor

                $this->info($whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice);
                LogHelper::orderLog("",$whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice, $uniqueId);


                $coinDigit = pow(10, $binanceHelper->getCoinPriceDigit($buyPrice)); //Coinin küsürat sayısının öğrenilmesi ve üstü alınarak sellPrice da düzeltme yapılması.


                // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BAŞLANGIÇ] ##################

                $commissionPercent = $fee; //Para birimine göre komisyon miktarı
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

                //SATIN ALMA LİMİTİNİN OLUŞTURMA İŞLEMİ
                $buyOrderId = $binanceHelper->buyCoin("XRPUSDT", 10, 0.50);

                $this->info($whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!");
                LogHelper::orderLog("Satın Alma Limit Koyma",$whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!", $uniqueId, $buyOrderId);

                $this->info($whileCounter."-Satın Alma Limitinin gerçekleşmesi bekleniyor...");
                LogHelper::orderLog("Satın Alma Limit",$whileCounter."-Satın Alma Limitinin gerçekleşmesi bekleniyor...", $uniqueId, $buyOrderId);

                $buyOrder = Order::where("id", $buyOrderId)->first();
                $binanceHelper->getOrderStatus($spot, $buyOrder);

                $this->info($whileCounter."-Satın Alma Limiti Başarıyla Gerçekleşti!");
                LogHelper::orderLog("Satın Alma Limit",$whileCounter."-Satın Alma Limiti Başarıyla Gerçekleşti!", $uniqueId, $buyOrderId);

                // ############# [SATIN ALMA BİTİŞ] #############

                // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BAŞLANGIÇ] #############

                //Satış yapılacak kar belirlenmesi
                $buyPieceCommission = $buyPiece * $commissionPercent; //Alım yapıldığında coin den komisyon düşümü.
                $this->info($whileCounter."-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: ". $buyPieceCommission);
                LogHelper::orderLog("Satın Alım Komisyon",$whileCounter."-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: ". $buyPieceCommission, $uniqueId, $buyOrderId);

                /* //Düşülmüş olan komisyon coin bilgisiyle = düşülmüş olan her adet için komisyon dolar bilgisini öğrenme.
                    Alınan adet = 188
                    188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                    0,0583665 * 0,188 = 0,010972902 // komisyon kesilen coinin toplam dolar karşılığı.
                    0,010972902 / 188 = 0,0000583665 // coin başına alınan komisyon dolar bilgisi.
                */
                $buyCommissionPrice = (($buyPrice * $buyPieceCommission) / $buyPiece);
                $this->info($whileCounter."-Adet başına kesilen komisyon ".$currency.": ". $buyCommissionPrice);
                LogHelper::orderLog("Satın Alım Komisyon",$whileCounter."-Adet başına kesilen komisyon ".$currency.": ". $buyCommissionPrice, $uniqueId, $buyOrderId);

                $sellPiece = $buyPiece - $buyPieceCommission; //satış için KALAN ADET
                $sellPiece = floor($sellPiece * 10) / 10; // satış için basamak düzeltme. ör: 10.989 => 10.9
                $this->info($whileCounter."-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: ". $sellPiece);
                LogHelper::orderLog("Satın Alım Komisyon",$whileCounter."-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: ". $sellPiece, $uniqueId);

                //SATIŞ İÇİN MİKTARIN BELİRLENMESİ
                //           ( alım miktar + kar artım )
                $sellPrice = $buyPrice + $coinProfit;

                //Satışta kesilecek komisyon bilgisi
                /*
                    Satış için kalan adet Adet = 188
                    188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                    0,0587266 * 0,188 = 0,0110406008 // komisyon kesilen coinin toplam dolar karşılığı.
                    0,0110406008 / 0,188 = 0,0587266 // coin başına alınan komisyon dolar bilgisi.
                */
                $sellCommissionPrice = ($sellPrice * ($sellPiece * $commissionPercent)) / $sellPiece; //satışta kesilecek olan adet başına komisyon doları
                $totalCommission = $buyCommissionPrice + $sellCommissionPrice; //Satın alım komisyon toplamı ve satışta alacak komisyon toplamı

                $this->info($whileCounter."-Her alım adeti için ödenen komisyon: ".$currency." ". $buyCommissionPrice);
                LogHelper::orderLog("Komisyon Analiz",$whileCounter."-Her alım adeti için ödenen komisyon: ".$currency." ". $buyCommissionPrice, $uniqueId);
                $this->info($whileCounter."-Her satım adeti için ödenen komisyon: ".$currency." ". $sellCommissionPrice);
                LogHelper::orderLog("Komisyon Analiz",$whileCounter."-Her satım adeti için ödenen komisyon: ".$currency." ". $sellCommissionPrice, $uniqueId);
                $this->info($whileCounter."-Her alım-satım adeti için toplam ödenen komisyon: ".$currency." ". $totalCommission);
                LogHelper::orderLog("Komisyon Analiz",$whileCounter."-Her alım+satım adeti için toplam ödenen komisyon: ".$currency." ". $totalCommission, $uniqueId);

                //###### SATIŞ LİMİT FİYATININ BELİRLENMESİ ######
                $sellPrice = $sellPrice + $totalCommission;
                $sellPrice = ceil($sellPrice * $coinDigit) / $coinDigit; //kusurat duzeltme örn: 1.2359069 => 1.23591

                // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BİTİŞ] #############


                // ################## [SATIŞ YAPMA BAŞLANGIÇ] ##################

                $this->info($whileCounter."-Satılacak Fiyat: ". $sellPrice);
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satılacak Fiyat: ". $sellPrice, $uniqueId);
                $this->info($whileCounter."-Satılacak Adet: ". $sellPiece);
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satılacak Adet: ". $sellPiece, $uniqueId);

                $this->info($whileCounter."-Satış Limiti Koyma = Başlatıldı!");
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satış Limiti Koyma = Başlatıldı!", $uniqueId);

                //SATIŞ LİMİNİTİN OLUŞTURMA İŞLEMİ
                $sellOrderId = $binanceHelper->sellCoin($spot, $sellPiece, $sellPrice);

                $this->info($whileCounter."-Satış Limiti Koyma = Başarıyla Koyuldu!");
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satış Limiti Koyma = Başarıyla Koyuldu!", $uniqueId);

                $sellOrder = Order::where("id", $sellOrderId)->first();

                $this->info($whileCounter."-Satış işleminin gerçekleşmesi bekleniyor...");
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satış işleminin gerçekleşmesi bekleniyor...", $uniqueId, $sellOrderId);

                //Satın alma limiti gerçekleşmiş mi ?
                $binanceHelper->getOrderStatus($spot, $sellOrder);

                $this->info($whileCounter."-Satın Limiti Başarıyla Gerçekleşti!");
                LogHelper::orderLog("Satış Yapma",$whileCounter."-Satın Limiti Başarıyla Gerçekleşti!", $uniqueId, $sellOrderId);

                // ################## [SATIŞ YAPMA BİTİŞ] ##################

                //Kar analiz
                $this->info($whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission));
                LogHelper::orderLog("Kâr",$whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission), $uniqueId);

                //(satış fiyati * satış adeti) - (satış komisyon fiyatı * satış adeti)
                $totalSellPrice = ($sellPrice * $sellPiece) - ($sellCommissionPrice * $sellPiece);
                $this->info($whileCounter."-Cüzdana Dönen ".$currency.": ". $totalSellPrice);
                LogHelper::orderLog("",$whileCounter."-Cüzdana Dönen ".$currency.": ". $totalSellPrice, $uniqueId);

                //satıştan sonra cüzdanda kalan para - alımdan sonra komisyon düşümüyle kalan fiyat
                $gain = $totalSellPrice - (($buyPrice * $buyPiece) - ($buyCommissionPrice * $buyPiece));
                $this->info($whileCounter."-Cüzdana Kazanç: ". $gain);
                LogHelper::orderLog("",$whileCounter."-Cüzdana Kazanç: ". $gain, $uniqueId);

                $this->info($whileCounter."-SPOT ALGORITHM END: ".Carbon::now()->format("d.m.Y H:i:s"));
                LogHelper::orderLog("", $whileCounter."-SPOT ALGORITHM END: ".Carbon::now()->format("d.m.Y H:i:s"), $uniqueId);
                $this->info("-------------------------------");
                LogHelper::orderLog("","-------------------------------", $uniqueId);

                dd("TEST OK OK OK OK !!!!");
                //Yeni Alım - Satım için
                $whileCounter++;
                sleep(1);
            }
        }else{
            LogHelper::log(1, "", "Coin Select", "Coin bulunamadı!");
        }
    }
}
