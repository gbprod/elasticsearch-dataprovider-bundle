<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\Registry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProviderInterface;

/**
 * Tests for DataProvider registry
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    private $testedInstance;
    
    public function setUp()
    {
        $this->testedInstance = new Registry();    
    }
    
    public function testGetProvidersEmptyIfNoProviders()
    {
        $this->assertEquals(
            [], 
            $this->testedInstance->getProviders()
        );
    }
    
    public function testGetProvidersReturnAllProviders()
    {
        $provider1 = $this->getMock(DataProviderInterface::class);
        $provider2 = $this->getMock(DataProviderInterface::class);
        $provider3 = $this->getMock(DataProviderInterface::class);
        
        $this->testedInstance
            ->addProvider($provider1, 'index', 'type1')
            ->addProvider($provider2, 'index', 'type2')
            ->addProvider($provider3, 'index2', 'type')
        ;
        
        $providers = $this->testedInstance->getProviders();
        
        $this->assertCount(3, $providers);
        
        $this->assertContains($provider1, $providers);
        $this->assertContains($provider2, $providers);
        $this->assertContains($provider3, $providers);
    }
    
    public function testGetProvidersReturnProvidersForAnIndex()
    {
        $provider1 = $this->getMock(DataProviderInterface::class);
        $provider2 = $this->getMock(DataProviderInterface::class);
        $provider3 = $this->getMock(DataProviderInterface::class);
        $provider4 = $this->getMock(DataProviderInterface::class);
        
        $this->testedInstance
            ->addProvider($provider1, 'index2', 'type')
            ->addProvider($provider2, 'index', 'type')
            ->addProvider($provider3, 'index2', 'type')
            ->addProvider($provider4, 'index', 'type2')
        ;
        
        $providers = $this->testedInstance->getProviders('index');
        
        $this->assertCount(2, $providers);
        
        $this->assertNotContains($provider1, $providers);
        $this->assertContains($provider2, $providers);
        $this->assertNotContains($provider3, $providers);
        $this->assertContains($provider4, $providers);
    }
    
    public function testGetProvidersReturnProvidersForAnIndexAndAType()
    {
        $provider1 = $this->getMock(DataProviderInterface::class);
        $provider2 = $this->getMock(DataProviderInterface::class);
        $provider3 = $this->getMock(DataProviderInterface::class);
        $provider4 = $this->getMock(DataProviderInterface::class);
        
        $this->testedInstance
            ->addProvider($provider1, 'index', 'type2')
            ->addProvider($provider2, 'index2', 'type2')
            ->addProvider($provider3, 'index2', 'type')
            ->addProvider($provider4, 'index', 'type')
        ;
        
        $providers = $this->testedInstance->getProviders('index', 'type');
        
        $this->assertCount(1, $providers);
        
        $this->assertNotContains($provider1, $providers);
        $this->assertNotContains($provider2, $providers);
        $this->assertNotContains($provider3, $providers);
        $this->assertContains($provider4, $providers);
    }
}