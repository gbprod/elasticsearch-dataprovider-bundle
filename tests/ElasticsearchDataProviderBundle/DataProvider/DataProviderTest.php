<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for abstract data provider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class DataProviderTest extends \PHPUnit_Framework_TestCase
{
   public function testRunExecutePopulate()
   {
      $provider = $this->getMockForAbstractClass(DataProvider::class);

      $provider
         ->expects($this->once())
         ->method('populate')
      ;

      $provider->run(
         $this->getClient(),
         'index',
         'type',
         $this->getMock(EventDispatcherInterface::class)
      );
   }

   /**
    * @return Client
    */
   private function getClient()
   {
      return $this
         ->getMockBuilder(Client::class)
         ->disableOriginalConstructor()
         ->getMock()
      ;
   }

   public function testIndexWithIndexAndType()
   {
      $provider = $this->getMockForAbstractClass(DataProvider::class);

      $client = $this->getClient();
      $client
         ->expects($this->once())
         ->method('bulk')
         ->with([
            'body' =>
            [
               [
                  'index' => [
                      '_index' => 'my_index',
                      '_type'  => 'my_type',
                      '_id'    => 'my_id',
                  ]
               ],
               [
                  'foo' => 'bar',
               ]
            ]
         ]
      );

      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  $provider->index(
                     'my_id',
                     ['foo' => 'bar']
                  );
               }
            )
         )
      ;

      $provider->run(
         $client,
         'my_index',
         'my_type',
         $this->getMock(EventDispatcherInterface::class)
      );
   }

   public function testIndexRunBulkTwiceIfMoreThanBatchSize()
   {
      $provider = $this->getMockForAbstractClass(DataProvider::class);

      $client = $this->getClient();
      $client
         ->expects($this->exactly(2))
         ->method('bulk')
      ;

      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  for($i = 0; $i < 1500; $i++) {
                     $provider->index(
                        'my_id',
                        ['foo' => 'bar']
                     );
                  }
               }
            )
         )
      ;

      $provider->run(
         $client,
         'my_index',
         'my_type',
         $this->getMock(EventDispatcherInterface::class)
      );
   }
}