<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Log;
use \Binance;

class HomeController extends Controller
{
    public function index(){
        $api = new Binance\API( base_path('public/binance/config.json')); //orjinal

        //TEST kısmını güncelleyecekler
        //$api = new Binance\API("NE2zfaJ3DeUi3E8slgkRp8tuzBjsQIqGXOJKPUtSSNkn3YhzQ2WIazskyb20m8nI", "fMhRLVEPFYe510tl4eAeQqUjSLW4igAwyLqKgiLA8bCkdpCgnmMbM0oAXe9MT8T4", true);
        //$buyStatus = $api->buyTest("BNBBTC", 1, 1.20);
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
        //$orderstatus = $api->orderStatus("ADAUSDT", $openorders["orderId"]); // limit hakkında bilgi alma

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

                /*$openorders = $api->openOrders($coin_usd); // Array ( [0] => Array ( [symbol] => ADAUSDT [orderId] => 1061155402 [orderListId] => -1 [clientOrderId] => and_7ef0d8f35bd3440ca487d811e99515b1 [price] => 1.23000000 [origQty] => 100.00000000 [executedQty] => 0.00000000 [cummulativeQuoteQty] => 0.00000000 [status] => NEW [timeInForce] => GTC [type] => LIMIT [side] => SELL [stopPrice] => 0.00000000 [icebergQty] => 0.00000000 [time] => 1614853539597 [updateTime] => 1614853539597 [isWorking] => 1 [origQuoteOrderQty] => 0.00000000 ) )
                if(count($openorders) > 0){ //Daha önceden bir limit emri verilmiş gerçekleşmesi için beklenecek.
                    // limitin gerçekleşmesi bekleniyor.
                    sleep(10);
                }else{*/
                    $tradeFee = $api->tradeFee($coin_usd); //{"success":true,"tradeFee":[{"maker":0.001,"symbol":"ADAUSDT","taker":0.001}]}
                    if($tradeFee["success"]){

                        if(isset($tradeFee["tradeFee"][0]["maker"])){
                            $fee = $tradeFee["tradeFee"][0]["maker"];

                            $ticker = $api->prices();
                            $balances = $api->balances($ticker)["USDT"]; //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )

                            $walletDolar = $balances["available"]; //Cüzdanımda kalan DOLAR para birimi
                            if($walletDolar > 5){ // test için 5 dolardan çok bakiye varsa 5 dolar olarak sabitle.
                                $walletDolar = 5;
                            }

                            $commissionPercent = $fee; //Komisyon
                            $commissionReverse = 1 - $commissionPercent;

                            //Miktarın stabiletisini kontrol etme.
                            $priceStatus = true; //alım limiti belirlenmiş mi ?

                            $price = -1; //şu anda olan coin para birimi
                            $priceUpLimit = -1; //şu anda olan coin biriminin sirkülasyon maks üst aralığı
                            $priceDownLimit = -1; //şu anda olan coin biriminin sirkülasyon min alt aralığı
                            $priceMaxMinStatus = false; // sirkülasyon aralığıbelirlenmiş mi ?
                            $priceDiff = -1; //coin para briminin aralık farkının alınması
                            $buyPriceCount = 30; //her 1 saniye de 30 kere aynı para birim aralığındaysa limit emriyle satın alma işlemi gerçekleştirilecek.
                            $buyPriceCounter = 0;

                            $buyPrice = -1; //
                            while($priceStatus){

                                try {
                                    $price = floatval($api->price($coin_usd)); //örnek çıktı: 1.06735000

                                    if($priceMaxMinStatus == false){
                                        $priceDiff = $price * $coin_purchase;
                                        $priceUpLimit = $price + $priceDiff;
                                        $priceDownLimit = $price - $priceDiff;
                                        $priceMaxMinStatus = true;
                                    }else{
                                        if($price > $priceUpLimit){ //max değeri aşılmış
                                            $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                                        }else if($priceDownLimit > $price){ //min değeri aşılmış
                                            $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                                        }else{ //Belirtilen min maks değerinin içerisinde
                                            if($buyPriceCounter == $buyPriceCount){ //aralık aynı seyirde devam ettiyse girer.
                                                $buyPrice = $price;
                                                $priceStatus = false; //alınacak para birimi belirlendi döngü sonlandırıldı.
                                            }else{
                                                $buyPriceCounter++;
                                            }
                                        }
                                    }

                                } catch (\Exception $e) {
                                    $log = new Log;
                                    $log->type = 2;
                                    $log->coin_id = $coin_id;
                                    $log->title = "Price Error";
                                    $log->description = "Para Birimi Alınamadı. Detay: ". $e->getMessage();
                                    $log->save();
                                    sleep(5); //hata alırsa 5 saniye bekledikten sonra devam ettir kontrolü.
                                }

                                if($priceStatus){ //para birimi bulunmuş mu = true bulunmamış / false bulunmuş bu yüzden beklemeyi atlayacak.
                                    sleep(1); // 1 saniye de 1 kere para birimini kontrol et.
                                }
                            }


                            return "test koruması";
                            $buyPiece = floor($walletDolar / $buyPrice); //alınacak adet.
                            //Satın alma limit eklenmesi.
                            $buyStatus = $api->buy($coin_usd, $buyPiece, $buyPrice);
                            if(isset($buyStatus)){
                            } //BAŞARISIZ OLURSA Genel try catch atılacak

                            // Devam edilecek
                            /*
                             * Buy işlemi doğru olursa yapılan işlem bilgileri veritabanına atılacak.
                             * Daha sonra satım emri belirlenecek ve satım emrinin bilgileride veritabanına atılacak.
                             * Veritabanında alım satım emirlerinin gerçekleştirlip gerçekleşmediğini API ile kontrol edilip veritabanında kontrol edildiğinde / true yapılıp tekrar kontrol engellenecek.
                             * Alım satım bilgileriyle kazanç bilgisi sitede yansıtılacak.
                             * */

                        }else{
                            $log = new Log;
                            $log->type = 2;
                            $log->coin_id = $coin_id;
                            $log->title = "TradeFee Data Error";
                            $log->description = "Komisyon bedeli bulunamadı!";
                            $log->save();
                        }

                    }else{
                        $log = new Log;
                        $log->type = 2;
                        $log->coin_id = $coin_id;
                        $log->title = "TradeFee 404";
                        $log->description = "Response Failed!";
                        $log->save();
                    }
                //}

                break; // TEST İÇİN
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
