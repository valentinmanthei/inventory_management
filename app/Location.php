<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Location
 *
 * @mixin \Eloquent
 * @property int    $id
 * @property string $name
 * @property string $type
 * @property int    $stock
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 */
class Location extends Model {
   const TYPE_FBA         = 'fba';
   const TYPE_FULFILLMENT = 'fulfillment';
   const TYPE_PRIVATE     = 'private';

   public $timestamps = false;

   /**
    * @return Location[]
    */
   public function products() {
      return $this->belongsToMany('App\Product')->withPivot('stock');
   }
}
