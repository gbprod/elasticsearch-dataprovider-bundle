<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;

/**
 * Handle data providing
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class Handler
{
    /**
     * @var Registry
     */
    private $registry;
    
    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * Handle provide command
     */
    public function handle(Client $client, $index, $type)
    {
        $entries = $this->registry->get($index, $type);
        
        foreach($entries as $entry) {
            $entry->getProvider()->run(
                $client, 
                $entry->getIndex(), 
                $entry->getType()
            );
        }
    }
}