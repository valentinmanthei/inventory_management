<?php

namespace App\Console\Commands;

use App\Location;
use App\Product;
use App\Services\BillbeeService;
use App\Shop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Setup extends Command {
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:setup';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Sets up the application';

   private $billbeeService;

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct(BillbeeService $billbeeService) {
      $this->billbeeService = $billbeeService;

      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {
      $this->truncate();
      $this->createShops();
      $locationIds = $this->createLocations();
      $this->importProducts($locationIds);
   }

   private function truncate() {
      $this->comment('Truncating tables');

      DB::table('shops')->truncate();
      DB::table('locations')->truncate();
      DB::table('products')->truncate();
      DB::table('locations_products')->truncate();
   }

   private function createShops() {
      $this->comment('Creating shops');

      foreach (['Amazon.de', 'Amazon.co.uk', 'Amazon.es', 'Amazon.fr', 'Amazon.it', 'eBay', 'Beardstyle', 'Dawanda'] as $shopName) {
         $shop       = new Shop();
         $shop->name = $shopName;
         $shop->save();
      }
   }

   private function createLocations() {
      $this->comment('Creating locations');

      $locationIds = [];

      $location       = new Location();
      $location->name = 'Lusava GmbH';
      $location->type = Location::TYPE_PRIVATE;
      $location->save();
      $locationIds[] = $location->id;

      $location       = new Location();
      $location->name = 'Amazon FBA';
      $location->type = Location::TYPE_FBA;
      $location->save();
      $locationIds[] = $location->id;

      return $locationIds;
   }

   private function importProducts($locationIds) {
      $this->comment('Importing products');

      $this->billbeeService->importProducts();

      $products = Product::get();
      foreach ($products as $product) {
         $product->locations()->attach($locationIds, ['stock' => 0]);
      }
   }
}
