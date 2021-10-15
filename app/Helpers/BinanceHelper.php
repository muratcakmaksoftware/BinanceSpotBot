<?php

namespace App\Helpers;

use App\Models\Log;
use App\Models\Order;
use App\Models\OrderLog;
use \Binance;
use Carbon\Carbon;

class BinanceHelper{

    protected $api = null;
    protected $coinId = null;
    function __construct($coin_id, $test = false){
        $this->coinId = $coin_id;
        if($test){
            //$this->api = new Binance\API("NE2zfaJ3DeUi3E8slgkRp8tuzBjsQIqGXOJKPUtSSNkn3YhzQ2WIazskyb20m8nI", "fMhRLVEPFYe510tl4eAeQqUjSLW4igAwyLqKgiLA8bCkdpCgnmMbM0oAXe9MT8T4", true);
        }else{
            $this->api = new Binance\API(base_path('public/binance/config.json')); //original
            $this->api->caOverride = true;
        }
    }

    //Komisyon bilgisinin alınması.
    function getCommission($spot){
        while(true){
            try{
                $fee = $this->api->commissionFee($spot);
                /*
                 * Taker = direk piyasadan direk alma veya satma şeklinde uygulanan komisyondur ve daha yüksek komisyon alır.
                 * Maker = limit emirleriyle işlemlerde daha düşük komisyon almaktadır.
                   0 => array:3 [
                    "symbol" => "XRPUSDT"
                    "makerCommission" => "0.001"
                    "takerCommission" => "0.001"
                  ]
                 * */
                if(isset($fee[0]["makerCommission"])){
                    return $fee[0]["makerCommission"];
                }else{
                    $log = new Log;
                    $log->type = 1;
                    $log->coin_id = $this->coinId;
                    $log->title = "commissionFee 404";
                    $log->description = "Response Failed!";
                    $log->save();
                    sleep(2);
                }
            }catch (\Exception $e){
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "commissionFee API Error";
                $log->description = $e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }
        }
    }

    //Cüzdan daki para bilgisinin alınması
    public function getWalletCurrency($currency){
        while(true){
            try{
                $ticker = $this->api->prices();
                $balances = $this->api->balances($ticker)[$currency]; //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )
                return $balances["available"]; //Cüzdanımda kalan para birimi
            }catch (\Exception $e){
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "Balance API Error";
                $log->description = "Cüzdan Para Bilgisi Alınamadı: ".$e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }
        }
    }



