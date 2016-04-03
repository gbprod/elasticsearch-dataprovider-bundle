<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\Registry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

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
            $this->testedInstance->get()
        );
    }
    
    public function testGetReturnMatchingEntries()
    {
        $entry1 = $this->newRegistryMatching(true);
        $entry2 = $this->newRegistryMatching(false);
        $entry3 = $this->newRegistryMatching(true);
        
        $this->testedInstance
            ->add($entry1)
            ->add($entry2)
            ->add($entry3)
        ;
        
        $entries = $this->testedInstance->get();
        
        $this->assertCount(2, $entries);
        
        $this->assertContains($entry1, $entries);
        $this->assertNotContains($entry2, $entries);
        $this->assertContains($entry3, $entries);
    }
    
    private function newRegistryMatching($matching)
    {
        $entry = $this
            ->getMockBuilder(RegistryEntry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $entry
            ->expects($this->any())
            ->method('match')
            ->willReturn($matching)
        ;
        
        return $entry;
    }
}