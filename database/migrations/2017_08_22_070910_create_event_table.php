<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration {
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up() {
      Schema::create('events', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('order_id')->default(0);
         $table->integer('product_id');
         $table->integer('location_id');
         $table->enum('type', ['sale', 'purchase', 'freebie', 'conversion', 'correction_relative', 'correction_absolute']);
         $table->string('comment', 50);
         $table->integer('stock_adjustment');
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down() {
      Schema::drop('events');
   }
}
