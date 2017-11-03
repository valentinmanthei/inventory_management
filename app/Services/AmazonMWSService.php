<?php

namespace App\Services;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class AmazonMWSService {

   const SIGNATURE_METHOD  = 'HmacSHA256';
   const SIGNATURE_VERSION = '2';
   const DATE_FORMAT       = "Y-m-d\TH:i:s.\\0\\0\\0\\Z";
   const APPLICATION_NAME  = 'MCS/MwsClient';

   private $config = [
      'Seller_Id'           => null,
      'Marketplace_Id'      => null,
      'Access_Key_ID'       => null,
      'Secret_Access_Key'   => null,
      'MWSAuthToken'        => null,
      'Application_Version' => '0.0.*',
   ];

   private $MarketplaceIds = [
      'A2EUQ1WTGCTBG2' => 'mws.amazonservices.ca',
      'ATVPDKIKX0DER'  => 'mws.amazonservices.com',
      'A1AM78C64UM0Y8' => 'mws.amazonservices.com.mx',
      'A1PA6795UKMFR9' => 'mws-eu.amazonservices.com',
      'A1RKKUPIHCS9HS' => 'mws-eu.amazonservices.com',
      'A13V1IB3VIYZZH' => 'mws-eu.amazonservices.com',
      'A21TJRUUN4KGV'  => 'mws.amazonservices.in',
      'APJ6JRA9NG5V4'  => 'mws-eu.amazonservices.com',
      'A1F83G8C2ARO7P' => 'mws-eu.amazonservices.com',
      'A1VC38T7YXB528' => 'mws.amazonservices.jp',
      'AAHKV2X7AFYLW'  => 'mws.amazonservices.com.cn',
   ];

