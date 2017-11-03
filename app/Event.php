<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Event
 *
 * @mixin \Eloquent
 * @property int                 $id
 * @property int                 $order_id
 * @property int                 $product_id
 * @property int                 $location_id
 * @property string              $type
 * @property string              $comment
 * @property int                 $stock_adjustment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereStockAdjustment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereUpdatedAt($value)
 * @property-read \App\Order     $order
 * @property-read \App\Product   $product
 */
class Event extends Model {
   
   const TYPE_SALE                = 'sale';
   const TYPE_PURCHASE            = 'purchase';
   const TYPE_FREEBIE             = 'freebie';
   const TYPE_CONVERSION          = 'conversion';
   const TYPE_CORRECTION_RELATIVE = 'correction_relative';
   const TYPE_CORRECTION_ABSOLUTE = 'correction_absolute';

   /**
    * @return Order
    */
   public function order() {
      return $this->belongsTo('App\Order');
   }

   /**
    * @return Product
    */
   public function product() {
      return $this->belongsTo('App\Product');
   }
}