    /**
     * Alınacak coinin miktarın stabiletisini kontrol etme.
     * @param $context
     * @param $spot
     * @param $coinPurchase
     * @param $sensitivity
     * @param bool $test
     * @return float
     */
    function getStabilizationPrice($context, $spot, $coinPurchase, $sensitivity, $test = false){

        $price = -1; //şu anda olan coin para birimi
        $priceUpLimit = -1; //şu anda olan coin biriminin sirkülasyon maks üst aralığı
        $priceDownLimit = -1; //şu anda olan coin biriminin sirkülasyon min alt aralığı
        $priceMaxMinStatus = false; // sirkülasyon aralığıbelirlenmiş mi ?
        $priceDiff = -1; //coin para briminin aralık farkının alınması
        $buyPriceCount = $sensitivity; //her 1 saniye de belirtlen X kere aynı para birim aralığındaysa limit emriyle satın alma işlemi gerçekleştirilecek.
        $buyPriceCounter = 0;

        while(true){

            try {
                $price = floatval($this->api->price($spot)); //örnek çıktı: 1.06735000
                if($test){ //hızlı test için stabilete ölcülmeden para birimi alınır.
                    return $price;
                }
                $context->warn("Stabiletesi ölçülüyor # ".$spot.": ".$price. " # TARİH:". Carbon::now()->format("d.m.Y H:i:s"));
                if($priceMaxMinStatus == false){
                    $priceDiff = $price * $coinPurchase;
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
                            return $price; //alınacak para birimi belirlendi döngü sonlandırıldı.
                        }else{
                            $buyPriceCounter++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "Price Error";
                $log->description = "Para Birimi Alınamadı. Detay: ". $e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }

            sleep(1); // 1 saniye de 1 kere para birimini kontrol et.
        }
    }

    /**
     * Satın alma limitinin eklenmesi.
     * @param $spot
     * @param $buyPiece
     * @param $buyPrice
     * @return mixed
     */
    function buyCoin($spot, $buyPiece, $buyPrice){
        while(true){
            try{
                /*
                 {
                   "symbol":"ADAUSDT",
                   "orderId":1128188745,
                   "orderListId":-1,
                   "clientOrderId":"2pOvnTiBwWlB0K4WfQMsMy",
                   "transactTime":1615919001851,
                   "price":"1.00000000",
                   "origQty":"10.00000000",
                   "executedQty":"0.00000000",
                   "cummulativeQuoteQty":"0.00000000",
                   "status":"NEW",
                   "timeInForce":"GTC",
                   "type":"LIMIT",
                   "side":"BUY",
                   "fills":[]
                }*/
                $buyStatus = $this->api->buy($spot, $buyPiece, $buyPrice);

                //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ?
                //NEW = Sipariş motor tarafından kabul edildi
                //FILLED = İŞLEM TAMAMEN GERÇEKLEŞTİ!
                //BUY satın alma olduğunda emin olmak için ek kontrol.
                if(($buyStatus["status"] == "NEW" || $buyStatus["status"] == "FILLED") && $buyStatus["side"] == "BUY") {
                    $order = new Order;
                    $order->coin_id = $this->coinId;
                    $order->orderId = $buyStatus["orderId"];
                    $order->symbol = $spot;
                    $order->side = $buyStatus["side"];
                    $order->origQty = $buyStatus["origQty"];
                    $order->price = $buyStatus["price"];
                    $order->type = $buyStatus["type"];
                    $order->status = $buyStatus["status"];
                    $order->var_piece = $buyPiece;
                    $order->var_price = $buyPrice;
                    $order->json_data = json_encode($buyStatus);
                    $order->save();
                    return $order->id;
                }else{
                    $log = new Log;
                    $log->type = 1;
                    $log->coin_id = $this->coinId;
                    $log->title = "buyCoin Status Error";
                    $log->description = "Satın alma limit farklı status değerine sahip. Data: ". json_encode($buyStatus);
                    $log->save();
                    sleep(2);
                }
            } catch (\Exception $e) {
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "buyCoin Limit Error";
                $log->description = "Satın Alma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }
        }
    }

    /**
     * //LİMİT EMRİ VERİLDİ VE LİMİT EMRİNİN GERÇEKLEŞMESİ BEKLENİYOR.
     * @param $spot
     * @param $order
     * @return bool
     */
    function getOrderStatus($spot, $order){
        while(true) {
            try{
                $orderStatus = $this->api->orderStatus($spot, $order->orderId);
                //işlem gerçekleşmiş. filled = Sipariş tamamlandı /// $orderStatus["status"] == "CANCELED" sipariş iptal edildiyse
                if($orderStatus["status"] == "FILLED"){
                    $order->status = $orderStatus["status"];
                    $order->save();
                    return true; //işlem gerçekleşmiş.
                }else{
                    sleep(2); // 2 saniye de 1 kere satın alınmış mı kontrolü
                }
            }catch (\Exception $e) {
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "Limit Status Error";
                $log->description = "Limit Emrinin Kontrolü Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }
        }
    }

    /**
     * Satış limitinin eklenmesi.
     * @param $spot
     * @param $sellPiece
     * @param $sellPrice
     * @return mixed
     */
    function sellCoin($spot, $sellPiece, $sellPrice){
        while(true){
            try{
                /*
                 {
                   "symbol":"TRXUSDT",
                   "orderId":701099196,
                   "orderListId":-1,
                   "clientOrderId":"lk9pIK7dpR9TPBuo1uPkJx",
                   "transactTime":1615926174851,
                   "price":"0.05800000",
                   "origQty":"500.00000000",
                   "executedQty":"0.00000000",
                   "cummulativeQuoteQty":"0.00000000",
                   "status":"NEW",
                   "timeInForce":"GTC",
                   "type":"LIMIT",
                   "side":"SELL",
                   "fills":[]
                }
                 * */
                $sellStatus = $this->api->sell($spot, $sellPiece, $sellPrice);
                //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ?
                // NEW = Sipariş motor tarafından kabul edildi &
                // Filled = tamamı başarı olmuş mu ?
                // SELL = satış olduğunda emin olmak için ek kontrol.
                if(($sellStatus["status"] == "NEW" || $sellStatus["status"] == "FILLED") && $sellStatus["side"] == "SELL") {
                    $order = new Order;
                    $order->coin_id = $this->coinId;
                    $order->orderId = $sellStatus["orderId"];
                    $order->symbol = $spot;
                    $order->side = $sellStatus["side"];
                    $order->origQty = $sellStatus["origQty"];
                    $order->price = $sellStatus["price"];
                    $order->type = $sellStatus["type"];
                    $order->status = $sellStatus["status"];
                    $order->var_piece = $sellPiece;
                    $order->var_price = $sellPrice;
                    $order->json_data = json_encode($sellStatus);
                    $order->save();

                    return $order->id;
                }else{
                    $log = new Log;
                    $log->type = 1;
                    $log->coin_id = $this->coinId;
                    $log->title = "sellCoin Status Error";
                    $log->description = "Satış yapma limiti farklı status değerine sahip. Data: ". json_encode($sellStatus);
                    $log->save();
                    sleep(2);
                }
            } catch (\Exception $e) {
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $this->coinId;
                $log->title = "sellCoin Limit Error";
                $log->description = "Satış Yapma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine();
                $log->save();
                sleep(5);
            }
        }
    }

    /**
     * Örnek MATICTRY belirtilen spot birimine daha önceden emir verilmişse emir gerçekleşene kadar bekletir.
     * @param $spot
     * @return bool
     */
    function waitOpenOrders($spot){
        while(true){
            try{
                /*
                 array:1 [
                      0 => array:18 [
                        "symbol" => "MATICTRY"
                        "orderId" => 17142850
                        "orderListId" => -1
                        "clientOrderId" => "web_3f5c4f5cabd442c9857ab71ddd244b87"
                        "price" => "12.50000000"
                        "origQty" => "559.90000000"
                        "executedQty" => "0.00000000"
                        "cummulativeQuoteQty" => "0.00000000"
                        "status" => "NEW"
                        "timeInForce" => "GTC"
                        "type" => "LIMIT"
                        "side" => "SELL"
                        "stopPrice" => "0.00000000"
                        "icebergQty" => "0.00000000"
                        "time" => 1633867470193
                        "updateTime" => 1633867470193
                        "isWorking" => true
                        "origQuoteOrderQty" => "0.00000000"
                      ]
                    ]
                 * */

                $openorders = $this->api->openOrders($spot);
                if(count($openorders) > 0){ //Daha önceden bir limit emri verilmiş gerçekleşmesi için beklenecek.
                    // limitin gerçekleşmesi bekleniyor.
                    sleep(2);
                }else{
                    return true; //limit emri bulunmadı.
                }
            }catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "Open Orders Bypass", "Daha önceden limit var mı kontrolü başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                sleep(5);
            }

        }

    }


    /**
     * ondalıklı sayısı kaç adet varsa sayar ör: 1.23444 = 5 döner
     * @param $price
     * @return int
     */
    function getCoinPriceDigit($price) {
        $exp = explode(".", strval($price));
        if(count($exp) > 1){
            return strlen($exp[1]);
        }else{
            return 1;
        }
    }



    /**
     * tüm para birimlerini alma
     * @return array
     * @throws \Exception
     */
    function getAllCoinPrices(){
        $ticker = $this->api->prices();
        return $ticker;
        //return print_r($ticker);
    }

}
