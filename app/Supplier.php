<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Supplier
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int $delivery_time_days
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Supplier whereDeliveryTimeDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Supplier whereName($value)
 */
class Supplier extends Model
{
   public $timestamps = false;
}
