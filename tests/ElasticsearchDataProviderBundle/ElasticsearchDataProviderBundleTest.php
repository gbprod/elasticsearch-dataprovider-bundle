<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle;

use GBProd\ElasticsearchDataProviderBundle\DependencyInjection\Compiler\DataProviderCompilerPass;
use GBProd\ElasticsearchDataProviderBundle\ElasticsearchDataProviderBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticsearchDataProviderBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            ElasticsearchDataProviderBundle::class,
            new ElasticsearchDataProviderBundle()
        );
    }

    public function testBuildAddCompilerPass()
    {
        $container = $this->getMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(DataProviderCompilerPass::class))
        ;

        $bundle = new ElasticsearchDataProviderBundle();
        $bundle->build($container);
    }
}
