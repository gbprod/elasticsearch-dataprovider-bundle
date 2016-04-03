<?php

namespace GBProd\ElasticsearchDataProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered when a document has been indexed
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasIndexedDocument extends Event
{
    /**
     * @var string
     */
    private $id; 
    
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
