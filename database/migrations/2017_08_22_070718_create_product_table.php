<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration {
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up() {
      Schema::create('products', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('supplier_id')->default(0);
         $table->integer('parent_product_id')->default(0);
         $table->string('sku', 20)->nullable();
         $table->string('name', 200)->default('');
         $table->string('image', 150)->default('');
         $table->float('sales_daily')->default(0.0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down() {
      Schema::drop('products');
   }
}
