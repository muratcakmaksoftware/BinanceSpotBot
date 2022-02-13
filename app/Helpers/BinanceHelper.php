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

        }
    }
}