   public static $endpoints = [
      'ListRecommendations'           => [
         'method' => 'POST',
         'action' => 'ListRecommendations',
         'path'   => '/Recommendations/2013-04-01',
         'date'   => '2013-04-01',
      ],
      'ListMarketplaceParticipations' => [
         'method' => 'POST',
         'action' => 'ListMarketplaceParticipations',
         'path'   => '/Sellers/2011-07-01',
         'date'   => '2011-07-01',
      ],
      'GetMyPriceForSKU'              => [
         'method' => 'POST',
         'action' => 'GetMyPriceForSKU',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetMyPriceForASIN'             => [
         'method' => 'POST',
         'action' => 'GetMyPriceForASIN',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetProductCategoriesForSKU'    => [
         'method' => 'POST',
         'action' => 'GetProductCategoriesForSKU',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetProductCategoriesForASIN'   => [
         'method' => 'POST',
         'action' => 'GetProductCategoriesForASIN',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetFeedSubmissionResult'       => [
         'method' => 'POST',
         'action' => 'GetFeedSubmissionResult',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'GetReportList'                 => [
         'method' => 'POST',
         'action' => 'GetReportList',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'GetReportRequestList'          => [
         'method' => 'POST',
         'action' => 'GetReportRequestList',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'GetReport'                     => [
         'method' => 'POST',
         'action' => 'GetReport',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'RequestReport'                 => [
         'method' => 'POST',
         'action' => 'RequestReport',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'ListOrders'                    => [
         'method' => 'POST',
         'action' => 'ListOrders',
         'path'   => '/Orders/2013-09-01',
         'date'   => '2013-09-01',
      ],
      'ListOrderItems'                => [
         'method' => 'POST',
         'action' => 'ListOrderItems',
         'path'   => '/Orders/2013-09-01',
         'date'   => '2013-09-01',
      ],
      'GetOrder'                      => [
         'method' => 'POST',
         'action' => 'GetOrder',
         'path'   => '/Orders/2013-09-01',
         'date'   => '2013-09-01',
      ],
      'SubmitFeed'                    => [
         'method' => 'POST',
         'action' => 'SubmitFeed',
         'path'   => '/',
         'date'   => '2009-01-01',
      ],
      'GetMatchingProductForId'       => [
         'method' => 'POST',
         'action' => 'GetMatchingProductForId',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'ListMatchingProducts'          => [
         'method' => 'POST',
         'action' => 'ListMatchingProducts',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetCompetitivePricingForASIN'  => [
         'method' => 'POST',
         'action' => 'GetCompetitivePricingForASIN',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetLowestOfferListingsForASIN' => [
         'method' => 'POST',
         'action' => 'GetLowestOfferListingsForASIN',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'GetLowestPricedOffersForASIN'  => [
         'method' => 'POST',
         'action' => 'GetLowestPricedOffersForASIN',
         'path'   => '/Products/2011-10-01',
         'date'   => '2011-10-01',
      ],
      'ListInventorySupply'           => [
         'method' => 'POST',
         'action' => 'ListInventorySupply',
         'path'   => '/FulfillmentInventory/2010-10-01',
         'date'   => '2010-10-01',
      ],
   ];

   protected $debugNextFeed = false;

   public function __construct(array $config) {
      foreach ($config as $key => $value) {
         if (array_key_exists($key, $this->config)) {
            $this->config[$key] = $value;
         }
      }
      $required_keys = [
         'Marketplace_Id', 'Seller_Id', 'Access_Key_ID', 'Secret_Access_Key',
      ];
      foreach ($required_keys as $key) {
         if (is_null($this->config[$key])) {
            throw new Exception('Required field ' . $key . ' is not set');
         }
      }

      if (!isset($this->MarketplaceIds[$this->config['Marketplace_Id']])) {
         throw new Exception('Invalid Marketplace Id');
      }

      $this->config['Application_Name'] = self::APPLICATION_NAME;
      $this->config['Region_Host']      = $this->MarketplaceIds[$this->config['Marketplace_Id']];
      $this->config['Region_Url']       = 'https://' . $this->config['Region_Host'];

   }

   /**
    * Call this method to get the raw feed instead of sending it
    */
   public function debugNextFeed() {
      $this->debugNextFeed = true;
   }

   /**
    * A method to quickly check if the supplied credentials are valid
    * @return boolean
    */
   public function validateCredentials() {
      try {
         $this->ListOrderItems('validate');
      } catch (Exception $e) {
         if ($e->getMessage() == 'Invalid AmazonOrderId: validate') {
            return true;
         } else {
            return false;
         }
      }
   }

   /**
    * Returns the current competitive price of a product, based on ASIN.
    *
    * @param array [$asin_array = []]
    *
    * @return array
    */
   public function GetCompetitivePricingForASIN($asin_array = []) {
      if (count($asin_array) > 20) {
         throw new Exception('Maximum amount of ASIN\'s for this call is 20');
      }

      $counter = 1;
      $query   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      foreach ($asin_array as $key) {
         $query['ASINList.ASIN.' . $counter] = $key;
         $counter++;
      }

      $response = $this->request(
         'GetCompetitivePricingForASIN',
         $query
      );

      if (isset($response['GetCompetitivePricingForASINResult'])) {
         $response = $response['GetCompetitivePricingForASINResult'];
         if (array_keys($response) !== range(0, count($response) - 1)) {
            $response = [$response];
         }
      } else {
         return [];
      }

      $array = [];
      foreach ($response as $product) {
         if (isset($product['Product']['CompetitivePricing']['CompetitivePrices']['CompetitivePrice']['Price'])) {
            $array[$product['Product']['Identifiers']['MarketplaceASIN']['ASIN']] = $product['Product']['CompetitivePricing']['CompetitivePrices']['CompetitivePrice']['Price'];
         }
      }
      return $array;

   }

   /**
    * Returns lowest priced offers for a single product, based on ASIN.
    *
    * @param string $asin
    * @param        string [$ItemCondition = 'New'] Should be one in: New, Used, Collectible, Refurbished, Club
    *
    * @return array
    */
   public function GetLowestPricedOffersForASIN($asin, $ItemCondition = 'New') {

      $query = [
         'ASIN'          => $asin,
         'MarketplaceId' => $this->config['Marketplace_Id'],
         'ItemCondition' => $ItemCondition,
      ];

      return $this->request(
         'GetLowestPricedOffersForASIN',
         $query
      );

   }

   /**
    * Returns pricing information for your own offer listings, based on SKU.
    *
    * @param array  [$sku_array = []]
    * @param string [$ItemCondition = null]
    *
    * @return array
    */
   public function GetMyPriceForSKU($sku_array = [], $ItemCondition = null) {
      if (count($sku_array) > 20) {
         throw new Exception('Maximum amount of SKU\'s for this call is 20');
      }

      $counter = 1;
      $query   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      if (!is_null($ItemCondition)) {
         $query['ItemCondition'] = $ItemCondition;
      }

      foreach ($sku_array as $key) {
         $query['SellerSKUList.SellerSKU.' . $counter] = $key;
         $counter++;
      }

      $response = $this->request(
         'GetMyPriceForSKU',
         $query
      );

      if (isset($response['GetMyPriceForSKUResult'])) {
         $response = $response['GetMyPriceForSKUResult'];
         if (array_keys($response) !== range(0, count($response) - 1)) {
            $response = [$response];
         }
      } else {
         return [];
      }

      $array = [];
      foreach ($response as $product) {
         if (isset($product['@attributes']['status']) && $product['@attributes']['status'] == 'Success') {
            if (isset($product['Product']['Offers']['Offer'])) {
               $array[$product['@attributes']['SellerSKU']] = $product['Product']['Offers']['Offer'];
            } else {
               $array[$product['@attributes']['SellerSKU']] = [];
            }
         } else {
            $array[$product['@attributes']['SellerSKU']] = false;
         }
      }
      return $array;

   }

   /**
    * Returns pricing information for your own offer listings, based on ASIN.
    *
    * @param array  [$asin_array = []]
    * @param string [$ItemCondition = null]
    *
    * @return array
    */
   public function GetMyPriceForASIN($asin_array = [], $ItemCondition = null) {
      if (count($asin_array) > 20) {
         throw new Exception('Maximum amount of SKU\'s for this call is 20');
      }

      $counter = 1;
      $query   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      if (!is_null($ItemCondition)) {
         $query['ItemCondition'] = $ItemCondition;
      }

      foreach ($asin_array as $key) {
         $query['ASINList.ASIN.' . $counter] = $key;
         $counter++;
      }

      $response = $this->request(
         'GetMyPriceForASIN',
         $query
      );

      if (isset($response['GetMyPriceForASINResult'])) {
         $response = $response['GetMyPriceForASINResult'];
         if (array_keys($response) !== range(0, count($response) - 1)) {
            $response = [$response];
         }
      } else {
         return [];
      }

      $array = [];
      foreach ($response as $product) {
         if (isset($product['@attributes']['status']) && $product['@attributes']['status'] == 'Success' && isset($product['Product']['Offers']['Offer'])) {
            $array[$product['@attributes']['ASIN']] = $product['Product']['Offers']['Offer'];
         } else {
            $array[$product['@attributes']['ASIN']] = false;
         }
      }
      return $array;

   }

   /**
    * Returns pricing information for the lowest-price active offer listings for up to 20 products, based on ASIN.
    *
    * @param array [$asin_array = []] array of ASIN values
    * @param array [$ItemCondition = null] Should be one in: New, Used, Collectible, Refurbished, Club. Default: All
    *
    * @return array
    */
   public function GetLowestOfferListingsForASIN($asin_array = [], $ItemCondition = null) {
      if (count($asin_array) > 20) {
         throw new Exception('Maximum amount of ASIN\'s for this call is 20');
      }

      $counter = 1;
      $query   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      if (!is_null($ItemCondition)) {
         $query['ItemCondition'] = $ItemCondition;
      }

      foreach ($asin_array as $key) {
         $query['ASINList.ASIN.' . $counter] = $key;
         $counter++;
      }

      $response = $this->request(
         'GetLowestOfferListingsForASIN',
         $query
      );

      if (isset($response['GetLowestOfferListingsForASINResult'])) {
         $response = $response['GetLowestOfferListingsForASINResult'];
         if (array_keys($response) !== range(0, count($response) - 1)) {
            $response = [$response];
         }
      } else {
         return [];
      }

      $array = [];
      foreach ($response as $product) {
         if (isset($product['Product']['LowestOfferListings']['LowestOfferListing'])) {
            $array[$product['Product']['Identifiers']['MarketplaceASIN']['ASIN']] = $product['Product']['LowestOfferListings']['LowestOfferListing'];
         } else {
            $array[$product['Product']['Identifiers']['MarketplaceASIN']['ASIN']] = false;
         }
      }
      return $array;

   }

   /**
    * Returns orders created or updated during a time frame that you specify.
    *
    * @param         object           DateTime $from
    * @param boolean $allMarketplaces , list orders from all marketplaces
    * @param array   $states          , an array containing orders states you want to filter on
    * @param string  $FulfillmentChannel
    *
    * @return array
    */
   public function ListOrders(DateTime $from, $allMarketplaces = false, $states = [
      'Unshipped', 'PartiallyShipped',
   ], $FulfillmentChannel = 'MFN') {
      $query = [
         'CreatedAfter'                 => gmdate(self::DATE_FORMAT, $from->getTimestamp()),
         'FulfillmentChannel.Channel.1' => $FulfillmentChannel,
      ];

      $counter = 1;
      foreach ($states as $status) {
         $query['OrderStatus.Status.' . $counter] = $status;
         $counter                                 = $counter + 1;
      }

      if ($allMarketplaces == true) {
         $counter = 1;
         foreach ($this->MarketplaceIds as $key => $value) {
            $query['MarketplaceId.Id.' . $counter] = $key;
            $counter                               = $counter + 1;
         }
      }

      $response = $this->request(
         'ListOrders',
         $query
      );

      if (isset($response['ListOrdersResult']['Orders']['Order'])) {
         $response = $response['ListOrdersResult']['Orders']['Order'];
         if (array_keys($response) !== range(0, count($response) - 1)) {
            return [$response];
         }
         return $response;
      } else {
         return [];
      }
   }

   /**
    * Returns an order based on the AmazonOrderId values that you specify.
    *
    * @param string $AmazonOrderId
    *
    * @return array if the order is found, false if not
    */
   public function GetOrder($AmazonOrderId) {
      $response = $this->request('GetOrder', [
         'AmazonOrderId.Id.1' => $AmazonOrderId,
      ]);

      if (isset($response['GetOrderResult']['Orders']['Order'])) {
         return $response['GetOrderResult']['Orders']['Order'];
      } else {
         return false;
      }
   }

   /**
    * Returns order items based on the AmazonOrderId that you specify.
    *
    * @param string $AmazonOrderId
    *
    * @return array
    */
   public function ListOrderItems($AmazonOrderId) {
      $response = $this->request('ListOrderItems', [
         'AmazonOrderId' => $AmazonOrderId,
      ]);

      $result = array_values($response['ListOrderItemsResult']['OrderItems']);

      if (isset($result[0]['QuantityOrdered'])) {
         return $result;
      } else {
         return $result[0];
      }
   }

   /**
    * Returns the parent product categories that a product belongs to, based on SellerSKU.
    *
    * @param string $SellerSKU
    *
    * @return array if found, false if not found
    */
   public function GetProductCategoriesForSKU($SellerSKU) {
      $result = $this->request('GetProductCategoriesForSKU', [
         'MarketplaceId' => $this->config['Marketplace_Id'],
         'SellerSKU'     => $SellerSKU,
      ]);

      if (isset($result['GetProductCategoriesForSKUResult']['Self'])) {
         return $result['GetProductCategoriesForSKUResult']['Self'];
      } else {
         return false;
      }
   }

   /**
    * Returns the parent product categories that a product belongs to, based on ASIN.
    *
    * @param string $ASIN
    *
    * @return array if found, false if not found
    */
   public function GetProductCategoriesForASIN($ASIN) {
      $result = $this->request('GetProductCategoriesForASIN', [
         'MarketplaceId' => $this->config['Marketplace_Id'],
         'ASIN'          => $ASIN,
      ]);

      if (isset($result['GetProductCategoriesForASINResult']['Self'])) {
         return $result['GetProductCategoriesForASINResult']['Self'];
      } else {
         return false;
      }
   }


   /**
    * Returns a list of products and their attributes, based on a list of ASIN, GCID, SellerSKU, UPC, EAN, ISBN, and
    * JAN values.
    *
    * @param array $asin_array A list of id's
    * @param       string      [$type = 'ASIN']  the identifier name
    *
    * @return array
    */
   public function GetMatchingProductForId(array $asin_array, $type = 'ASIN') {
      $asin_array = array_unique($asin_array);

      if (count($asin_array) > 5) {
         throw new Exception('Maximum number of id\'s = 5');
      }

      $counter = 1;
      $array   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
         'IdType'        => $type,
      ];

      foreach ($asin_array as $asin) {
         $array['IdList.Id.' . $counter] = $asin;
         $counter++;
      }

      $response = $this->request(
         'GetMatchingProductForId',
         $array,
         null,
         true
      );

      $languages = [
         'de-DE', 'en-EN', 'es-ES', 'fr-FR', 'it-IT', 'en-US',
      ];

      $replace = [
         '</ns2:ItemAttributes>' => '</ItemAttributes>',
      ];

      foreach ($languages as $language) {
         $replace['<ns2:ItemAttributes xml:lang="' . $language . '">'] = '<ItemAttributes><Language>' . $language . '</Language>';
      }

      $replace['ns2:'] = '';

      $response = $this->xmlToArray(strtr($response, $replace));

      if (isset($response['GetMatchingProductForIdResult']['@attributes'])) {
         $response['GetMatchingProductForIdResult'] = [
            0 => $response['GetMatchingProductForIdResult'],
         ];
      }

      $found     = [];
      $not_found = [];

      if (isset($response['GetMatchingProductForIdResult']) && is_array($response['GetMatchingProductForIdResult'])) {
         $array = [];
         foreach ($response['GetMatchingProductForIdResult'] as $product) {

            //print_r($product);exit;

            $asin = $product['@attributes']['Id'];
            if ($product['@attributes']['status'] != 'Success') {
               $not_found[] = $asin;
            } else {
               $array = [];

               if (isset($product['Products']['Product']['Identifiers']['MarketplaceASIN']['ASIN']))
                  $array["ASIN"] = $product['Products']['Product']['Identifiers']['MarketplaceASIN']['ASIN'];

               if (!isset($product['Products']['Product']['AttributeSets'])) {
                  $product['Products']['Product'] = $product['Products']['Product'][0];
               }
               foreach ($product['Products']['Product']['AttributeSets']['ItemAttributes'] as $key => $value) {
                  if (is_string($key) && is_string($value)) {
                     $array[$key] = $value;
                  }
               }

               if (isset($product['Products']['Product']['AttributeSets']['ItemAttributes']['Feature'])) {
                  $array['Feature'] = $product['Products']['Product']['AttributeSets']['ItemAttributes']['Feature'];
               }

               if (isset($product['Products']['Product']['AttributeSets']['ItemAttributes']['PackageDimensions'])) {
                  $array['PackageDimensions'] = array_map(
                     'floatval',
                     $product['Products']['Product']['AttributeSets']['ItemAttributes']['PackageDimensions']
                  );
               }

               if (isset($product['Products']['Product']['AttributeSets']['ItemAttributes']['SmallImage'])) {
                  $image                 = $product['Products']['Product']['AttributeSets']['ItemAttributes']['SmallImage']['URL'];
                  $array['medium_image'] = $image;
                  $array['small_image']  = str_replace('._SL75_', '._SL50_', $image);
                  $array['large_image']  = str_replace('._SL75_', '', $image);;
               }
               $found[$asin] = $array;
            }
         }
      }

      return [
         'found'     => $found,
         'not_found' => $not_found,
      ];

   }

   /**
    * Returns a list of products and their attributes, ordered by relevancy, based on a search query that you specify.
    *
    * @param string $query                                                          the open text query
    * @param        string                                                          [$query_context_id = null] the
    *                                                                                                  identifier for
    *                                                                                                  the context
    *                                                                                                  within which the
    *                                                                                                  given search
    *                                                                                                  will be
    *                                                                                                  performed. see:
    *                                                                                                  http://docs.developer.amazonservices.com/en_US/products/Products_QueryContextIDs.html
    *
    * @return array
    */
   public function ListMatchingProducts($query, $query_context_id = null) {

      if (trim($query) == "") {
         throw new Exception('Missing query');
      }

      $array = [
         'MarketplaceId'  => $this->config['Marketplace_Id'],
         'Query'          => urlencode($query),
         'QueryContextId' => $query_context_id,
      ];

      $response = $this->request(
         'ListMatchingProducts',
         $array,
         null,
         true
      );


      $languages = [
         'de-DE', 'en-EN', 'es-ES', 'fr-FR', 'it-IT', 'en-US',
      ];

      $replace = [
         '</ns2:ItemAttributes>' => '</ItemAttributes>',
      ];

      foreach ($languages as $language) {
         $replace['<ns2:ItemAttributes xml:lang="' . $language . '">'] = '<ItemAttributes><Language>' . $language . '</Language>';
      }

      $replace['ns2:'] = '';

      $response = $this->xmlToArray(strtr($response, $replace));

      if (isset($response['ListMatchingProductsResult'])) {
         return $response['ListMatchingProductsResult'];
      } else
         return ['ListMatchingProductsResult' => []];

   }


   /**
    * Returns a list of reports that were created in the previous 90 days.
    *
    * @param array [$ReportTypeList = []]
    *
    * @return array
    */
   public function GetReportList($ReportTypeList = []) {
      $array   = [];
      $counter = 1;
      if (count($ReportTypeList)) {
         foreach ($ReportTypeList as $ReportType) {
            $array['ReportTypeList.Type.' . $counter] = $ReportType;
            $counter++;
         }
      }

      return $this->request('GetReportList', $array);
   }

   /**
    * Returns your active recommendations for a specific category or for all categories for a specific marketplace.
    *
    * @param string                                                                            [$RecommendationCategory =
    *                                                                                                                   null]
    *                                                                                                                   One
    *                                                                                                                   of:
    *                                                                                                                   Inventory,
    *                                                                                                                   Selection,
    *                                                                                                                   Pricing,
    *                                                                                                                   Fulfillment,
    *                                                                                                                   ListingQuality,
    *                                                                                                                   GlobalSelling,
    *                                                                                                                   Advertising
    *
    * @return array/false if no result
    */
   public function ListRecommendations($RecommendationCategory = null) {
      $query = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      if (!is_null($RecommendationCategory)) {
         $query['RecommendationCategory'] = $RecommendationCategory;
      }

      $result = $this->request('ListRecommendations', $query);

      if (isset($result['ListRecommendationsResult'])) {
         return $result['ListRecommendationsResult'];
      } else {
         return false;
      }

   }

   /**
    * Returns a list of marketplaces that the seller submitting the request can sell in, and a list of participations
    * that include seller-specific information in that marketplace
    * @return array
    */
   public function ListMarketplaceParticipations() {
      $result = $this->request('ListMarketplaceParticipations');
      if (isset($result['ListMarketplaceParticipationsResult'])) {
         return $result['ListMarketplaceParticipationsResult'];
      } else {
         return $result;
      }
   }

   public function ListInventorySupply($skus) {
      $counter = 1;
      $query   = [
         'MarketplaceId' => $this->config['Marketplace_Id'],
      ];

      foreach ($skus as $sku) {
         $query['SellerSkus.member.' . $counter] = $sku;
         $counter++;
      }

      $response = $this->request(
         'ListInventorySupply',
         $query
      );
      print_r($response);die();
      $items = [];

      if (isset($response['ListInventorySupplyResult']['InventorySupplyList']['member'][0])) {
         foreach ($response['ListInventorySupplyResult']['InventorySupplyList']['member'] as $item) {
            $items[$item['SellerSKU']] = (int)$item['InStockSupplyQuantity'];
         }
         return $items;
      } elseif (isset($response['ListInventorySupplyResult']['InventorySupplyList']['member'])) {
         $item                      = $response['ListInventorySupplyResult']['InventorySupplyList']['member'];
         $items[$item['SellerSKU']] = (int)$item['InStockSupplyQuantity'];
         return $items;
      } else {
         return [];
      }
   }

   /**
    * Returns the feed processing report and the Content-MD5 header.
    *
    * @param string $FeedSubmissionId
    *
    * @return array
    */
   public
   function GetFeedSubmissionResult($FeedSubmissionId) {
      $result = $this->request('GetFeedSubmissionResult', [
         'FeedSubmissionId' => $FeedSubmissionId,
      ]);

      if (isset($result['Message']['ProcessingReport'])) {
         return $result['Message']['ProcessingReport'];
      } else {
         return $result;
      }
   }

   /**
    * Convert an array to xml
    *
    * @param $array      array to convert
    * @param $customRoot [$customRoot = 'AmazonEnvelope']
    *
    * @return sting
    */
   private
   function arrayToXml(array $array, $customRoot = 'AmazonEnvelope') {
      return ArrayToXml::convert($array, $customRoot);
   }

   /**
    * Convert an xml string to an array
    *
    * @param string $xmlstring
    *
    * @return array
    */
   private
   function xmlToArray($xmlstring) {
      return json_decode(json_encode(simplexml_load_string($xmlstring)), true);
   }

   /**
    * Creates a report request and submits the request to Amazon MWS.
    *
    * @param string $report  (http://docs.developer.amazonservices.com/en_US/reports/Reports_ReportType.html)
    * @param        DateTime [$StartDate = null]
    * @param        EndDate  [$EndDate = null]
    *
    * @return string ReportRequestId
    */
   public
   function RequestReport($report, $StartDate = null, $EndDate = null) {
      $query = [
         'MarketplaceIdList.Id.1' => $this->config['Marketplace_Id'],
         'ReportType'             => $report,
      ];

      if (!is_null($StartDate)) {
         if (!is_a($StartDate, 'DateTime')) {
            throw new Exception('StartDate should be a DateTime object');
         } else {
            $query['StartDate'] = gmdate(self::DATE_FORMAT, $StartDate->getTimestamp());
         }
      }

      if (!is_null($EndDate)) {
         if (!is_a($EndDate, 'DateTime')) {
            throw new Exception('EndDate should be a DateTime object');
         } else {
            $query['EndDate'] = gmdate(self::DATE_FORMAT, $EndDate->getTimestamp());
         }
      }

      $result = $this->request(
         'RequestReport',
         $query
      );

      if (isset($result['RequestReportResult']['ReportRequestInfo']['ReportRequestId'])) {
         return $result['RequestReportResult']['ReportRequestInfo']['ReportRequestId'];
      } else {
         throw new Exception('Error trying to request report');
      }
   }

   /**
    * Get a report's processing status
    *
    * @param string $ReportId
    *
    * @return array if the report is found
    */
   public
   function GetReportRequestStatus($ReportId) {
      $result = $this->request('GetReportRequestList', [
         'ReportRequestIdList.Id.1' => $ReportId,
      ]);

      if (isset($result['GetReportRequestListResult']['ReportRequestInfo'])) {
         return $result['GetReportRequestListResult']['ReportRequestInfo'];
      }

      return false;

   }

   /**
    * Request MWS
    */
   private
   function request($endPoint, array $query = [], $body = null, $raw = false) {

      $endPoint = self::$endpoints[$endPoint];

      $merge = [
         'Timestamp'        => gmdate(self::DATE_FORMAT, time()),
         'AWSAccessKeyId'   => $this->config['Access_Key_ID'],
         'Action'           => $endPoint['action'],
         //'MarketplaceId.Id.1' => $this->config['Marketplace_Id'],
         'SellerId'         => $this->config['Seller_Id'],
         'SignatureMethod'  => self::SIGNATURE_METHOD,
         'SignatureVersion' => self::SIGNATURE_VERSION,
         'Version'          => $endPoint['date'],
      ];

      $query = array_merge($merge, $query);

      if (!isset($query['MarketplaceId.Id.1'])) {
         $query['MarketplaceId.Id.1'] = $this->config['Marketplace_Id'];
      }

      if (!is_null($this->config['MWSAuthToken'])) {
         $query['MWSAuthToken'] = $this->config['MWSAuthToken'];
      }

      if (isset($query['MarketplaceId'])) {
         unset($query['MarketplaceId.Id.1']);
      }

      if (isset($query['MarketplaceIdList.Id.1'])) {
         unset($query['MarketplaceId.Id.1']);
      }

      try {

         $headers = [
            'Accept'              => 'application/xml',
            'x-amazon-user-agent' => $this->config['Application_Name'] . '/' . $this->config['Application_Version'],
         ];

         if ($endPoint['action'] === 'SubmitFeed') {
            $headers['Content-MD5']  = base64_encode(md5($body, true));
            $headers['Content-Type'] = 'text/xml; charset=iso-8859-1';
            $headers['Host']         = $this->config['Region_Host'];

            unset(
               $query['MarketplaceId.Id.1'],
               $query['SellerId']
            );
         }

         $requestOptions = [
            'headers' => $headers,
            'body'    => $body,
         ];

         ksort($query);

         $query['Signature'] = base64_encode(
            hash_hmac(
               'sha256',
               $endPoint['method']
               . "\n"
               . $this->config['Region_Host']
               . "\n"
               . $endPoint['path']
               . "\n"
               . http_build_query($query, null, '&', PHP_QUERY_RFC3986),
               $this->config['Secret_Access_Key'],
               true
            )
         );

         $requestOptions['query'] = $query;

         $client = new Client();

         $response = $client->request(
            $endPoint['method'],
            $this->config['Region_Url'] . $endPoint['path'],
            $requestOptions
         );


         $body = (string)$response->getBody();


         if ($raw) {
            return $body;
         } else if (strpos(strtolower($response->getHeader('Content-Type')[0]), 'xml') !== false) {
            return $this->xmlToArray($body);
         } else {
            return $body;
         }

      } catch (BadResponseException $e) {
         if ($e->hasResponse()) {
            $message = $e->getResponse();
            $message = $message->getBody();
            if (strpos($message, '<ErrorResponse') !== false) {
               $error   = simplexml_load_string($message);
               $message = $error->Error->Message;
            }
         } else {
            $message = 'An error occured';
         }
         throw new Exception($message);
      }
   }

}