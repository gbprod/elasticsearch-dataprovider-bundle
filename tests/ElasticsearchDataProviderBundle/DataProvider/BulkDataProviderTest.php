<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\BulkDataProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for abstract data provider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class BulkDataProviderTest extends \PHPUnit_Framework_TestCase
{
   public function testRunExecutePopulate()
   {
      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);

      $provider
         ->expects($this->once())
         ->method('populate')
      ;

      $provider->run(
         $this->getClient('index'),
         'index',
         'type',
         $this->getMock(EventDispatcherInterface::class)
      );
   }

   /**
    * @return Client
    */
   private function getClient($index)
   {
      $client = $this
         ->getMockBuilder(Client::class)
         ->disableOriginalConstructor()
         ->getMock()
      ;

      $client
         ->expects($this->any())
         ->method('indices')
         ->willReturn(
            $this->newIndicesExpectingRefresh($index)
         )
      ;

      return $client;
   }

   private function newIndicesExpectingRefresh($index)
   {
      $indices = $this
         ->getMockBuilder(IndicesNamespace::class)
         ->disableOriginalConstructor()
         ->getMock()
      ;

      $indices
         ->expects($this->once())
         ->method('refresh')
         ->with([
            'index' => $index,
         ])
      ;

      return $indices;
   }

   public function testIndexWithIndexAndType()
   {
      $client = $this->newClientExpectingBulk(
         [
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

      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);
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

   private function newClientExpectingBulk($content)
   {
      $client = $this->getClient('my_index');

      $client
         ->expects($this->once())
         ->method('bulk')
         ->with($content)
      ;

      return $client;
   }

   public function testIndexRunBulkTwiceIfMoreThanBatchSize()
   {
      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);

      $client = $this->getClient('my_index');
      $client
         ->expects($this->exactly(3))
         ->method('bulk')
      ;

      $provider->changeBulkSize(50);
      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  for($i = 0; $i < 150; $i++) {
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

   public function testCountIsNull()
   {
      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);

      $this->assertNull($provider->count());
   }

   public function testDelete()
   {
      $client = $this->newClientExpectingBulk(
         [
            'body' =>
            [
               [
                  'delete' => [
                      '_index' => 'my_index',
                      '_type'  => 'my_type',
                      '_id'    => 'my_id',
                  ]
               ]
            ]
         ]
      );

      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);
      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  $provider->delete('my_id');
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

   public function testCreate()
   {
      $client = $this->newClientExpectingBulk(
         [
            'body' =>
            [
               [
                  'create' => [
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

      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);
      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  $provider->create('my_id', ['foo' => 'bar']);
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

   public function testUpdate()
   {
      $client = $this->newClientExpectingBulk(
         [
            'body' =>
            [
               [
                  'update' => [
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

      $provider = $this->getMockForAbstractClass(BulkDataProvider::class);
      $provider
         ->expects($this->once())
         ->method('populate')
         ->will(
            $this->returnCallback(
               function () use ($provider) {
                  $provider->update('my_id', ['foo' => 'bar']);
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
