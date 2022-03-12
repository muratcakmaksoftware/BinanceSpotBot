<?php

namespace App\Traits;

use App\Enums\ConsoleMessageType;
use App\Models\Log;
use App\Models\OrderLog;
use Carbon\Carbon;

trait LogTrait
{
    public $mintContext = null;

    /**
     * API veya işlem hata log.
     *
     * @param int $type
     * @param int $coin_id
     * @param string $title
     * @param string $description
     * @return void
     */
    public function log(int $type, int $coin_id, string $title, string $description){
        switch ($type){
            case ConsoleMessageType::INFO:
                $this->consoleMessage(ConsoleMessageType::INFO, $description);
                break;
            case ConsoleMessageType::WARNING:
                $this->consoleMessage(ConsoleMessageType::WARNING, $description);
                break;
            case ConsoleMessageType::ERROR:
                $this->consoleMessage(ConsoleMessageType::ERROR, $description);
                break;
        }

        $log = new Log;
        $log->type = $type;
        $log->coin_id = $coin_id;
        $log->title = $title;
        $log->description = $description;
        $log->save();
    }

    /**
     * Spot işlem log
     *
     * @param int $type
     * @param string $description
     * @param string|null $unique_id
     * @param int|null $orderId
     * @return void
     */
    public function orderLog(int $type, string $description, string $unique_id = null, int $orderId = null, $time = true){
        switch ($type){
            case ConsoleMessageType::INFO:
                $this->info($description. ($time == true ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
            case ConsoleMessageType::WARNING:
                $this->warn($description. ($time == true ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
            case ConsoleMessageType::ERROR:
                $this->error($description. ($time == true ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
        }

        $orderLog = new OrderLog;
        $orderLog->unique_id = $unique_id;
        $orderLog->orderId = $orderId;
        $orderLog->description = $description;
        $orderLog->save();
    }
}
