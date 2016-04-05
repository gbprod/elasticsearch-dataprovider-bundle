<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\Event;

use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

/**
 * Tests for HasStartedHandling
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedHandlingTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasStartedHandling(['entries']);
        
        $this->assertEquals(['entries'], $testedInstance->getEntries());
    }
}
