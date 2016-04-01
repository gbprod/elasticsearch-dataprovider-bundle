<?php

namespace GBProd\ElasticsearchDataProviderBundle\DependencyInjection\Compiler;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler path to register DataProviders
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class DataProviderCompilerPass extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('gbprod.elasticsearch_dataprovider.registry')) {
            return;
        }
        
        $registry = $container->getDefinition(
            'gbprod.elasticsearch_dataprovider.registry'
        );
        
        $providers = $container->findTaggedServiceIds(
            'elasticsearch_dataprovider.provider'
        );
        
        foreach ($providers as $providerId => $tags) {
            $this->processProvider($container, $registry, $providerId, $tags);
        }
    }
    
    private function processProvider(ContainerBuilder $container, $registry, $providerId, $tags)
    {
        $this->validateIsAProvider(
            $container->getDefinition($providerId)
        );

        foreach ($tags as $tag) {
            $this->validateTag($tag);
            $this->registerProvider($registry, $providerId, $tag);
        }
    }
    
    private function validateIsAProvider(Definition $definition)
    {
        if ($this->isNotAProvider($definition)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'DataProvider "%s" must extends DataProvider.', 
                    $providerId
                )
            );
        }
    }
        
    private function isNotAProvider(Definition $definition)
    {
        $reflection = new \ReflectionClass($definition->getClass());

        return false === $reflection->isSubclassOf(DataProvider::class)
            && DataProvider::class !== $definition->getClass()
        ;
    }
    
    private function validateTag($tag)
    {
        if ($this->isTagIncorrect($tag)) {
            throw new \InvalidArgumentException(
                sprintf('DataProvider "%s" must specify the "index"'.
                    ' and "type" attribute.', 
                    $providerId
                )
            );
        }
    }

    private function isTagIncorrect($tag)
    {
        return !isset($tag['type']) 
            || !isset($tag['index'])
            || empty($tag['index'])
            || empty($tag['type'])
        ;
    }
    
    private function registerProvider($registry, $providerId, $tag)
    {
       $registry->addMethodCall('addProvider', [
            new Reference($providerId),
            $tag['index'], 
            $tag['type']
        ]);
    }
}