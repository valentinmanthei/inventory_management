<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration {
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up() {
      Schema::create('orders', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('shop_id');
         $table->integer('location_id')->default(0);
         //$table->shop
         $table->text('raw_data');
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down() {
      Schema::drop('orders');
   }
}
