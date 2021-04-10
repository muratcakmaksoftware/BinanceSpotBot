<?php

namespace App\Console\Commands;

use App\Models\Coin;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use \Binance;

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

        $api = new Binance\API( base_path('public/binance/config.json')); //orjinal
        $api->caOverride = true;

        $this->info("Spot Başlangıç: ". Carbon::now()->format("d.m.Y H:i:s"));

        $coin = Coin::where("id", 5)->first();
        if(isset($coin)){

            $coin_id = $coin->id;
            $coin_name = $coin->name; //ADA
            $coin_usd = $coin->name_usd; //ADAUSDT
            $coin_profit = $coin->profit; // Satışta alacağımız kazanç miktarı
            $coin_purchase = $coin->purchase; // satın alma aralık  coin para birimi artı ve eksi aralığını belirler.
            $this->info("Coin ID: ". $coin_id);
            orderLogAdd("Coin ID", $coin_id);
            $this->info("Coin Adı: ". $coin_name);
            orderLogAdd("Coin Adı", $coin_name);
            $this->info("Coin USD: ". $coin_usd);
            orderLogAdd("Coin USD", $coin_usd);
            $this->info("Coin Sabit Kazanç: ". $coin_profit);
            orderLogAdd("Coin Sabit Kazanç", $coin_profit);
            $this->info("Coin Sürkülasyon Aralığı: ". $coin_purchase);
            orderLogAdd("Coin Sürkülasyon Aralığı", $coin_purchase);


            $whileCounter = 1;
            while(true){
                $unique_id = uniqid(rand(), true);
                $this->info("-------------------------------");
                orderLogAdd("", "-------------------------------", $unique_id);
                $this->info($whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"));
                orderLogAdd("", $whileCounter."-SPOT ALGORITHM START: ". Carbon::now()->format("d.m.Y H:i:s"), $unique_id);
                $this->info($whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...");
                orderLogAdd("", $whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...", $unique_id);
                if(openOrdersByPass($api, $coin_id, $coin_usd)){ //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                    $this->info($whileCounter."-Orders Bypass OK!");
                    orderLogAdd("", $whileCounter."-Orders Bypass OK!", $unique_id);

                    //Komisyon bilgisinin alınması.
                    $fee = null;
                    while(true){
                        $fee = getCommission($api, $coin_id, $coin_usd);
                        if($fee != null){  //komisyon bilgisi başarıyla alındı.
                            break; //döngü sonlandırıldı.
                        }
                        sleep(1); //Komisyon bilgisi alınması başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Komisyon: ". $fee);
                    orderLogAdd("", $whileCounter."-Komisyon: ". $fee, $unique_id);

                    //Cüzdan daki dolar bilgisi alınıyor
                    $walletDolar = null;
                    while(true){
                        $walletDolar = getWalletDolar($api, $coin_id);
                        if($walletDolar != null){
                            break;
                        }
                        sleep(1);  //Cüzdan dolar bilgisi alınması başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    //Test için en düşük 20 dolardan alım yapılabilir.
                    $walletDolar = 20;
                    $this->info($whileCounter."-Cüzdandaki Dolar: ". $walletDolar);
                    orderLogAdd("", $whileCounter."-Cüzdandaki Dolar: ". $walletDolar, $unique_id);

                    $this->info($whileCounter."-Stabiletesi kontrol ediliyor...");
                    orderLogAdd("", $whileCounter."-Stabiletesi kontrol ediliyor...", $unique_id);
                    //Alınacak coinin miktarın stabiletisini kontrol etme.
                    $buyPrice = null; //Alınacak coinin fiyatı
                    while(true){
                        $buyPrice = getPaymentCoinAmount($api, $coin_id, $coin_usd, $coin_purchase);
                        if($buyPrice != null){
                            break;
                        }
                        sleep(1); //Alınacak coinin miktarı başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice);
                    orderLogAdd("", $whileCounter."-Stabiletesi bulunmuş Fiyat: ". $buyPrice, $unique_id);

                    $coin_digit = pow(10, countDecimals($buyPrice)); //Coinin küsürat sayısının öğrenilmesi ve üstü alınarak sellPrice da düzeltme yapılması.

                    // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BAŞLANGIÇ] ##################

                    $commissionPercent = $fee; //Binance Komisyon
                    $buyPiece = floor($walletDolar / $buyPrice); //alınacak adet.


                    // ################## [KOMİSYON VE ALINACAK ADETIN BELİRLENMESİ BİTİŞ] ##################



                    // ############# [SATIN ALMA BAŞLANGIÇ] #############

                    $this->info($whileCounter."-Satın Alınacak Fiyat: ". $buyPrice);
                    orderLogAdd("Satın Alma", $whileCounter."-Satın Alınacak Fiyat: ". $buyPrice, $unique_id);
                    $this->info($whileCounter."-Satın Alınacak Adet: ". $buyPiece);
                    orderLogAdd("Satın Alma", $whileCounter."-Satın Alınacak Adet: ". $buyPiece, $unique_id);
                    $buyDolar = $buyPrice * $buyPiece;
                    $this->info($whileCounter."-Satın Alışda ödenecek dolar: ". $buyDolar);
                    orderLogAdd("Satın Alma", $whileCounter."-Satın Alışda ödenecek dolar: ". ($buyPrice * $buyPiece), $unique_id);

                    $this->info($whileCounter."-Satın Alma Limiti Koyma = Başlatıldı!");
                    orderLogAdd("Satın Alma Limit Koyma", $whileCounter."-Satın Alma Limiti Koyma = Başlatıldı!", $unique_id);
                    //Satın alma limit eklenmesi.
                    $buyOrderId = buyCoin($api, $coin_id, $coin_usd, $buyPiece, $buyPrice);

                    if($buyOrderId == null){
                        break; //HATA ALINDIYSA DÖNGÜ SONLANDIR.
                    }
                    $this->info($whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!");
                    orderLogAdd("Satın Alma Limit Koyma", $whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!", $unique_id, $buyOrderId);

                    $this->info($whileCounter."-Satın Alma Limitinin gerçekleşmesi bekleniyor...");
                    orderLogAdd("Satın Alma Limit", $whileCounter."-Satın Alma Limitinin gerçekleşmesi bekleniyor...", $unique_id, $buyOrderId);
                    $buyOrder = Order::where("id", $buyOrderId)->first();
                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $buyOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Satın Alma Limiti Başarıyla Gerçekleşti!");
                    orderLogAdd("Satın Alma Limit", $whileCounter."-Satın Alma Limiti Başarıyla Gerçekleşti!", $unique_id, $buyOrderId);

                    // ############# [SATIN ALMA BİTİŞ] #############



                    // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BAŞLANGIÇ] #############

                    //Satış yapılacak kar belirlenmesi
                    $buyPieceCommission = $buyPiece * $commissionPercent; //Alım yapıldığında coin den komisyon düşümü.
                    $this->info($whileCounter."-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: ". $buyPieceCommission);
                    orderLogAdd("Satın Alım Komisyon", $whileCounter."-Satın Alma komisyon düşümü için alınan adette düşülecek Adet: ". $buyPieceCommission, $unique_id);

                    /* //Düşülmüş olan komisyon coin bilgisiyle = düşülmüş olan her adet için komisyon dolar bilgisini öğrenme.
                        Alınan adet = 188
                        188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                        0,0583665 * 0,188 = 0,010972902 // komisyon kesilen coinin toplam dolar karşılığı.
                        0,010972902 / 188 = 0,0000583665 // coin başına alınan komisyon dolar bilgisi.
                    */
                    $buyCommissionPrice = (($buyPrice * $buyPieceCommission) / $buyPiece);
                    $this->info($whileCounter."-Adet başına kesilen komisyon doları: ". $buyCommissionPrice);
                    orderLogAdd("Satın Alım Komisyon", $whileCounter."-Adet başına kesilen komisyon doları: ". $buyCommissionPrice, $unique_id);

                    $sellPiece = $buyPiece - $buyPieceCommission; //satış için KALAN ADET
                    $sellPiece = floor($sellPiece * 10) / 10; // satış için basamak düzeltme. ör: 10.989 => 10.9
                    $this->info($whileCounter."-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: ". $sellPiece);
                    orderLogAdd("Satın Alma Komisyon", $whileCounter."-Satın aldıktan sonra komisyon adetten düşmüş ve satış için kalan adet: ". $sellPiece, $unique_id);

                    //Satış miktarının belirlenmesi.

                    //           (alım miktar + kar artım )
                    $tolerance = 0.002;
                    $sellPrice = $buyPrice + $coin_profit + $tolerance;

                    //Satışta kesilecek komisyon bilgisi
                    /*
                        Satış için kalan adet Adet = 188
                        188 * 0,001 = 0,188 = toplam kesilen komisyon miktarı COİN
                        0,0587266 * 0,188 = 0,0110406008 // komisyon kesilen coinin toplam dolar karşılığı.
                        0,0587266 * 0,188 = 0,0587266 // coin başına alınan komisyon dolar bilgisi.
                    */
                    $sellCommissionPrice = ($sellPrice * ($sellPiece * $commissionPercent)) / $sellPiece; //satışta kesilecek olan adet başına komisyon doları
                    $totalCommission = $buyCommissionPrice + $sellCommissionPrice; //Satın alım komisyon toplamı ve satışta alacak komisyon toplamı

                    $this->info($whileCounter."-Her alım adeti için ödenen komisyon: $". $buyCommissionPrice);
                    orderLogAdd("Komisyon Analiz", $whileCounter."-Her alım adeti için ödenen komisyon: $". $buyCommissionPrice, $unique_id);
                    $this->info($whileCounter."-Her satım adeti için ödenen komisyon: $". $sellCommissionPrice);
                    orderLogAdd("Komisyon Analiz", $whileCounter."-Her satım adeti için ödenen komisyon: $". $sellCommissionPrice, $unique_id);
                    $this->info($whileCounter."-Toplam Ödenen komisyon: $". $totalCommission);
                    orderLogAdd("Komisyon Analiz", $whileCounter."-Toplam Ödenen komisyon: $". $totalCommission, $unique_id);

                    $sellPrice = $sellPrice + $totalCommission;
                    $sellPrice = ceil($sellPrice * $coin_digit) / $coin_digit; //kusurat duzeltme örn: 1.2359069 => 1.23591

                    // ############# [SATIŞ BİLGİLERİNİN HESAPLANMA İŞLEMLERİ BİTİŞ] #############



                    // ################## [SATIŞ YAPMA BAŞLANGIÇ] ##################

                    $this->info($whileCounter."-Satılacak Fiyat: ". $sellPrice);
                    orderLogAdd("Satış Yapma", $whileCounter."-Satılacak Fiyat: ". $sellPrice, $unique_id);
                    $this->info($whileCounter."-Satılacak Adet: ". $sellPiece);
                    orderLogAdd("Satış Yapma", $whileCounter."-Satılacak Adet: ". $sellPiece, $unique_id);

                    $this->info($whileCounter."-Satış Limiti Koyma = Başlatıldı!");
                    orderLogAdd("Satış Yapma", $whileCounter."-Satış Limiti Koyma = Başlatıldı!", $unique_id);
                    //Satış limitin oluşturulması
                    $sellOrderId = sellCoin($api, $coin_id, $coin_usd, $sellPiece, $sellPrice); //buyPiece Alındığı adet kadar satılacak.

                    if($sellOrderId == null){
                        break; //HATA ALINDIYSA DÖNGÜ SONLANDIR.
                    }
                    $this->info($whileCounter."-Satış Limiti Koyma = Başarıyla Koyuldu!");
                    orderLogAdd("Satış Yapma", $whileCounter."-Satış Limiti Koyma = Başarıyla Koyuldu!", $unique_id, $sellOrderId);

                    $sellOrder = Order::where("id", $sellOrderId)->first();

                    $this->info($whileCounter."-Satış işleminin gerçekleşmesi bekleniyor...");
                    orderLogAdd("Satış Yapma", $whileCounter."-Satış işleminin gerçekleşmesi bekleniyor...", $unique_id, $sellOrderId);
                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $sellOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Satın Limiti Başarıyla Gerçekleşti!");
                    orderLogAdd("Satış Yapma", $whileCounter."-Satın Limiti Başarıyla Gerçekleşti!", $unique_id, $sellOrderId);

                    // ################## [SATIŞ YAPMA BİTİŞ] ##################

                    //Kar analiz
                    $this->info($whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission));
                    orderLogAdd("Kâr", $whileCounter."-Toplam Kâr: ". floatval(($sellPrice - $buyPrice) - $totalCommission), $unique_id);

                    $sellDolar = $sellPrice * $sellPiece;
                    $this->info($whileCounter."-Cüzdana Dönen Dolar: ". $sellDolar);
                    orderLogAdd("", $whileCounter."-Cüzdana Dönen Dolar: ". $sellDolar, $unique_id);

                    $gain = $sellDolar - $buyDolar;
                    $this->info($whileCounter."-Cüzdana Kazanç: ". $gain);
                    orderLogAdd("", $whileCounter."-Cüzdana Kazanç: ". $gain, $unique_id);

                } //else konumuna gelemez bypass true olana kadar döngü içindedir.
                $this->info($whileCounter."-SPOT ALGORITHM END: ".Carbon::now()->format("d.m.Y H:i:s"));
                orderLogAdd("", $whileCounter."-SPOT ALGORITHM END: ".Carbon::now()->format("d.m.Y H:i:s"), $unique_id);
                $this->info("-------------------------------");
                orderLogAdd("","-------------------------------", $unique_id);

                //Yeni Alım - Satım için
                $whileCounter++;
                sleep(5);
            } // end while


        }else{
            $log = new \App\Models\Log;
            $log->type = 2;
            $log->coin_id = null;
            $log->title = "Coin Select";
            $log->description = "Coin bulunamadı!";
            $log->save();
        }

        $this->info("Spot Bitiş: ". Carbon::now()->format("d.m.Y H:i:s"));
    }
}
