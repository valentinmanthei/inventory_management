<?php

namespace App\Providers;

use App\Services\BillbeeService;
use Illuminate\Support\ServiceProvider;

class BillbeeServiceProvider extends ServiceProvider {

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
      $this->app->singleton('App\Services\BillbeeService', function ($app) {
         return new BillbeeService();
      });
   }

}
