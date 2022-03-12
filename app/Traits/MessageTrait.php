<?php

namespace App\Traits;

use App\Enums\ConsoleMessageType;
use Carbon\Carbon;

trait MessageTrait
{
    /**
     * @param int $messageType
     * @param $message
     * @param bool $time
     * @return void
     */
    public function consoleMessage(int $messageType = ConsoleMessageType::INFO, $message, bool $time = true)
    {
        switch ($messageType){
            case ConsoleMessageType::INFO:
                $this->info($message.' '. ($time ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
            case ConsoleMessageType::WARNING:
                $this->warn($message.' '. ($time ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
            case ConsoleMessageType::ERROR:
                $this->error($message.' '. ($time ? ' ### '.Carbon::now()->format("d.m.Y H:i:s") : ''));
                break;
        }
    }
}
