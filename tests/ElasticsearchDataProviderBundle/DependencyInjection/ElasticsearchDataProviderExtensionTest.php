<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use GBProd\ElasticsearchDataProviderBundle\DependencyInjection\ElasticsearchDataProviderExtension;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\Registry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\Handler;

/**
 * Tests for ElasticsearchDataProviderExtension
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticsearchDataProviderExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    private $container;

    protected function setUp()
    {
        $this->extension = new ElasticsearchDataProviderExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
        
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
    }
    
    /**
     * @dataProvider getServices
     */
    public function testServices($serviceId, $classname)
    {
        $this->assertTrue(
            $this->container->has($serviceId)
        );
        
        $service = $this->container->get($serviceId);
        
        $this->assertInstanceOf($classname, $service);
    }
    
    public function getServices()
    {
        return [
            [
                'gbprod.elasticsearch_dataprovider.registry', 
                Registry::class,
            ], 
            [
                'gbprod.elasticsearch_dataprovider.handler', 
                Handler::class,
            ],
        ];
    }
}
