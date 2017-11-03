<?php

namespace App\Providers;

use App\Services\AmazonMWSService;
use Illuminate\Support\ServiceProvider;

class AmazonAWSServiceProvider extends ServiceProvider {

   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot() {
      //
   }

   /**
    * Register the application services.
    *
    * @return void
    */
   public function register() {
      $this->app->singleton('App\Services\AmazonMWSService', function ($app) {
         $config = [];
         $config['Marketplace_Id']    = config('services.amazonmws.marketplace_id');
         $config['Seller_Id']         = config('services.amazonmws.seller_id');
         $config['Access_Key_ID']     = config('services.amazonmws.access_key_id');
         $config['Secret_Access_Key'] = config('services.amazonmws.secret_access_key');
         return new AmazonMWSService($config);
      });
   }

}
