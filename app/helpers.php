<?php

use App\Models\Log;
use App\Models\Order;

if(!function_exists('testim')){

    function testim(){
        return "okdsas";
    }
}

//Komisyon bilgisinin alınması.
function getCommission($api, $coin_id, $coin_usd){
    try{
        $tradeFee = $api->tradeFee($coin_usd); //{"success":true,"tradeFee":[{"maker":0.001,"symbol":"ADAUSDT","taker":0.001}]}
        if($tradeFee["success"]){
            if(isset($tradeFee["tradeFee"][0]["maker"])){
                return $tradeFee["tradeFee"][0]["maker"]; //fee
            }else{
                $log = new Log;
                $log->type = 2;
                $log->coin_id = $coin_id;
                $log->title = "TradeFee Data Error";
                $log->description = "Komisyon bedeli bulunamadı!";
                $log->save();

                return null;
            }

        }else{
            $log = new Log;
            $log->type = 2;
            $log->coin_id = $coin_id;
            $log->title = "TradeFee 404";
            $log->description = "Response Failed!";
            $log->save();

            return null;
        }
    }catch (\Exception $e){
        $log = new Log;
        $log->type = 2;
        $log->coin_id = $coin_id;
        $log->title = "TradeFee API Error";
        $log->description = $e->getMessage();
        $log->save();
        return null;
    }

}

//Cüzdan daki dolar bilgisi alınıyor
function getWalletDolar($api, $coin_id){
    try{
        $ticker = $api->prices();
        $balances = $api->balances($ticker)["USDT"]; //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )
        $walletDolar = $balances["available"]; //Cüzdanımda kalan DOLAR para birimi
        return $walletDolar;
    }catch (\Exception $e){
        $log = new Log;
        $log->type = 2;
        $log->coin_id = $coin_id;
        $log->title = "Balance API Error";
        $log->description = "Cüzdan Dolar Bilgisi Alınamadı: ".$e->getMessage();
        $log->save();
        return null;
    }

}

//Alınacak coinin miktarın stabiletisini kontrol etme.
function getPaymentCoinAmount($api, $coin_id, $coin_usd, $coin_purchase){

    $price = -1; //şu anda olan coin para birimi
    $priceUpLimit = -1; //şu anda olan coin biriminin sirkülasyon maks üst aralığı
    $priceDownLimit = -1; //şu anda olan coin biriminin sirkülasyon min alt aralığı
    $priceMaxMinStatus = false; // sirkülasyon aralığıbelirlenmiş mi ?
    $priceDiff = -1; //coin para briminin aralık farkının alınması
    $buyPriceCount = 30; //her 1 saniye de 30 kere aynı para birim aralığındaysa limit emriyle satın alma işlemi gerçekleştirilecek.
    $buyPriceCounter = 0;

    while(true){

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
                        return $price; //alınacak para birimi belirlendi döngü sonlandırıldı.
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
            return null;
        }

        sleep(1); // 1 saniye de 1 kere para birimini kontrol et.
    }
}

//Satın alma limitinin eklenmesi.
function buyCoin($api, $coin_id, $coin_usd, $buyPiece, $buyPrice){

    try{
        $buyStatus = $api->buy($coin_usd, $buyPiece, $buyPrice); // {"symbol":"ADAUSDT","orderId":1128188745,"orderListId":-1,"clientOrderId":"2pOvnTiBwWlB0K4WfQMsMy","transactTime":1615919001851,"price":"1.00000000","origQty":"10.00000000","executedQty":"0.00000000","cummulativeQuoteQty":"0.00000000","status":"NEW","timeInForce":"GTC","type":"LIMIT","side":"BUY","fills":[]}
        if($buyStatus["status"] == "NEW" && $buyStatus["side"] == "BUY") { //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ? NEW = Sipariş motor tarafından kabul edildi & BUY satın alma olduğunda emin olmak için ek kontrol.
            $order = new Order;
            $order->coin_id = $coin_id;
            $order->orderId = $buyStatus["orderId"];
            $order->symbol = $coin_usd;
            $order->side = $buyStatus["side"];
            $order->origQty = $buyStatus["origQty"];
            $order->price = $buyStatus["price"];
            $order->type = $buyStatus["type"];
            $order->status = $buyStatus["status"];
            $order->save();

            return $order->id;
        }else{
            $log = new Log;
            $log->type = 2;
            $log->coin_id = $coin_id;
            $log->title = "buyCoin Status Error";
            $log->description = "Satın alma limit farklı status değerine sahip. Data: ". $buyStatus;
            $log->save();
            return null;
        }
    } catch (\Exception $e) {
        $log = new Log;
        $log->type = 2;
        $log->coin_id = $coin_id;
        $log->title = "buyCoin Limit Error";
        $log->description = "Satın Alma Limit Başarısız. Detay: ". $e->getMessage();
        $log->save();
        return null;
    }
}

