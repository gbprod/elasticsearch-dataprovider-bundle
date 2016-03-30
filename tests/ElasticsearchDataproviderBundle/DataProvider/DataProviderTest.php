<?php

namespace Tests\GBProd\ElasticsearchDataproviderBundle\DataProvider;

use Elasticsearch\Client;
use GBProd\ElasticsearchDataproviderBundle\DataProvider\DataProvider;

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
      
      $provider->run($this->getClient(), 'index', 'type');
   }
   
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
         ->method('index')
         ->with([
            'index' => 'my_index',
            'type'  => 'my_type',
            'id'    => 'my_id',
            'body'  => ['foo' => 'bar'],
         ])
      ;
      
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
      
      $provider->run($client, 'my_index', 'my_type');
   }
}