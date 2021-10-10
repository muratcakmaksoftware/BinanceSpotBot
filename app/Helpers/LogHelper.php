<?php

namespace App\Helpers;

use App\Models\Log;
use App\Models\OrderLog;

class LogHelper{

    public static function log($type, $coin_id, $title, $description){
        $log = new Log;
        $log->type = $type;
        $log->coin_id = $coin_id;
        $log->title = $title;
        $log->description = $description;
        $log->save();
    }

    public static function orderLog($title, $description, $unique_id = null, $orderId = null){
        $orderLog = new OrderLog;
        $orderLog->unique_id = $unique_id;
        $orderLog->orderId = $orderId;
        $orderLog->title = $title;
        $orderLog->description = $description;
        $orderLog->save();
    }
}
