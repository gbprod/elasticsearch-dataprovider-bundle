<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\Event;

use GBProd\ElasticsearchDataProviderBundle\Event\HasIndexedDocument;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

/**
 * Tests for HasIndexedDocument
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasIndexedDocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasIndexedDocument('id');
        
        $this->assertEquals('id', $testedInstance->getId());
    }
}
