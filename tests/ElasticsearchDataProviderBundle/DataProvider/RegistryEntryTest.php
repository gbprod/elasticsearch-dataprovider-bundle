<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProviderInterface;

/**
 * Tests for DataProvider registry entry
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class RegistryEntryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetProvidersEmptyIfNoProviders()
    {
        $provider = $this->getMock(DataProviderInterface::class);

        $testedInstance = new RegistryEntry(
            $provider,
            'my_index',
            'my_type'
        );

        $this->assertEquals(
            $provider,
            $testedInstance->getProvider()
        );

        $this->assertEquals(
            'my_index',
            $testedInstance->getIndex()
        );
        $this->assertEquals(
            'my_type',
            $testedInstance->getType()
        );
    }

    public function testMatch()
    {
       $testedInstance = new RegistryEntry(
            $this->getMock(DataProviderInterface::class),
            'my_index',
            'my_type'
        );

        $this->assertTrue(
            $testedInstance->match('my_index', 'my_type')
        );

        $this->assertFalse(
            $testedInstance->match('my_index', 'my_type_2')
        );

        $this->assertTrue(
            $testedInstance->match('my_index', null)
        );

        $this->assertFalse(
            $testedInstance->match('my_index_2', 'my_type')
        );

        $this->assertTrue(
            $testedInstance->match(null, null)
        );
    }
}
