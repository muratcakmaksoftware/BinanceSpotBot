<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Log;
use App\Models\Order;
use \Binance;

class HomeController extends Controller
{
    public function index(){
        $api = new Binance\API( base_path('public/binance/config.json')); //orjinal
        //TEST kısmını güncelleyecekler
        //$api = new Binance\API("NE2zfaJ3DeUi3E8slgkRp8tuzBjsQIqGXOJKPUtSSNkn3YhzQ2WIazskyb20m8nI", "fMhRLVEPFYe510tl4eAeQqUjSLW4igAwyLqKgiLA8bCkdpCgnmMbM0oAXe9MT8T4", true);
        //$buyStatus = $api->buyTest("XRPUSDT", 10, 1.59);
        //return $buyStatus;

        //$ticker = $api->prices();// tüm para birimlerini alma
        //return print_r($ticker);

        //return $api->price("ADAUSDT"); //sadece ADA para biriminin usdt karşılığını öğreme

        /*
            $quantity = 1;
            $price = 0.0005;
            $order = $api->buy("BNBBTC", $quantity, $price);
            $quantity = 1;
            $price = 0.0006;
            $order = $api->sell("BNBBTC", $quantity, $price);
         * */

        //24 saatlik istatistik alma
        //$prevDay = $api->prevDay("ADAUSDT");
        //return print_r($prevDay);
        //echo "BNB price change since yesterday: ".$prevDay['priceChangePercent']."%".PHP_EOL;

        //$history = $api->history("ADAUSDT");
        //return print_r($history);

        //derinlik
        //$depth = $api->depth("ADAUSDT");
        //return print_r($depth);

        //Periods: 1m,3m,5m,15m,30m,1h,2h,4h,6h,8h,12h,1d,3d,1w,1M
        //$ticks = $api->candlesticks("ADAUSDT", "5m");
        //return print_r($ticks);

        //Kalan bakiye sorgulama ve pazarda olan bakiye bilgisini alma
        $ticker = $api->prices();
        $balances = $api->balances($ticker)["ADA"]; //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )
        //$balances["available"]; // mevcut olan bakiye
        //$balances["onOrder"]; // pazarda olan bakiye
        $coinPrice = $api->price("ADAUSDT"); // 1.106310001 // ADA nın satın alabileceğimiz USDT de karşılığı

        //Limit Emir Bilgilerini Alma
        $openorders = $api->openOrders("ADAUSDT")[0]; // Array ( [0] => Array ( [symbol] => ADAUSDT [orderId] => 1061155402 [orderListId] => -1 [clientOrderId] => and_7ef0d8f35bd3440ca487d811e99515b1 [price] => 1.23000000 [origQty] => 100.00000000 [executedQty] => 0.00000000 [cummulativeQuoteQty] => 0.00000000 [status] => NEW [timeInForce] => GTC [type] => LIMIT [side] => SELL [stopPrice] => 0.00000000 [icebergQty] => 0.00000000 [time] => 1614853539597 [updateTime] => 1614853539597 [isWorking] => 1 [origQuoteOrderQty] => 0.00000000 ) )
        //$openorders["status"] // NEW
        //$openorders["symbol"]; // coin adını verir örn: ADAUSDT
        //$openorders["orderId"]; // Limit ID bilgisi
        //$openorders["origQty"]; // Limite verilen ADA bilgisi
        //$openorders["price"]; // Limitin işlem göreceği para birimi
        //$openorders["side"]; // Limitin SELL VEYA BUY parametresi döner alım mı satım mı limitinin bilgisidir.

        //$openorders["orderId"]; Limit ID bilgisi
        //$orderstatus = $api->orderStatus("ADAUSDT", $openorders["orderId"]); // bilgi alma : {"symbol":"ADAUSDT","orderId":1197572803,"orderListId":-1,"clientOrderId":"and_d020b57a54254ab6a427c90bd5023cd3","price":"1.05000000","origQty":"87.70000000","executedQty":"0.00000000","cummulativeQuoteQty":"0.00000000","status":"NEW","timeInForce":"GTC","type":"LIMIT","side":"BUY","stopPrice":"0.00000000","icebergQty":"0.00000000","time":1616962113169,"updateTime":1616962113169,"isWorking":true,"origQuoteOrderQty":"0.00000000"}

        //Limit iptal etme.
        //$cancelOrder = $api->cancel("ADAUSDT", $openorders["orderId"]); //limiti iptal etme. // Array ( [symbol] => ADAUSDT [origClientOrderId] => and_7ef0d8f35bd3440ca487d811e99515b1 [orderId] => 1061155402 [orderListId] => -1 [clientOrderId] => V4ZYosXSyxWqthHqds7NiL [price] => 1.23000000 [origQty] => 100.00000000 [executedQty] => 0.00000000 [cummulativeQuoteQty] => 0.00000000 [status] => CANCELED [timeInForce] => GTC [type] => LIMIT [side] => SELL )
        //$cancelOrder["status"] //CANCELED ise başarılı bir iptal işlemi

        //Yüzde alma (10 / 100) * X(0.1) yüzdesi = 0,01

        //SENARYO
        //ADA 1.13 rakamında
        //ADA nın %0,02 ARTIŞ İLE ALIYORUM (1,13 / 100) * 0.02 = 0,000226 1 TANE ADADAN KAZANCIMIZ.
        //10 ADET ADA * 0,000226 = 0,00226
        //1,13 + 0,00226 = 1,13226 // 10 adet ADA nın 0,02 artışla alımı
        ////////////////////////---------------
        //1,13 * 10 ADET ADA = 11,3 $
        //(11,3 / 100) * 0,02 = 0,00226
        //1,13 + 0,00226 = 1,13226

        //binance bizden 0,1% komisyon almakta.


        //return print_r($coinPrice);

        //TradeFee
        /*
          *Piyasa Yapıcı (Maker) işlem yapan kullanıcı satış ve alış için coin fiyatını kendisi belirler ve istediği fiyattan satış ve alış emri girer. İstediği fiyattan emir girdiği için ve bu mevcut fiyattan ucuz veya pahalı olduğu için işlemi hemen gerçekleşmez.
          *Piyasa Alıcı (Taker) işlem yapan kullanıcı satış ve alış için coin fiyatını kendisi belirlemez, mevcut anlık fiyattan alım veya satım yapar. İşlemi hemen gerçekleşir. Kolay Alış-Satış bölümündeki tüm işlemler Piyasa Alıcı (Taker) işlemidir.
        */



        $coin = Coin::where("id", 1)->first();
        if(isset($coin)){
            $coin_id = $coin->id;
            $coin_name = $coin->name; //ADA
            $coin_usd = $coin->name_usd; //ADAUSDT
            $coin_profit = $coin->profit; // Satışta alacağımız kazanç miktarı
            $coin_purchase = $coin->purchase; // satın alma aralık  coin para birimi artı ve eksi aralığını belirler.

            while(true){

                if(openOrdersByPass($api, $coin_id, $coin_usd)){ //Beklemede olan açık emir yoksa algoritmaya giriş yapabilir!
                    //Komisyon bilgisinin alınması.
                    $fee = null;
                    while(true){
                        $fee = getCommission($api, $coin_id, $coin_usd);
                        if($fee != null){  //komisyon bilgisi başarıyla alındı.
                            break; //döngü sonlandırıldı.
                        }
                        sleep(1); //Komisyon bilgisi alınması başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

                    //Cüzdan daki dolar bilgisi alınıyor
                    $walletDolar = null;
                    while(true){
                        $walletDolar = getWalletDolar($api, $coin_id);
                        if($walletDolar != null){
                            break;
                        }
                        sleep(1);  //Cüzdan dolar bilgisi alınması başarısız oldu. 1 saniye sonra tekrar denenecek.
                    }

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

                    sleep(5); //Diğer alım satıma geçiş için bekletme.

                } //else konumuna gelemez bypass true olana kadar döngü içindedir.
            }

        }else{
            $log = new Log;
            $log->type = 2;
            $log->coin_id = null;
            $log->title = "Coin Select";
            $log->description = "Coin bulunamadı!";
            $log->save();
        }

        return view('home.index');
    }
}
