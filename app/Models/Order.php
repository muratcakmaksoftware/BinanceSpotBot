<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    public $timestamps = true;

    /*
     * orderId = bize limit emrinin idsinin verir.
     * symbol = hangi para birimiyle aldıysak onun bilgisini verir.
     * side = buy / sell bilgisini verir.
     * origQty = adet bilgisini verir.
     * price = kaç paradan aldığının bilgisini verir.
     * */
}