//LİMİT EMRİ VERİLDİ VE LİMİT EMRİNİN GERÇEKLEŞMESİ BEKLENİYOR.
function getOrderStatus($api, $coin_id, $coin_usd, $order){
    try{
        while(true){
            $orderStatus = $api->orderStatus($coin_usd, $order->orderId);
            if($orderStatus["status"] == "FILLED"){ //işlem gerçekleşmiş. filled = Sipariş tamamlandı /// $orderStatus["status"] == "CANCELED" sipariş iptal edildiyse
                $order->status = $orderStatus["status"];
                $order->save();
                return true; //işlem gerçekleşmiş.
            }else{
                sleep(1); // 1 saniye 1 kere satın alınmış mı kontrolü
            }
        }
    }catch (\Exception $e) {
        $log = new Log;
        $log->type = 2;
        $log->coin_id = $coin_id;
        $log->title = "Limit Status Error";
        $log->description = "Limit Emrinin Kontrolü Başarısız. Detay: ". $e->getMessage();
        $log->save();
        return false;
    }

}

//Satış limitinin eklenmesi.
function sellCoin($api, $coin_id, $coin_usd, $sellPiece, $sellPrice){

    try{
        $sellStatus = $api->sell($coin_usd, $sellPiece, $sellPrice); // {"symbol":"TRXUSDT","orderId":701099196,"orderListId":-1,"clientOrderId":"lk9pIK7dpR9TPBuo1uPkJx","transactTime":1615926174851,"price":"0.05800000","origQty":"500.00000000","executedQty":"0.00000000","cummulativeQuoteQty":"0.00000000","status":"NEW","timeInForce":"GTC","type":"LIMIT","side":"SELL","fills":[]}
        if($sellStatus["status"] == "NEW" && $sellStatus["side"] == "SELL") { //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ? NEW = Sipariş motor tarafından kabul edildi & SELL satış olduğunda emin olmak için ek kontrol.
            $order = new Order;
            $order->coin_id = $coin_id;
            $order->orderId = $sellStatus["orderId"];
            $order->symbol = $coin_usd;
            $order->side = $sellStatus["side"];
            $order->origQty = $sellStatus["origQty"];
            $order->price = $sellStatus["price"];
            $order->type = $sellStatus["type"];
            $order->status = $sellStatus["status"];
            $order->save();

            return $order->id;
        }else{
            $log = new Log;
            $log->type = 2;
            $log->coin_id = $coin_id;
            $log->title = "sellCoin Status Error";
            $log->description = "Satış yapma limiti farklı status değerine sahip. Data: ". $sellStatus;
            $log->save();
            return null;
        }
    } catch (\Exception $e) {
        $log = new Log;
        $log->type = 2;
        $log->coin_id = $coin_id;
        $log->title = "sellCoin Limit Error";
        $log->description = "Satış Yapma Limit Başarısız. Detay: ". $e->getMessage();
        $log->save();
        return null;
    }
}

//Daha önceden bu coine limit emri verilmiş mi kontrolü varsa gerçekleşene kadar bekleyecek.
function openOrdersByPass($api, $coin_id, $coin_usd){
    while(true){
        try{
            $openorders = $api->openOrders($coin_usd); // Array ( [0] => Array ( [symbol] => ADAUSDT [orderId] => 1061155402 [orderListId] => -1 [clientOrderId] => and_7ef0d8f35bd3440ca487d811e99515b1 [price] => 1.23000000 [origQty] => 100.00000000 [executedQty] => 0.00000000 [cummulativeQuoteQty] => 0.00000000 [status] => NEW [timeInForce] => GTC [type] => LIMIT [side] => SELL [stopPrice] => 0.00000000 [icebergQty] => 0.00000000 [time] => 1614853539597 [updateTime] => 1614853539597 [isWorking] => 1 [origQuoteOrderQty] => 0.00000000 ) )
            if(count($openorders) > 0){ //Daha önceden bir limit emri verilmiş gerçekleşmesi için beklenecek.
                // limitin gerçekleşmesi bekleniyor.
                sleep(10);
            }else{
                return true; //limit emri bulunmadı.
            }
        }catch (\Exception $e) {
            $log = new Log;
            $log->type = 2;
            $log->coin_id = $coin_id;
            $log->title = "sellCoin Limit Error";
            $log->description = "Satış Yapma Limit Başarısız. Detay: ". $e->getMessage();
            $log->save();
            sleep(5);
        }

    }

}

