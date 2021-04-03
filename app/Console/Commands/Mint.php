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

        $coin = Coin::where("id", 3)->first();
        if(isset($coin)){
            $coin_id = $coin->id;
            $coin_name = $coin->name; //ADA
            $coin_usd = $coin->name_usd; //ADAUSDT
            $coin_profit = $coin->profit; // Satışta alacağımız kazanç miktarı
            $coin_purchase = $coin->purchase; // satın alma aralık  coin para birimi artı ve eksi aralığını belirler.
            $this->info("Coin ID: ". $coin_id);
            $this->info("Coin Adı: ". $coin_name);
            $this->info("Coin USD: ". $coin_usd);
            $this->info("Coin Sabit Kazanç: ". $coin_profit);
            $this->info("Coin Sürkülasyon Aralığı: ". $coin_purchase);


            $whileCounter = 1;
            while(true){
                $this->info($whileCounter."-SPOT ALGORITHM START:". Carbon::now()->format("d.m.Y H:i:s"));
                $this->info($whileCounter."-Önceden Yapılmış Siparişin Bitmesi Bekleniyor...");
                if(openOrdersByPass($api, $coin_id, $coin_usd)){ //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                    $this->info($whileCounter."-Orders Bypass OK!");

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

                    //Cüzdan daki dolar bilgisi alınıyor
                    $walletDolar = null;
                    while(true){
                        $walletDolar = getWalletDolar($api, $coin_id);
                        if($walletDolar != null){
                            break;
                        }
                        sleep(1);  //Cüzdan dolar bilgisi alınması başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    //Test için en düşük 10 dolardan alım yapılabilir.
                    $walletDolar = 21;
                    $this->info($whileCounter."-Cüzdandaki Dolar: ". $walletDolar);

                    //$this->info($whileCounter."-Cüzdandaki Dolar: ". $walletDolar);

                    $this->info($whileCounter."-Stabiletesi kontrol ediliyor...");
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

                    // ##################################################

                    $commissionPercent = $fee; //Komisyon
                    $commissionReverse = 1 - $commissionPercent;
                    $buyPiece = floor($walletDolar / $buyPrice); //alınacak adet.

                    // ##################################################

                    // ############# SATIN ALMA #############

                    $this->info($whileCounter."-Satın Alınacak Fiyat: ". $buyPiece);
                    $this->info($whileCounter."-Satın Alınacak Adet: ". $buyPiece);

                    $this->info($whileCounter."-Satın Alma Limiti Koyma = Başlatıldı!");
                    //Satın alma limit eklenmesi.
                    $buyOrderId = buyCoin($api, $coin_id, $coin_usd, $buyPiece, $buyPrice);

                    if($buyOrderId == null){
                        $whileCounter++;
                        break; //HATA ALINDIYSA DÖNGÜ SONLANDIR.
                    }
                    $this->info($whileCounter."-Satın Alma Limiti = Başarıyla Koyuldu!");

                    $this->info($whileCounter."-Satın Alma Limitinin gerçekleşmesi bekleniyor...");
                    $buyOrder = Order::where("id", $buyOrderId)->first();
                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $buyOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Satın Alma Limiti Başarıyla Gerçekleşti!");

                    // ##################################################

                    //Satış yapılacak kar belirlenmesi
                    $unitPurchasePrice = $buyPrice / $commissionReverse; // Birim alış fiyatı //Komisyonlu halde satın aldığımız fiyat.
                    $unitSellPrice = ($unitPurchasePrice + $coin_profit ) * $commissionReverse; // Birim satış fiyatı ( satış yapılacak tutar )

                    $this->info($whileCounter."-Satılacak Fiyat: ". $unitSellPrice);
                    $this->info($whileCounter."-Satılacak Adet: ". $buyPiece);

                    // ##################################################

                    // ############# SATIŞ YAPMA #############

                    $this->info($whileCounter."-Satış Limiti Koyma = Başlatıldı!");
                    //Satış limitin oluşturulması
                    $sellOrderId = sellCoin($api, $coin_id, $coin_usd, $buyPiece, $unitSellPrice); //buyPiece Alındığı adet kadar satılacak.

                    if($sellOrderId == null){
                        $whileCounter++;
                        break; //HATA ALINDIYSA DÖNGÜ SONLANDIR.
                    }
                    $this->info($whileCounter."-Satış Limiti Koyma = Başarıyla Koyuldu!");

                    $sellOrder = Order::where("id", $sellOrderId)->first();

                    $this->info($whileCounter."-Satış işleminin gerçekleşmesi bekleniyor...");
                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $sellOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }
                    $this->info($whileCounter."-Satın Limiti Başarıyla Gerçekleşti!");

                } //else konumuna gelemez bypass true olana kadar döngü içindedir.
                $this->info($whileCounter."-SPOT ALGORITHM END: ".Carbon::now()->format("d.m.Y H:i:s"));
                $whileCounter++;
                sleep(5); ////Diğer alım satıma geçiş için bekletme
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
