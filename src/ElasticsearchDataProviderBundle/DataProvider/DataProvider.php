<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use GBProd\ElasticsearchDataProviderBundle\Event\HasIndexedDocument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract class for data providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
abstract class DataProvider implements DataProviderInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;
    
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function run(
        Client $client, 
        $index, 
        $type, 
        EventDispatcherInterface $dispatcher
    ) {
        $this->client     = $client;
        $this->index      = $index;
        $this->type       = $type;
        $this->dispatcher = $dispatcher;
        
        $this->populate();
    }

    /**
     * Populate
     *
     * @return null
     */
    abstract protected function populate();

    /**
     * Index document
     *
     * @param string $id
     * @param array  $body
     */
    public function index($id, array $body)
    {
        $this->client->index([
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $id,
            'body'  => $body,
        ]);
        
        $this->dispatcher->dispatch(
            'elasticsearch.has_indexed_document',
            new HasIndexedDocument($id)
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return null;
    }
}    
