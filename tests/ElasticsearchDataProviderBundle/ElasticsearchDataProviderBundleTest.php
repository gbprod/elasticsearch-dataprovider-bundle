<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle;

use GBProd\ElasticsearchDataProviderBundle\ElasticsearchDataProviderBundle;

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
}
