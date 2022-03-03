<?php

namespace App\Traits;

trait BinanceFakerTrait
{
    /**
     * Satın almada fake data üretir.
     *
     * @param $spot
     * @param $buyPiece
     * @param $buyPrice
     * @return array
     */
    public function buyFaker($spot, $buyPiece, $buyPrice): array
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

    public function openOrdersFaker($spot)
    {
        // birden fazla order olabilir ona gore yazilmasi lazim ornegin 2 tane MATICUSDT ve 1 tane ADAUSDT
    }

    /**
     * @return int
     */
    public function randomOrderId()
    {
        return rand(90000, 99999999);
    }

    public function randomPrice()
    {
        //belirledigimiz digit kadar rasgele price uretilecek
    }

    public function randomPiece()
    {

    }

}
