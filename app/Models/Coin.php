<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $table = "coins";
    public $timestamps = false;

    public function getNameUsdAttribute()
    {
        return $this->name."USDT";
    }

}
