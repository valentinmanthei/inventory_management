<?php

namespace App\Services;

use App\Product;
use App\Shop;
use GuzzleHttp\Client;

class BillbeeService {

   public function importProducts() {
      $response = $this->getGuzzleClient()->request('GET', $this->getUrl('products'));

      $responseJson = json_decode($response->getBody());

      foreach ($responseJson->Data as $responseProduct) {
         /** @var Product $product */
         $product = Product::firstOrCreate(['sku' => $responseProduct->SKU]);
         $product->sku  = $responseProduct->SKU;
         $product->name = $responseProduct->Title[0]->Text;
         if (isset($responseProduct->Images) && !empty($responseProduct->Images)) {
            $product->image = $responseProduct->Images[0]->ThumbUrl;
         }
         $product->save();
      }
   }

   public function getOrders() {
      $customers = [];
      $page      = 1;
      $pages     = 1;

      while ($page <= $pages) {
         $response = $this->getGuzzleClient()->request('GET', $this->getUrl('orders'), [
            'query' => [
               'page'         => $page,
               'pageSize'     => 1,
               //'orderStateId' => 4,
               'minOrderDate' => '2017-08-23T10:00:48',
            ],
         ]);

         /*
          *       +"ShippingIds": array:1 [
        0 => {#623
          +"ShippingId": "26412260"
          +"Shipper": "Amazon Multichannel"
          +"Created": "2017-08-22T21:44:35.157"
        }
      ]
               +"ShippingIds": array:1 [
        0 => {#603
          +"ShippingId": "85222138830309"
          +"Shipper": "Hermes ProfiPaketService"
          +"Created": "2017-08-10T11:33:10.763"
        }
      ]
          */

         $responseJson = json_decode($response->getBody());
         dd($responseJson);
         $pages = $responseJson->Paging->TotalPages;

         echo "Currently on page " . $page . "/" . $pages . "\n";

         $page = $responseJson->page + 1;

         if ($page >= $pages) {
            echo "Done.\n";
            break;
         }

         if ($responseJson && isset($responseJson->Data) && !empty($responseJson->Data)) {
            foreach ($responseJson->Data as $responseProduct) {
               // only import shipped products
               if ($responseProduct->ShippedAt) {

               }
               $shop = Shop::whereName($responseProduct->Seller->BillbeeShopName)->first();
            }
         }

         sleep(5);
      }

      return $customers;
   }

   /**
    * @return Client
    */
   private function getGuzzleClient() {
      return new Client(['timeout' => 3,
                         'auth'    => [config('services.billbee.user'), config('services.billbee.password')],
                         'headers' => [
                            'X-Billbee-Api-Key' => config('services.billbee.api_key'),
                         ]]);
   }

   /**
    * @param string $append
    *
    * @return string
    */
   private function getUrl($append) {
      $url = config('services.billbee.url');

      return $url . $append;
   }

}