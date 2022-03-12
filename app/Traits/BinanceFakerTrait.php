<?php

namespace App\Traits;

trait BinanceFakerTrait
{
    private $minPrice = 11.10000000; // Fiyat
    private $maxPrice = 12.90000000;

    private $minQuanty = 20; // Miktar
    private $maxQuanty = 100;

    //private $fakeOpenOrder = true; //Bir coine ait bir tane önceden belirtilmiş emir olabilir. Açık olursa önceden emir varmış gibi davranır.

    private $minWalletAvailable = 500; // Cüzdandaki para miktarı
    private $maxWalletAvailable = 800;

    /**
     * Satın almada rastgele veri üretir.
     *
     * @param $spot
     * @param $buyPiece
     * @param $buyPrice
     * @return array
     */
    public function buyFake($spot, $buyPiece, $buyPrice): array
    {
        return [
            "symbol" => $spot, //MATICUSDT
            "orderId" => $this->randomOrderId(), //Rasgele üretilecek
            "orderListId" => -1, //sabit kalabilir
            "clientOrderId" => "VMx9Mfac4ABbGCfDHuUXmz", //sabit kalabilir
            "transactTime" => 1634406291074, //sabit kalabilir
            "price" => $buyPrice, //1.50900000
            "origQty" => $buyPiece, //13.00000000
            "executedQty" => "0.00000000", //sabit kalabilir
            "cummulativeQuoteQty" => "0.00000000", //sabit kalabilir
            "status" => "NEW", //sabit kalabilir
            "timeInForce" => "GTC", //sabit kalabilir
            "type" => "LIMIT", //sabit kalabilir
            "side" => "BUY", //sabit kalabilir
            "fills" => [] //sabit kalabilir
        ];
    }

    /**
     * Bir coine ait bir tane emir üretilmelidir.
     *
     * @param $spot
     * @return array
     */
    /*public function openOrdersFake($spot)
    {
        if ($this->fakeOpenOrder) {
            $orders[] = [
                "symbol" => $spot, // MATICTRY
                "orderId" => '', // Veritabanindaki önceden kaydolmuş bir BUY veya SELL emir bilgisi oluşmuş ama FILLED olmamış olandan id bilgisi buraya koyulması gerekiyor.
                "orderListId" => -1,
                "clientOrderId" => "web_3f5c4f5cabd442c9857ab71ddd244b87",
                "price" => $this->randomPrice(), // 12.50000000
                "origQty" => $this->randomQuanty(), // 559.90000000
                "executedQty" => "0.00000000",
                "cummulativeQuoteQty" => "0.00000000",
                "status" => "NEW",
                "timeInForce" => "GTC",
                "type" => "LIMIT",
                "side" => $this->randomSide(), // SELL
                "stopPrice" => "0.00000000",
                "icebergQty" => "0.00000000",
                "time" => 1633867470193,
                "updateTime" => 1633867470193,
                "isWorking" => true,
                "origQuoteOrderQty" => "0.00000000",
            ];

            return $orders;
        } else {
            return [];
        }
    }*/

    /**
     * Sahte emir durumu üretir.
     *
     * @param $spot
     * @param $orderId
     * @return array
     */
    public function orderStatusFake($spot, $orderId): array
    {
        return [
            "status" => $this->randomStatus(),
            "orderId" => $orderId,
            "symbol" => $spot,
        ];
    }

    /**
     * Sahte emir iptali.
     *
     * @param $symbol
     * @param $orderId
     * @return array
     */
    /*public function cancelFake($symbol, $orderId): array
    {
        return [
            "symbol" => $symbol,
            "origClientOrderId" => "myOrder1",
            "orderId" => $orderId,
            "orderListId" => -1,
            "clientOrderId" => "cancelMyOrder1",
            "price" => "2.00000000",
            "origQty" => "1.00000000",
            "executedQty" => "0.00000000",
            "cummulativeQuoteQty" => "0.00000000",
            "status" => "CANCELED",
            "timeInForce" => "GTC",
            "type" => "LIMIT",
            "side" => $this->randomSide()
        ];
    }*/

    /**
     * Sahte komisyon bilgisi üretir.
     *
     * @param $spot
     * @return array[]
     */
    public function getCommissionFake($spot)
    {
        return [
            [
                "symbol" => $spot, // XRPUSDT
                "makerCommission" => "0.001",
                "takerCommission" => "0.001",
            ]
        ];
    }

    /**
     * Sahte cüzdan bilgisi üretir.
     * !!!Kullanılmadı!!!
     *
     * @return float[]
     */
    public function pricesFake()
    {
        return [
            "available" => 0.07340000,
            "onOrder" => 100.00000000,
            "btcValue" => 0.00000170,
            "btcTotal" => 0.00232070
        ];
    }

    /**
     * Sahte cüzdan bakiyesi üretir.
     *
     * @return array
     */
    public function balancesFake()
    {
        return [
            "available" => $this->randomWalletAvailable()
        ];
    }

    /**
     * Sahte fiyat bilgisi üretir.
     *
     * @param null $min
     * @param null $max
     * @return float|int
     */
    public function priceFake($min = null, $max = null)
    {
        return $this->randomPrice($min ?? $this->minPrice, $max ?? $this->maxPrice);
    }

    /**
     * Sahte satış emri bilgisi üretir.
     *
     * @param $spot
     * @param $sellPiece
     * @param $sellPrice
     * @return array
     */
    public function sellFake($spot, $sellPiece, $sellPrice)
    {
        return [
            "symbol" => $spot, // MATICUSDT
            "orderId" => $this->randomOrderId(), // 1153061673
            "orderListId" => -1,
            "clientOrderId" => "NNLDOPoudQOWe2ThA4V76a",
            "transactTime" => 1634406294188,
            "price" => $sellPrice, // 1.52100000
            "origQty" => $sellPiece, // 12.90000000
            "executedQty" => "0.00000000",
            "cummulativeQuoteQty" => "0.00000000",
            "status" => "NEW",
            "timeInForce" => "GTC",
            "type" => "LIMIT",
            "side" => "SELL",
            "fills" => []
        ];
    }

    /**
     * Rastgele Order ID üretir.
     *
     * @return int
     */
    public function randomOrderId()
    {
        return rand(90000, 99999999);
    }

    /**
     * int rastgele fiyat üretir.
     *
     * @return float|int
     */
    public function randomPrice($st_num = 0, $end_num = 1, $mul = 100000000)
    {
        if ($st_num > $end_num) return false;
        return mt_rand($st_num * $mul, $end_num * $mul) / $mul;
        //return rand($min ?? $this->minPrice, $max ?? $this->maxPrice - 1) + (rand(0, 99999999) / 100000000);
    }

    /**
     * int rastgele miktar üretir.
     *
     * @return float|int
     */
    public function randomQuanty()
    {
        return rand($this->minQuanty, $this->maxQuanty);
    }

    /**
     * Rastgele BUY ya da SELL dönderecek.
     *
     * @return string
     */
    public function randomSide()
    {
        if (rand(0, 1) == 0) {
            return "BUY";
        } else {
            return "SELL";
        }
    }

    /**
     * Rastgele durum üretir. FILLED ya da CANCELED
     *
     * @return string
     */
    public function randomStatus()
    {
        if (rand(0, 1) == 0) {
            return "FILLED";
        } else {
            return "CANCELED";
        }
    }

    /**
     * Rastgele cüzdan bakiyesi üretir.
     *
     * @return int
     */
    public function randomWalletAvailable(): int
    {
        return rand($this->maxWalletPriceLimit, $this->maxWalletAvailable);
    }
}
