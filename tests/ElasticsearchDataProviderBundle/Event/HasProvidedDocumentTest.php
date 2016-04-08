<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\Event;

use GBProd\ElasticsearchDataProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

/**
 * Tests for HasProvidedDocument
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasProvidedDocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasProvidedDocument('id');

        $this->assertEquals('id', $testedInstance->getId());
    }
}
