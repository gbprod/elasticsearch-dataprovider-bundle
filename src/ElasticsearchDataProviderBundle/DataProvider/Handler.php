<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    /**
     * @param Registry                      $registry
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(
        Registry $registry, 
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->registry   = $registry;
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Handle provide command
     */
    public function handle(Client $client, $index, $type)
    {
        $entries = $this->registry->get($index, $type);
        
        $this->dispatchHandlingStartedEvent($entries);
        
        foreach($entries as $entry) {
            $this->dispatchProvidingStartedEvent($entry);
    
            $entry->getProvider()->run(
                $client, 
                $entry->getIndex(), 
                $entry->getType()
            );
        }
    }
    
    private function dispatchHandlingStartedEvent(array $entries)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                'elasticsearch.has_started_handling',
                new HasStartedHandling($entries)
            );
        }
    }
    
    private function dispatchProvidingStartedEvent(RegistryEntry $entry)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                'elasticsearch.has_started_providing',
                new HasStartedProviding($entry)
            );
        }
    }
}
