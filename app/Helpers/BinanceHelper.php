<?php

namespace App\Helpers;

use App\Models\Log;
use App\Models\Order;
use App\Models\OrderLog;
use \Binance;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BinanceHelper{

    protected $api = null;
    protected $coinId = null;
    protected $context = null;
    public $uniqueId = -1;
    protected $lossTolerance = 0.022; //%22 Kayıp toleransı
    protected $limitLossTolerance = 0.04; //%4 Yüksek zarar miktarını engellemek için tolerans.
    public $fee = 0.001;
    function __construct($context, $coin_id, $test = false){
        $this->coinId = $coin_id;
        $this->context = $context;
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
                    LogHelper::log(1, $this->coinId, "Komisyon Bilgisi", "Komisyon bilgisi alınamadı". json_encode($fee));
                    $this->context->error("Komisyon bilgisi alınamadı");
                    sleep(2);
                }
            }catch (\Exception $e){
                LogHelper::log(2, $this->coinId, "Komisyon Bilgisi API Error", "Komisyon bilgisini alma api error: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Komisyon bilgisini alma api error: ".$e->getMessage(). " Satır: ". $e->getLine());
                sleep(5);
            }
        }
    }

    //Cüzdan daki para bilgisinin alınması
    public function getWalletCurrency($currency){
        while(true){
            try{
                $ticker = $this->api->prices();
                //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )
                $balances = $this->api->balances($ticker)[$currency];
                return $balances["available"]; //Cüzdanımda kalan para birimi
            }catch (\Exception $e){
                LogHelper::log(2, $this->coinId, "Balance API Error", "Cüzdan Para Bilgisi Alınamadı: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Cüzdan Para Bilgisi Alınamadı: ".$e->getMessage(). " Satır: ". $e->getLine());
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
    function getStabilizationPrice($spot, $coinPurchase, $sensitivity, $test = false){

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
                if($test){ //hızlı test için stabilite ölcülmeden para birimi alınır.
                    return $price;
                }

                if($priceMaxMinStatus == false){
                    $priceDiff = $price * $coinPurchase;
                    $priceUpLimit = $price + $priceDiff;
                    $priceDownLimit = $price - $priceDiff;
                    $priceMaxMinStatus = true;
                    $this->context->info("----------------------");
                    $this->context->info("Ölçülen Fiyat: ". $price);
                    $this->context->info("Max Fiyat Aralığı: ". $priceUpLimit);
                    $this->context->info("Min Fiyat Aralığı: ". $priceDownLimit);
                    $this->context->info("Max-Min Farkı: ". $priceDiff);
                    $this->context->info("----------------------");
                }else{
                    $this->context->warn($buyPriceCounter.". Stabilitesi ölçülüyor #   ".$spot.": ".$price. "   # ".$buyPriceCounter." == ".$buyPriceCount." # ". Carbon::now()->format("d.m.Y H:i:s"));
                    if($price > $priceUpLimit){ //max değeri aşılmış
                        $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                        $buyPriceCounter = 0;
                    }else if($priceDownLimit > $price){ //min değeri aşılmış
                        $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                        $buyPriceCounter = 0;
                    }else{ //Belirtilen min maks değerinin içerisinde
                        if($buyPriceCounter == $buyPriceCount){ //aralık aynı seyirde devam ettiyse girer.
                            $this->context->warn("Stabilitesi Bulundu: ". $price);
                            return $price; //alınacak para birimi belirlendi döngü sonlandırıldı.
                        }else{
                            $buyPriceCounter++;
                        }
                    }
                }
            } catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "Price Error", "Para Birimi Alınamadı. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Para Birimi Alınamadı. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
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
                   "symbol":"MATICUSDT",
                   "orderId":1153061591,
                   "orderListId":-1,
                   "clientOrderId":"VMx9Mfac4ABbGCfDHuUXmz",
                   "transactTime":1634406291074,
                   "price":"1.50900000",
                   "origQty":"13.00000000",
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
                    $order->unique_id = $this->uniqueId;
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
                    LogHelper::log(1, $this->coinId, "buyCoin Status Error", "Satın alma limit farklı status değerine sahip. Data: ". json_encode($buyStatus));
                    $this->context->error("Satın alma limit farklı status değerine sahip. Data: ". json_encode($buyStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "buyCoin Limit Error", "Satın Alma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Satın Alma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
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
        $orderBuy = Order::where("unique_id", $order->unique_id)->where("side", "BUY")->first();
        while(true) {
            try{
                $orderStatus = $this->api->orderStatus($spot, $order->orderId);
                //işlem gerçekleşmiş. filled = Sipariş tamamlandı /// $orderStatus["status"] == "CANCELED" sipariş iptal edildiyse
                $price = floatval($this->api->price($spot)); //örnek çıktı: 1.06735000

                if($orderStatus["status"] == "FILLED"){
                    $order->status = $orderStatus["status"];
                    $order->fee = floatval($order->price) * (floatval($order->origQty) * $this->fee); //toplam kesilen komisyon doları
                    $order->total = floatval($order->price) * floatval($order->origQty); // toplam ödenen para
                    $order->save();
                    $this->context->warn($order->side." # ".$spot." # durumu # ".$order->status." # ".floatval($order->price)." >= ".$price." # işlem başarıyla gerçekleşti! # ". Carbon::now()->format("d.m.Y H:i:s"));
                    return true; //işlem gerçekleşmiş.
                }else{
                    $this->context->warn($order->side." # ".$spot." # durumu # ".$order->status." # ".floatval($order->price)." >= ".$price." # işlemin gerçekleşmesi bekleniyor. # ". Carbon::now()->format("d.m.Y H:i:s"));

                    //STOP-LIMIT Kontrolü
                    if($order->side == "SELL"){
                        if(isset($orderBuy)){
                            $orderBuyPrice = floatval($orderBuy->price);
                            //göze alınan kabul edilebilir kayıp toleransı.
                            $tolerancePrice = $orderBuyPrice * $this->lossTolerance; // 1.60 * 0.04 = 0.056
                            $lossPriceLimit = $orderBuyPrice - $tolerancePrice; // 1.60 - 0.056 = 1.544

                            //Limit Tolerance yüksek miktarda düşüş gerçekleştirdiğinde iptali gerçekleştirmemek için kontroldür.
                            //Yüksek kayıp miktarını beklemek için yapıldı.
                            $limitTolerancePrice = $orderBuyPrice * $this->limitLossTolerance;
                            $limitLossPriceLimit = $orderBuyPrice - $limitTolerancePrice;
                            if($limitLossPriceLimit > $price){
                                $this->context->warn("Belirlenen yüksek zarar miktarı aşıldı bu yüzden işlem gerçekleşene kadar beklenecek. # ".$limitLossPriceLimit." > ".$price." # ". Carbon::now()->format("d.m.Y H:i:s"));
                            }else{ //Göze alınabilir kayıp miktarı kontrolü.
                                if($lossPriceLimit > $price){ // kayıp limit para birimi güncel para biriminden büyükse önceki limiti iptal edip zarar satış yapar.
                                    $this->context->warn("Belirlenen zarar miktarı aşıldı. # ".$lossPriceLimit." > ".$price." # ". Carbon::now()->format("d.m.Y H:i:s"));
                                    return false; //Order Cancel edilecek
                                }
                            }
                        }
                    }
                    sleep(2); // 2 saniye de 1 kere satın alınmış mı kontrolü
                }
            }catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "Limit Status Error", "Limit Emrinin Kontrolü Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Limit Emrinin Kontrolü Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
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
                   "symbol":"MATICUSDT",
                   "orderId":1153061673,
                   "orderListId":-1,
                   "clientOrderId":"NNLDOPoudQOWe2ThA4V76a",
                   "transactTime":1634406294188,
                   "price":"1.52100000",
                   "origQty":"12.90000000",
                   "executedQty":"0.00000000",
                   "cummulativeQuoteQty":"0.00000000",
                   "status":"NEW",
                   "timeInForce":"GTC",
                   "type":"LIMIT",
                   "side":"SELL",
                   "fills":[]
                 }*/
                $sellStatus = $this->api->sell($spot, $sellPiece, $sellPrice);
                //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ?
                // NEW = Sipariş motor tarafından kabul edildi &
                // Filled = tamamı başarı olmuş mu ?
                // SELL = satış olduğunda emin olmak için ek kontrol.
                if(($sellStatus["status"] == "NEW" || $sellStatus["status"] == "FILLED") && $sellStatus["side"] == "SELL") {
                    $order = new Order;
                    $order->unique_id = $this->uniqueId;
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
                    LogHelper::log(1, $this->coinId, "sellCoin Status Error", "Satış yapma limiti farklı status değerine sahip. Data: ". json_encode($sellStatus));
                    $this->context->error("Satış yapma limiti farklı status değerine sahip. Data: ". json_encode($sellStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "sellCoin Limit Error", "Satış Yapma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Satış Yapma Limit Başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
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

                $openOrders = $this->api->openOrders($spot);
                //dd($openOrders);
                if(count($openOrders) > 0){ //Daha önceden bir limit emri verilmiş gerçekleşmesi için beklenecek.
                    // limitin gerçekleşmesi bekleniyor.
                    $this->context->warn("Önceden koyulmuş limitin gerçekleşmesi bekleniyor. ". Carbon::now()->format("d.m.Y H:i:s"));
                    //Limit durumuna göre iptal veya yeniden limit oluşturulması
                    foreach($openOrders as $openOrder){
                        if($spot == $openOrder["symbol"]){ //SPOT bilgisine göre işlem gerçekleştirilecek farklı coinlerde spot olabilir.
                            if($openOrder["side"] == "BUY"){ //Daha önce satın alınmamış olduğundan direk buy işlemi iptal edilecek.
                                $this->context->warn("Önceki ALIM Limiti iptal ediliyor yeni alım limiti koyulacak. ". Carbon::now()->format("d.m.Y H:i:s"));
                                LogHelper::orderLog("Önceki ALIM Limitinin İptali","Önceki ALIM Limiti iptal ediliyor yeni alım limiti koyulacak.");
                                $cancelStatus = $this->api->cancel($openOrder["symbol"], $openOrder["orderId"]);
                                if($cancelStatus["status"] == "CANCELED") { //Limit emri iptal edildi.
                                    $this->context->warn("Önceki satın alım limiti başarıyla iptal edildi! " . Carbon::now()->format("d.m.Y H:i:s"));
                                    LogHelper::orderLog("Önceki Satın Limitinin İptali", "Önceki satın alım limit başarıyla iptal edildi!");
                                }else{
                                    LogHelper::log(1, $this->coinId, "Cancel Error", "Cancel Edilirken Bir Hata Oluştu. Data: ". json_encode($cancelStatus));
                                    $this->context->error("Cancel Edilirken Bir Hata Oluştu. Data: ". json_encode($cancelStatus));
                                }
                            }else if($openOrder["side"] == "SELL"){ //LIMIT EMRI SELL mi ?
                                $order = Order::where("orderId", $openOrder["orderId"])->first();
                                if(isset($order)){
                                    if($order->side == "SELL"){
                                        $this->context->warn("Önceki Satım limiti kontrol ediliyor.". Carbon::now()->format("d.m.Y H:i:s"));
                                        LogHelper::orderLog("Önceki Satım Limiti Kontrolü","Önceki Satım limiti kontrol ediliyor.", $order->unique_id, $order->orderId);
                                        //Önceki Satış limiti gerçekleşmiş mi ?
                                        if($this->getOrderStatus($order->symbol, $order)){
                                            $this->context->info("Önceki Satış Limiti Zaten Gerçekleşmiş!");
                                            LogHelper::orderLog("Önceki Satım Limiti Kontrolü","Önceki Satış Limiti Zaten Gerçekleşmiş!", $order->unique_id, $order->orderId);
                                        }else{
                                            $this->context->info("Önceki Satış Limiti İptal Ediliyor!");
                                            LogHelper::orderLog("Önceki Satış Limit İptali","Önceki Satış Limiti İptal Ediliyor!", $order->unique_id, $order->orderId);
                                            $this->orderCancel($order);
                                            $this->context->info("Önceki Satış Limiti Başarıyla İptal Edildi!");
                                            LogHelper::orderLog("Önceki Satış Limit İptali","Önceki Satış Limiti Başarıyla İptal Edildi!", $order->unique_id, $order->orderId);
                                        }
                                    }else{
                                        $this->context->warn("Database OpenOrder Bilinmeyen durum Order => ".$order->side." ". Carbon::now()->format("d.m.Y H:i:s"));
                                    }
                                }//else order bulunamadı işlem yapılmayacak
                            }else{
                                $this->context->warn("API OpenOrder Bilinmeyen durum Order => ".$openOrder["side"]." ". Carbon::now()->format("d.m.Y H:i:s"));
                            }
                        } //sembole göre kontrol
                    }

                    sleep(2);
                }else{
                    return true; //limit emri bulunmadı.
                }
            }catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "Open Orders Bypass", "Daha önceden limit var mı kontrolü başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Daha önceden limit var mı kontrolü başarısız. Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                sleep(5);
            }

        }

    }

    function orderCancel($order){

        while(true) {
            try {
                $price = floatval($this->api->price($order->symbol)); //örnek çıktı: 1.06735000
                $coinDigit = pow(10, $this->getCoinPriceDigit($price)); //ilk önce coinin kaç basamaklı olduğunu bulmak gerekir.
                $price = $price - ($price * 0.005); //para biriminin altına satış yaparak anlık satışı gerçekleştirebiliriz. bu yüzden para biriminin biraz düşük rakamını alıp satıl yapılacak.
                $sellPrice = ceil($price * $coinDigit) / $coinDigit;

                /*
                  {
                    "symbol": "LTCBTC",
                    "origClientOrderId": "myOrder1",
                    "orderId": 4,
                    "orderListId": -1, //Unless part of an OCO, the value will always be -1.
                    "clientOrderId": "cancelMyOrder1",
                    "price": "2.00000000",
                    "origQty": "1.00000000",
                    "executedQty": "0.00000000",
                    "cummulativeQuoteQty": "0.00000000",
                    "status": "CANCELED",
                    "timeInForce": "GTC",
                    "type": "LIMIT",
                    "side": "BUY"
                    }
                 * */
                $cancelStatus = $this->api->cancel($order->symbol, $order->orderId);
                if($cancelStatus["status"] == "CANCELED") { //Limit emri iptal edildi.
                    $order->status = "CANCELED";
                    $order->fee = 0;
                    $order->total = 0;
                    $order->save(); //Limit emrinin iptal edildiğine dahil güncelleme.
                    $this->context->info("Satış limit başarıyla iptal edildi! ". Carbon::now()->format("d.m.Y H:i:s"));
                    LogHelper::orderLog("Satış Limit İptali","Satış limit başarıyla iptal edildi! ", $this->uniqueId, $order->orderId);

                    $this->context->warn("Zarar Satış Limiti koyuluyor!". Carbon::now()->format("d.m.Y H:i:s"));
                    LogHelper::orderLog("Zarar Satış","Zarar Satış Limiti koyuluyor!");

                    $sellOrderId = $this->sellCoin($order->symbol, floatval($order->origQty), $sellPrice); //Satış limit emri koyuluyor.

                    $this->context->warn("Zarar Satış Limiti Başarıyla Koyuldu!". Carbon::now()->format("d.m.Y H:i:s"));
                    LogHelper::orderLog("Zarar Satış","Zarar Satış Limiti Başarıyla Koyuldu!");

                    $sellOrder = Order::where("id", $sellOrderId)->first();
                    if($this->getOrderStatus($sellOrder->symbol, $sellOrder)){ //Zarar satış gerçekleştiriliyor.
                        $this->context->warn("Zarar Satış Limiti Başarıyla Gerçekleşti!". Carbon::now()->format("d.m.Y H:i:s"));
                        LogHelper::orderLog("Zarar Satış","Zarar Satış Limiti Başarıyla Gerçekleşti!");
                        return true;
                    }else{ //Eğer koyulan limitin altına düştüyse tekrar zarar satış tekrarı deneniyor.
                        $this->context->warn("Zarar satış limiti tekrar deneniyor! ". Carbon::now()->format("d.m.Y H:i:s"));
                        return $this->orderCancel($sellOrder);
                    }
                }else{
                    LogHelper::log(1, $this->coinId, "Cancel Error", "Cancel Edilirken Bir Hata Oluştu. Data: ". json_encode($cancelStatus));
                    $this->context->error("Cancel Edilirken Bir Hata Oluştu. Data: ". json_encode($cancelStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                LogHelper::log(2, $this->coinId, "Order Cancel", "Order Cancel Başarısız Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
                $this->context->error("Order Cancel Başarısız Detay: ". $e->getMessage(). " Satır: ". $e->getLine());
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

    function clearLastZeros($value){
        return Str::replaceLast("0","",$value);
    }
}
