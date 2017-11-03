<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Product
 *
 * @mixin \Eloquent
 * @property int                 $id
 * @property int                 $supplier_id
 * @property int                 $parent_product_id
 * @property string              $sku
 * @property string              $name
 * @property string              $image
 * @property float               $sales_daily
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereParentProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereSalesDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Location[] $locations
 * @property-read \App\Product $parent
 * @property-read \App\Supplier $supplier
 */
class Product extends Model {

   protected $fillable = ['sku'];
   /**
    * @return Supplier
    */
   public function supplier() {
      return $this->belongsTo('App\Supplier');
   }

   /**
    * @return Product
    */
   public function parent() {
      return $this->belongsTo('App\Product', 'product_id', 'parent_product_id');
   }

   /**
    * @return Location[]
    */
   public function locations() {
      return $this->belongsToMany('App\Location', 'locations_products', 'product_id', 'location_id')->withPivot('stock');
   }

}
