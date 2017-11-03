<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @mixin \Eloquent
 * @property int                 $id
 * @property int                 $shop_id
 * @property string              $raw_data
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @property int $location_id
 * @property-read \App\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read \App\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereLocationId($value)
 */
class Order extends Model {
   /**
    * @return Shop
    */
   public function shop() {
      return $this->belongsTo('App\Shop');
   }

   /**
    * @return Location
    */
   public function location() {
      return $this->belongsTo('App\Location');
   }

   /**
    * @return Product[]
    */
   public function products() {
      return $this->belongsToMany('App\Product', 'orders_products', 'order_id', 'product_id')->withPivot('quantity');
   }

}
