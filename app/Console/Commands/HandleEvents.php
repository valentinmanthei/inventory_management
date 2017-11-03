<?php

namespace App\Console\Commands;

use App\Event;
use App\Location;
use App\Product;
use Illuminate\Console\Command;

class HandleEvents extends Command {
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'events:handle';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Handles events';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct() {
      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {
      $events = Event::get();

      if (!empty($events)) {
         $this->comment('Found ' . count($events) . ' events');

         foreach ($events as $event) {
            /** @var Product $product */
            $product = Product::whereId($event->product_id)->with('locations')->first();
            if ($product) {
               $currentStock = $product->locations()->whereId($event->location_id)->first()->pivot->stock;
               $newStock     = 0;

               if ($event->type === Event::TYPE_CORRECTION_ABSOLUTE) {
                  $newStock = $event->stock_adjustment;
               } else {
                  $newStock = $currentStock + $event->stock_adjustment;
               }

               $this->comment('Setting stock ' . $newStock . ' for SKU ' . $product->sku . ', event type ' . $event->type . ' (old stock was ' . $currentStock . ')');
               $product->locations()->updateExistingPivot($event->location_id, ['stock' => $newStock]);
            }
            $event->delete();
         }
      }
   }
}
