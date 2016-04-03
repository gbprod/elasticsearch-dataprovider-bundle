<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\Registry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;
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
    private $client;
    private $registry;
    
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $this->registry = new Registry();
    }
    
    public function testHandlerRunEveryProviders()
    {
        $this->registry
            ->add(
                new RegistryEntry(
                    $this->createProviderExpectingRun('my_index', 'my_type'),
                    'my_index', 
                    'my_type'
                )
            )
            ->add(
                new RegistryEntry(
                    $this->createProviderExpectingRun('my_index', 'my_type_2'),
                    'my_index', 
                    'my_type_2'
                )
            )
        ;
        
        $handler = new Handler($this->registry);
        
        $handler->handle($this->client, 'my_index', null);
    }
    
    private function createProviderExpectingRun($index, $type)
    {
        $provider = $this->getMock(DataProviderInterface::class);
        
        $provider
            ->expects($this->once())
            ->method('run')
            ->with($this->client, $index, $type)
        ;
        
        return $provider;    
    }
}