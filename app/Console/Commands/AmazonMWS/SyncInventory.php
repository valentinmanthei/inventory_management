<?php

namespace App\Console\Commands\AmazonMWS;

use App\Event;
use App\Location;
use App\Product;
use App\Services\AmazonMWSService;
use Illuminate\Console\Command;

class SyncInventory extends Command {
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'mws:sync-inventory';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Syncs marketplace inventory with local database';

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
   public function handle(AmazonMWSService $amazonMWSService) {
      $this->comment('Getting SKUs from products');

      $products = Product::all();
      $skus     = [];
      foreach ($products as $product) {
         if ($product->sku) {
            $skus[] = $product->sku;
         }
      }

      $this->comment(count($skus) . ' SKUs found.');

      if (!empty($skus)) {
         $amazonMWSService->validateCredentials();
         $supply = $amazonMWSService->ListInventorySupply($skus);

         $this->comment('Received ' . count($supply) . ' products from Amazon');

         if (!empty($supply) && $supply) {
            foreach ($supply as $sku => $stock) {
               /** @var Product $product */
               $product = Product::whereSku($sku)->with('locations')->first();
               if ($product) {
                  $locationId = 0;
                  foreach ($product->locations as $location) {
                     if ($location->type === Location::TYPE_FBA) {
                        $locationId = $location->id;
                     }
                  }
                  if ($locationId > 0) {
                     $this->comment('Setting stock ' . $stock . ' for SKU ' . $sku);

                     $event                   = new Event();
                     $event->product_id       = $product->id;
                     $event->location_id      = $locationId;
                     $event->type             = Event::TYPE_CORRECTION_ABSOLUTE;
                     $event->stock_adjustment = $stock;
                     $event->comment          = 'Amazon FBA Inventory Update';
                     $event->save();

                     //$product->locations()->whereType(Location::TYPE_FBA)->updateExistingPivot($locationId, ['stock' => $stock]);
                  }
               }
            }
         }
      }
   }
}
