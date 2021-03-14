<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "logs";
    public $timestamps = true;

    public function getTypeTextAttribute(){
        switch ($this->type){
            case 0: return "NORMAL";
            case 1: return "ORTA";
            case 2: return "YÜKSEK";
            default: return "BİLİNMİYOR";
        }
    }
}
