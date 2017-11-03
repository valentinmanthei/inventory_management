<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Shop
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereName($value)
 */
class Shop extends Model
{
   public $timestamps = false;
}
