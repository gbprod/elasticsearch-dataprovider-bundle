<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\Registry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProviderInterface;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\Handler;
use Elasticsearch\Client;

/**
 * Tests for handler
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlerRunEveryProviders()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $registry = $this->getMock(Registry::class);
        $registry
            ->expects($this->any())
            ->method('getProviders')
            ->with('my_index', 'my_type')
            ->willReturn([
                $this->createProviderExpectingRun($client, 'my_index', 'my_type'),
                $this->createProviderExpectingRun($client, 'my_index', 'my_type'),
                $this->createProviderExpectingRun($client, 'my_index', 'my_type'),
            ])
        ;
        
        $handler = new Handler($registry);
        
        $handler->handle($client, 'my_index', 'my_type');
    }
    
    private function createProviderExpectingRun($client, $index, $type)
    {
        $provider = $this->getMock(DataProviderInterface::class);
        
        $provider
            ->expects($this->once())
            ->method('run')
            ->with($client, $index, $type)
        ;
        
        return $provider;    
    }
}