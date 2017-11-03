<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('app:test', function (\App\Services\BillbeeService $billbeeService, \App\Services\AmazonMWSService $amazonMWSService) {
   //$billbeeService->getOrders();

   /*if ($amazonMWSService->validateCredentials()) {
      $supply = $amazonMWSService->ListInventorySupply(['ZL-ZDOF-O3XW', 'OM-UBFM-P3BW']);
      dd($supply);
   }*/
   //\Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CheckoutCompleted($user, true));

   //$pdf = $easybillService->getInvoicePdf(109435520);

   /*$billingAddress  = $addressDataProvider->getBillingAddress($user->id);
   $deliveryAddress = $addressDataProvider->getDeliveryAddress($user->id);

   $easybillService->createCustomer($user, $billingAddress, $deliveryAddress);
   $easybillService->addInvoice($user, $order);*/
});

Artisan::command('import:products', function (\App\Services\BillbeeService $billbeeService) {
   \Illuminate\Support\Facades\DB::table('locations')->truncate();
   \Illuminate\Support\Facades\DB::table('products')->truncate();
   \Illuminate\Support\Facades\DB::table('locations_products')->truncate();

   $locationIds = [];

   $location       = new \App\Location();
   $location->name = 'Lusava GmbH';
   $location->type = \App\Location::TYPE_PRIVATE;
   $location->save();
   $locationIds[] = $location->id;

   $location       = new \App\Location();
   $location->name = 'Amazon FBA';
   $location->type = \App\Location::TYPE_FBA;
   $location->save();
   $locationIds[] = $location->id;

   $billbeeService->importProducts();

   $products = \App\Product::get();
   foreach ($products as $product) {
      $product->locations()->attach($locationIds, ['stock' => 0]);
   }
});