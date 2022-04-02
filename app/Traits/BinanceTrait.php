<?php

namespace App\Traits;

use App\Enums\ConsoleMessageType;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait BinanceTrait
{
    /**
     * Komisyon bilgisinin alınması.
     * @param $spot
     * @return mixed
     */
    function getCommission($spot)
    {
        while (true) {
            try {
                if ($this->testMode) {
                    $fee = $this->getCommissionFake($spot);
                } else {
                    $fee = $this->api->commissionFee($spot);
                }
                /*
                 * Taker = direk piyasadan direk alma veya satma şeklinde uygulanan komisyondur ve daha yüksek komisyon alır.
                 * Maker = limit emirleriyle işlemlerde daha düşük komisyon almaktadır.
                   0 => array:3 [
                    "symbol" => "XRPUSDT"
                    "makerCommission" => "0.001"
                    "takerCommission" => "0.001"
                  ]
                 * */
                if (isset($fee[0]["makerCommission"])) {
                    return $fee[0]["makerCommission"];
                } else {
                    $this->log(ConsoleMessageType::ERROR, $this->coinId, "Komisyon Bilgisi", "Komisyon bilgisi alınamadı" . json_encode($fee));
                    sleep(2);
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Komisyon Bilgisi API Error", "Komisyon bilgisini alma api error: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }
        }
    }

    /**
     * Cüzdan daki para bilgisinin alınması örneğin USDT bilgisi
     * @param $currency
     * @return mixed
     */
    public function getWalletCurrency($currency)
    {
        while (true) {
            try {
                if ($this->testMode) {
                    //$ticker = $this->pricesFake();
                    $balances = $this->balancesFake();
                } else {
                    $ticker = $this->api->prices();
                    //Array ( [available] => 0.07340000 [onOrder] => 100.00000000 [btcValue] => 0.00000170 [btcTotal] => 0.00232070 )
                    $balances = $this->api->balances($ticker)[$currency];
                }
                return $balances["available"]; //Cüzdanımda kalan para birimi
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Balance API Error", "Cüzdan Para Bilgisi Alınamadı: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }
        }
    }


    /**
     * Alınacak coinin miktarın stabiletisini kontrol etme.
     * @param $spot
     * @param $coinPurchase
     * @param $sensitivity
     * @return float
     */
    function getStabilizationPrice($spot, $coinPurchase, $sensitivity): float
    {
        $price = -1; //şu anda olan coin para birimi
        $priceUpLimit = -1; //şu anda olan coin biriminin sirkülasyon maks üst aralığı
        $priceDownLimit = -1; //şu anda olan coin biriminin sirkülasyon min alt aralığı
        $priceMaxMinStatus = false; // sirkülasyon aralığıbelirlenmiş mi ?
        $priceDiff = -1; //coin para briminin aralık farkının alınması
        $buyPriceCount = $sensitivity; //her 1 saniye de belirtlen X kere aynı para birim aralığındaysa limit emriyle satın alma işlemi gerçekleştirilecek.
        $buyPriceCounter = 1;

        while (true) {

            try {
                if ($this->testMode) {
                    if($priceUpLimit != -1 && $priceDownLimit != -1){
                        $price = floatval($this->priceFake($priceDownLimit, $priceUpLimit));
                    }else{
                        $price = floatval($this->priceFake());
                    }
                } else {
                    $price = floatval($this->api->price($spot)); //örnek çıktı: 1.06735000
                }

                if ($priceMaxMinStatus == false) {
                    $coinDigit = pow(10, $this->getCoinPriceDigit($price));
                    $priceDiff = $price * $coinPurchase;
                    $priceDiff = ceil($priceDiff * $coinDigit) / $coinDigit; //kusurat duzeltme örn: 1.2359069 => 1.23591
                    $priceUpLimit = $price + $priceDiff;
                    $priceDownLimit = $price - $priceDiff;
                    $priceMaxMinStatus = true;
                    $this->consoleMessage(ConsoleMessageType::INFO, '----------------------', false);
                    $this->consoleMessage(ConsoleMessageType::INFO, "Ölçülen Fiyat: " . $price, false);
                    $this->consoleMessage(ConsoleMessageType::INFO, "Max Fiyat Aralığı: " . $priceUpLimit, false);
                    $this->consoleMessage(ConsoleMessageType::INFO, "Min Fiyat Aralığı: " . $priceDownLimit, false);
                    $this->consoleMessage(ConsoleMessageType::INFO, "Max-Min Farkı: " . $priceDiff, false);
                    $this->consoleMessage(ConsoleMessageType::INFO, "----------------------", false);
                } else {
                    $this->consoleMessage(ConsoleMessageType::WARNING, $buyPriceCounter . ". Stabilitesi ölçülüyor #   " . $spot . ": " . $price . "   # " . $buyPriceCounter . " == " . $buyPriceCount . " # " . Carbon::now()->format("d.m.Y H:i:s"), false);
                    if ($price > $priceUpLimit) { //max değeri aşılmış
                        $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                        $buyPriceCounter = 1;
                    } else if ($priceDownLimit > $price) { //min değeri aşılmış
                        $priceMaxMinStatus = false; //tekrardan min ve maks değeri belirle
                        $buyPriceCounter = 1;
                    } else { //Belirtilen min maks değerinin içerisinde
                        if ($buyPriceCounter == $buyPriceCount) { //aralık aynı seyirde devam ettiyse girer.
                            $this->consoleMessage(ConsoleMessageType::WARNING, "Stabilitesi Bulundu: " . $price, false);
                            return $price; //alınacak para birimi belirlendi döngü sonlandırıldı.
                        } else {
                            $buyPriceCounter++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Price Error", "Para Birimi Alınamadı. Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
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
     * @return Order
     */
    function buyCoin($spot, $buyPiece, $buyPrice): Order
    {
        while (true) {
            try {
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
                if ($this->testMode) {
                    $buyStatus = $this->buyFake($spot, $buyPiece, $buyPrice);
                } else {
                    $buyStatus = $this->api->buy($spot, $buyPiece, $buyPrice);
                }


                //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ?
                //NEW = Sipariş motor tarafından kabul edildi
                //FILLED = İŞLEM TAMAMEN GERÇEKLEŞTİ!
                //BUY satın alma olduğunda emin olmak için ek kontrol.
                if (($buyStatus["status"] == "NEW" || $buyStatus["status"] == "FILLED") && $buyStatus["side"] == "BUY") {
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
                    return $order;
                } else {
                    $this->log(ConsoleMessageType::ERROR, $this->coinId, "buyCoin Status Error", "Satın alma limit farklı status değerine sahip. Data: " . json_encode($buyStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "buyCoin Limit Error", "Satın Alma Limit Başarısız. Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
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
    function getOrderStatus($spot, $order): bool
    {
        while (true) {
            try {
                if ($this->testMode) {
                    $orderStatus = $this->orderStatusFake($spot, $order->orderId);
                } else {
                    //$orderStatus["status"] = FILLED -> işlem gerçekleşmiş /// $orderStatus["status"] == "CANCELED" sipariş iptal edildiyse
                    $orderStatus = $this->api->orderStatus($spot, $order->orderId);
                }

                if ($this->testMode) {
                    $price = $this->priceFake();
                } else {
                    $price = floatval($this->api->price($spot)); //örnek çıktı: 1.06735000
                }

                if ($orderStatus["status"] == "FILLED") { //SATILMA İŞLEMİ GERÇEKLEŞMİŞ
                    $order->status = $orderStatus["status"];
                    $order->fee = floatval($order->price) * (floatval($order->origQty) * $this->fee); //toplam kesilen komisyon doları
                    $order->total = floatval($order->price) * floatval($order->origQty); // toplam ödenen para
                    $order->save();
                    $this->consoleMessage(ConsoleMessageType::WARNING, $order->side . " # " . $spot . " # durumu # " . $order->status . " # " . floatval($order->price) . " >= " . $price . " # işlem başarıyla gerçekleşti! #");
                    return true; //işlem gerçekleşmiş.
                } else { //DİĞER DURUM KONTROLLERİ
                    $this->consoleMessage(ConsoleMessageType::WARNING, $order->side . " # " . $spot . " # durumu # " . $order->status . " # " . floatval($order->price) . " >= " . $price . " # işlemin gerçekleşmesi bekleniyor. #");

                    //STOP-LIMIT Kontrolü
                    if ($order->side == "SELL") { //SİPARİŞ(ORDER) İŞLEMİ SATIŞ İŞLEMİYSE

                        if ($this->lossToleranceStatus) { // Kayıp toleransı aktif mi ? Yani Satış siparişi var ve bu satış işlemimiz tolerans ettiğimiz satın aldığımız fiyattan aşağı düşmüşse harekete geçer.
                            if (!isset($orderBuy)) {
                                $orderBuy = Order::where("unique_id", $order->unique_id)->where("side", "BUY")->first(); //BU UNIQUE_ID AIT BUY ISLEMININ ORDER BILGISININ ALINMASI.
                            }

                            $orderBuyPrice = floatval($orderBuy->price);
                            //göze alınan kabul edilebilir kayıp toleransı.
                            $tolerancePrice = $orderBuyPrice * $this->lossTolerance; // 1.60 * 0.04 = 0.056
                            $lossPriceLimit = $orderBuyPrice - $tolerancePrice; // 1.60 - 0.056 = 1.544

                            //Limit Tolerance yüksek miktarda düşüş gerçekleştirdiğinde iptali gerçekleştirmemek için kontroldür.
                            //Yüksek kayıp miktarını beklemek için yapıldı.
                            $limitTolerancePrice = $orderBuyPrice * $this->limitLossTolerance;
                            $limitLossPriceLimit = $orderBuyPrice - $limitTolerancePrice;
                            if ($limitLossPriceLimit > $price) {
                                $this->consoleMessage(ConsoleMessageType::WARNING, "Belirlenen yüksek zarar miktarı aşıldı bu yüzden işlem gerçekleşene kadar beklenecek. # " . $limitLossPriceLimit . " > " . $price . " # ");
                            } else { //Göze alınabilir kayıp miktarı kontrolü.
                                if ($lossPriceLimit > $price) { // kayıp limit para birimi güncel para biriminden büyükse önceki limiti iptal edip zarar satış yapar.
                                    $this->consoleMessage(ConsoleMessageType::WARNING, "Belirlenen zarar miktarı aşıldı. # " . $lossPriceLimit . " > " . $price . " # ");
                                    return false; //Order Cancel edilecek
                                }
                            }
                        }

                    }
                    sleep(2); // 2 saniye de 1 kere satın alınmış mı kontrolü
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Limit Status Error", "Limit Emrinin Kontrolü Başarısız. Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }
        }
    }

    /**
     * Satış limitinin eklenmesi.
     * @param $spot
     * @param $sellPiece
     * @param $sellPrice
     * @return Order
     */
    function sellCoin($spot, $sellPiece, $sellPrice): Order
    {
        while (true) {
            try {
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
                if ($this->testMode) {
                    $sellStatus = $this->sellFake($spot, $sellPiece, $sellPrice);
                } else {
                    $sellStatus = $this->api->sell($spot, $sellPiece, $sellPrice);
                }
                //SİPARİŞ BINANCE TARAFINDAN KABUL EDİLDİ Mİ?
                // NEW = Sipariş motor tarafından kabul edildi &
                // Filled = tamamı başarı olmuş mu ?
                // SELL = satış olduğunda emin olmak için ek kontrol.
                if (($sellStatus["status"] == "NEW" || $sellStatus["status"] == "FILLED") && $sellStatus["side"] == "SELL") {
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

                    return $order;
                } else {
                    $this->log(ConsoleMessageType::ERROR, $this->coinId, "sellCoin Status Error", "Satış yapma limiti farklı status değerine sahip. Data: " . json_encode($sellStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "sellCoin Limit Error", "Satış Yapma Limit Başarısız. Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }
        }
    }

    /**
     * Örnek MATICTRY belirtilen spot birimine daha önceden emir verilmişse emir gerçekleşene kadar bekletir.
     * @param $spot
     * @return bool
     */
    function waitOpenOrders($spot): bool
    {
        if($this->testMode){
            return true;
        }

        while (true) {
            try {
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

                $openOrders = $this->api->openOrders($spot); //SPOT adına göre açık emir bilgilerini getirir. Sadece o spota ait olan emirler gelecektir.

                //dd($openOrders);
                if (count($openOrders) > 0) { //Daha önceden bir limit emri verilmiş gerçekleşmesi için beklenecek.
                    // limitin gerçekleşmesi bekleniyor.
                    $this->orderLog(ConsoleMessageType::WARNING, "Önceden koyulmuş limitin gerçekleşmesi bekleniyor.", $this->uniqueId);
                    //Limit durumuna göre iptal veya yeniden limit oluşturulması
                    foreach ($openOrders as $openOrder) {

                        if ($openOrder["side"] == "BUY") { //LIMIT EMRI BUY MI ? Daha önce satın alınmamış olduğundan direk buy işlemi iptal edilecek.
                            $this->orderLog(ConsoleMessageType::WARNING, "Önceki ALIM Limiti iptal ediliyor yeni alım limiti koyulacak. ", $this->uniqueId);

                            $cancelStatus = $this->api->cancel($openOrder["symbol"], $openOrder["orderId"]);

                            if ($cancelStatus["status"] == "CANCELED") { //Limit emri iptal edildi.
                                $this->orderLog(ConsoleMessageType::WARNING, "Önceki satın alım limiti başarıyla iptal edildi!", $this->uniqueId);
                            } else {
                                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Cancel Error", "Cancel Edilirken Bir Hata Oluştu. Data: " . json_encode($cancelStatus));
                            }
                        } else if ($openOrder["side"] == "SELL") { //LIMIT EMRI SELL mi ?
                            $order = Order::where("orderId", $openOrder["orderId"])->first(); //Senaryo sell satışı yapılırken programda elektrikler kesildi ve tekrar programın akışında sell emri açık kaldı binance tarafında bizde bunu veritabanından orderId bilgisini alarak devam ettiriyoruz.
                            if (isset($order)) {
                                if ($order->side == "SELL") {
                                    $this->orderLog(ConsoleMessageType::WARNING, "Önceki Satım limiti kontrol ediliyor.", $this->uniqueId, $order->orderId);
                                    //Önceki Satış limiti gerçekleşmiş mi ?
                                    if ($this->getOrderStatus($order->symbol, $order)) {
                                        $this->orderLog(ConsoleMessageType::INFO, "Önceki Satış Limiti Zaten Gerçekleşmiş!", $order->unique_id, $order->orderId);
                                    } else {//Eğer $lossToleranceStatus aktif ise önceki limit iptal edilir.
                                        $this->orderLog(ConsoleMessageType::INFO, "Önceki Satış Limiti İptal Ediliyor!", $order->unique_id, $order->orderId);
                                        $this->orderCancel($order);
                                        $this->orderLog(ConsoleMessageType::INFO, "Önceki Satış Limiti Başarıyla İptal Edildi!", $order->unique_id, $order->orderId);
                                    }
                                } else {
                                    $this->consoleMessage(ConsoleMessageType::WARNING, "Database OpenOrder Bilinmeyen durum Order => " . $order->side);
                                }
                            }//else order bulunamadı işlem yapılmayacak
                        } else {
                            $this->consoleMessage(ConsoleMessageType::WARNING, "API OpenOrder Bilinmeyen durum Order => " . $openOrder["side"]);
                        }
                    }

                    sleep(2);
                } else {
                    return true; //limit emri bulunamadı.
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Open Orders Bypass", "Daha önceden limit var mı kontrolü başarısız. Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }

        }

    }

    /**
     * Bir limiti iptal eder.
     * @param $order
     * @return bool
     */
    function orderCancel($order): bool
    {
        while (true) {
            try {
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

                if ($cancelStatus["status"] == "CANCELED") { //Limit emri iptal edildi.
                    $order->status = "CANCELED";
                    $order->fee = 0;
                    $order->total = 0;
                    $order->save(); //Limit emrinin iptal edildiğine dahil güncelleme.
                    $this->orderLog(ConsoleMessageType::INFO, "Satış limit başarıyla iptal edildi!", $this->uniqueId, $order->orderId);

                    if ($this->lossToleranceStatus) {
                        return $this->sellLossTolerance($order);
                    }
                    return true;
                } else {
                    $this->log(ConsoleMessageType::ERROR, $this->coinId, "Cancel Error", "Cancel Edilirken Bir Hata Oluştu. Data: " . json_encode($cancelStatus));
                    sleep(2);
                }
            } catch (\Exception $e) {
                $this->log(ConsoleMessageType::ERROR, $this->coinId, "Order Cancel", "Order Cancel Başarısız Detay: " . $e->getMessage() . " Satır: " . $e->getLine());
                sleep(5);
            }
        }
    }

    /**
     * Belirtilen kayıp tolaransı olduğunda zararına satışı gerçekleştirir.
     * @param $order
     * @return bool|void
     * @throws \Exception
     */
    public function sellLossTolerance($order)
    {
        $price = floatval($this->api->price($order->symbol)); //örnek çıktı: 1.06735000 // Zarar satış limiti koyulacak para biriminin güncel değerini alınıyor.

        $coinDigit = pow(10, $this->getCoinPriceDigit($price)); //coinin basamak değeri alınıyor.
        $price = $price - ($price * 0.005); //para biriminin altına satış yaparak anlık satışı gerçekleştirebiliriz. bu yüzden para biriminin biraz düşük rakamını alıp satış yapılacak.
        $sellPrice = ceil($price * $coinDigit) / $coinDigit;

        $this->orderLog(ConsoleMessageType::WARNING, "Zarar Satış Limiti koyuluyor!", $this->uniqueId, $order->orderId);

        $sellOrderId = $this->sellCoin($order->symbol, floatval($order->origQty), $sellPrice); //Satış limit emri koyuluyor.

        $this->orderLog(ConsoleMessageType::WARNING, "Zarar Satış Limiti Başarıyla Koyuldu!", $this->uniqueId, $sellOrderId);

        $sellOrder = Order::where("id", $sellOrderId)->first();
        if ($this->getOrderStatus($sellOrder->symbol, $sellOrder)) { //Zarar satış gerçekleştiriliyor.
            $this->orderLog(ConsoleMessageType::WARNING, "Zarar Satış Limiti Başarıyla Gerçekleşti!", $this->uniqueId, $sellOrder->id);
            return true;
        } else { //Eğer koyulan limitin altına düştüyse tekrar zarar satış tekrarı deneniyor.
            $this->orderLog(ConsoleMessageType::WARNING, "Zarar satış limiti tekrar deneniyor!", $this->uniqueId, $sellOrder->id);
            return $this->orderCancel($sellOrder);
        }
    }

    /**
     * ondalıklı sayısı kaç adet varsa sayar ör: 1.23444 = 5 döner
     * @param $price
     * @return int
     */
    function getCoinPriceDigit($price): int
    {
        $exp = explode(".", strval($price));
        if (count($exp) > 1) {
            return strlen($exp[1]);
        } else {
            return 1;
        }
    }

    /**
     * tüm para birimlerini alma
     * @return array
     * @throws \Exception
     */
    function getAllCoinPrices()
    {
        $ticker = $this->api->prices();
        return $ticker;
        //return print_r($ticker);
    }

    function clearLastZeros($value)
    {
        return Str::replaceLast("0", "", $value);
    }
}
