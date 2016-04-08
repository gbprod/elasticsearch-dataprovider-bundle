<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DependencyInjection\Compiler;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProvider;
use GBProd\ElasticsearchDataProviderBundle\DependencyInjection\Compiler\DataProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler path to register DataProviders
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class DataProviderCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    private $testedInstance;

    private $container;

    private $registryDefinition;

    public function setUp()
    {
        $this->testedInstance     = new DataProviderCompilerPass();
        $this->container          = new ContainerBuilder();
        $this->registryDefinition = new Definition();
    }

    public function testShouldRegisterTaggedDataProviders()
    {
        $this->container->setDefinition(
            'gbprod.elasticsearch_dataprovider.registry',
            $this->registryDefinition
        );

        $this->container->setDefinition(
            'data_provider.foo.bar',
            $this->newDataProviderDefinition('foo', 'bar')
        );

        $this->container->setDefinition(
            'data_provider.bar.foo',
            $this->newDataProviderDefinition('fizz', 'buzz')
        );

        $this->testedInstance->process($this->container);

        $calls = $this->registryDefinition->getMethodCalls();

        $this->assertEquals('add', $calls[0][0]);
        $this->assertInstanceOf(Definition::class, $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][0]->getArgument(0));
        $this->assertEquals('data_provider.foo.bar', $calls[0][1][0]->getArgument(0)->__toString());
        $this->assertEquals('foo', $calls[0][1][0]->getArgument(1));
        $this->assertEquals('bar', $calls[0][1][0]->getArgument(2));

        $this->assertEquals('add', $calls[1][0]);
        $this->assertInstanceOf(Definition::class, $calls[1][1][0]);
        $this->assertEquals('data_provider.bar.foo', $calls[1][1][0]->getArgument(0)->__toString());
        $this->assertEquals('fizz', $calls[1][1][0]->getArgument(1));
        $this->assertEquals('buzz', $calls[1][1][0]->getArgument(2));
    }

    private function newDataProviderDefinition($index, $type)
    {
        $tag = ['index' => $index, 'type' => $type];

        $definition = new Definition(DataProvider::class);
        $definition->addTag('elasticsearch.dataprovider', $tag);

        return $definition;
    }

    public function testThrowsExceptionIfNotDataProvider()
    {
        $this->container->setDefinition(
            'gbprod.elasticsearch_dataprovider.registry',
            $this->registryDefinition
        );

        $definition = new Definition(\stdClass::class);
        $definition->addTag(
            'elasticsearch.dataprovider',
            ['index' => 'foo', 'type' => 'bar']
        );

        $this->container->setDefinition(
            'data_provider.foo.bar',
            $definition
        );

        $this->setExpectedException(\InvalidArgumentException::class);

        $this->testedInstance->process($this->container);
    }

    public function testThrowsExceptionIfBadTag()
    {
        $this->container->setDefinition(
            'gbprod.elasticsearch_dataprovider.registry',
            $this->registryDefinition
        );

        $definition = new Definition(DataProvider::class);
        $definition->addTag(
            'elasticsearch.dataprovider',
            ['type' => 'my-type']
        );

        $this->container->setDefinition(
            'data_provider.foo.bar',
            $definition
        );

        $this->setExpectedException(\InvalidArgumentException::class);

        $this->testedInstance->process($this->container);
    }

    public function testNeverCallGetDefinitionIfServiceNotSet()
    {
        $container = $this->getMock(ContainerBuilder::class);

        $container
            ->expects($this->any())
            ->method('hasDefinition')
            ->with('gbprod.elasticsearch_dataprovider.registry')
            ->willReturn(false)
        ;

        $container
            ->expects($this->never())
            ->method('getDefinition')
            ->with('gbprod.elasticsearch_dataprovider.registry')
        ;

        $this->testedInstance->process($container);
    }
}
