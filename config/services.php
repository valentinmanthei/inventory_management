<?php

return [

   /*
   |--------------------------------------------------------------------------
   | Third Party Services
   |--------------------------------------------------------------------------
   |
   | This file is for storing the credentials for third party services such
   | as Stripe, Mailgun, SparkPost and others. This file provides a sane
   | default location for this type of information, allowing packages
   | to have a conventional place to find your various credentials.
   |
   */

   'mailgun' => [
      'domain' => env('MAILGUN_DOMAIN'),
      'secret' => env('MAILGUN_SECRET'),
   ],

   'ses' => [
      'key'    => env('SES_KEY'),
      'secret' => env('SES_SECRET'),
      'region' => 'us-east-1',
   ],

   'sparkpost' => [
      'secret' => env('SPARKPOST_SECRET'),
   ],

   'stripe' => [
      'model'  => App\User::class,
      'key'    => env('STRIPE_KEY'),
      'secret' => env('STRIPE_SECRET'),
   ],

   'billbee' => [
      'url'      => env('BILLBEE_URL'),
      'user'     => env('BILLBEE_USER'),
      'password' => env('BILLBEE_PASSWORD'),
      'api_key'  => env('BILLBEE_API_KEY'),
   ],

   'amazonmws' => [
      'marketplace_id'    => env('AMAZONMWS_MARKETPLACE_ID'),
      'seller_id'         => env('AMAZONMWS_SELLER_ID'),
      'access_key_id'     => env('AMAZONMWS_ACCESS_KEY_ID'),
      'secret_access_key' => env('AMAZONMWS_SECRET_ACCESS_KEY'),
   ],

];
