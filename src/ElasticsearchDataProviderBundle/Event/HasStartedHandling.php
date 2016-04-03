<?php

namespace GBProd\ElasticsearchDataProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered when handling has been started
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedHandling extends Event
{
    private $entries;
    
    public function __construct($entries)
    {
        $this->entries = $entries;
    }
    
    public function getEntries()
    {
        return $this->entries;
    }

}
