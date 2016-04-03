<?php

namespace GBProd\ElasticsearchDataProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;

/**
 * Event triggered when providing has been Finished
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasFinishedProviding extends Event
{
    private $entry;
    
    public function __construct(RegistryEntry $entry)
    {
        $this->entry = $entry;
    }
    
    public function getEntry()
    {
        return $this->entry;
    }
}
