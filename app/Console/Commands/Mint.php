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
        $this->caOverride = true;

        $this->info("Spot Başlangıç: ". Carbon::now()->format("d.m.Y H:i:s"));

        $coin = Coin::where("id", 1)->first();
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
                $this->info($whileCounter."-SPOT ALGORITHM START");
                $this->info($whileCounter."-Önceden Yapılmış Siparişin İptali Bekleniyor...");
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

                    $this->info($whileCounter."-Cüzdandaki Dolar: ". $walletDolar);
                    /*
                    if($walletDolar > 15){ //test için sabitleme
                        $walletDolar = 15;
                    }

                    //Alınacak coinin miktarın stabiletisini kontrol etme.
                    $buyPrice = null; //Alınacak coinin fiyatı
                    while(true){
                        $buyPrice = getPaymentCoinAmount($api, $coin_id, $coin_usd, $coin_purchase);
                        if($buyPrice != null){
                            break;
                        }
                        sleep(1); //Alınacak coinin miktarı başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    // ##################################################

                    $commissionPercent = $fee; //Komisyon
                    $commissionReverse = 1 - $commissionPercent;
                    $buyPiece = floor($walletDolar / $buyPrice); //alınacak adet.

                    // ##################################################

                    //Satın alma limit eklenmesi.
                    $buyOrderId = null;
                    while(true){
                        $buyOrderId = buyCoin($api, $coin_id, $coin_usd, $buyPiece, $buyPrice);
                        if($buyOrderId != null){
                            break;
                        }
                        sleep(1); //Satın alma limitin eklenmesi başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    $buyOrder = Order::where("id", $buyOrderId)->first();

                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $buyOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    // ##################################################

                    //Satış yapılacak kar belirlenmesi
                    $unitPurchasePrice = $buyPrice / $commissionReverse; // Birim alış fiyatı //Komisyonlu halde satın aldığımız fiyat.
                    $unitSellPrice = ($unitPurchasePrice + $coin_profit ) * $commissionReverse; // Birim satış fiyatı ( satış yapılacak tutar )

                    // ##################################################

                    //Satış limitin oluşturulması
                    $sellOrderId = null;
                    while(true){
                        $sellOrderId = sellCoin($api, $coin_id, $coin_usd, $buyPiece, $unitSellPrice); //buyPiece Alındığı adet kadar satılacak.
                        if($sellOrderId != null){
                            break;
                        }
                        sleep(1); //Satış limitin eklenmesi başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    $sellOrder = Order::where("id", $sellOrderId)->first();

                    //Satın alma limiti gerçekleşmiş mi ?
                    while(true){
                        if(getOrderStatus($api, $coin_id, $coin_usd, $sellOrder)){
                            break;
                        }
                        sleep(1); //Satın alma limitin kontrolü başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    sleep(5); //Diğer alım satıma geçiş için bekletme.*/

                } //else konumuna gelemez bypass true olana kadar döngü içindedir.
                $this->info($whileCounter."-SPOT ALGORITHM END");
                sleep(5);
            }

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
