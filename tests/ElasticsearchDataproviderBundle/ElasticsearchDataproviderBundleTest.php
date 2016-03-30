<?php

namespace Tests\GBProd\ElasticsearchDataproviderBundle;

use GBProd\ElasticsearchDataproviderBundle\ElasticsearchDataproviderBundle;

/**
 * Tests for Bundle
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticsearchDataproviderBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            ElasticsearchDataproviderBundle::class,
            new ElasticsearchDataproviderBundle()
        );
    }
}
