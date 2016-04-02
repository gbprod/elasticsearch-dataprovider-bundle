<?php

namespace GBProd\ElasticsearchDataProviderBundle;

use GBProd\ElasticsearchDataProviderBundle\DependencyInjection\Compiler\DataProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticsearchDataProviderBundle extends Bundle
{
    /**
     * {inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DataProviderCompilerPass());
    }
}