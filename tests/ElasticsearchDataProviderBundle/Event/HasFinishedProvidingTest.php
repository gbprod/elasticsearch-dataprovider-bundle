<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\Event;

use GBProd\ElasticsearchDataProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

/**
 * Tests for HasFinishedProviding
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasFinishedProvidingTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $entry = $this
            ->getMockBuilder(RegistryEntry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $testedInstance = new HasFinishedProviding($entry);
        
        $this->assertEquals(
            $entry,
            $testedInstance->getEntry()
        );
    }
}
