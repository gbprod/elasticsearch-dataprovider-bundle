<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;

/**
 * Abstract class for data providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
abstract class DataProvider
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
     * Populate index
     *
     * @param Client $client
     * @index string $index
     * @index string $type
     */
    public function run(Client $client, $index, $type)
    {
        $this->client = $client;
        $this->index  = $index;
        $this->type   = $type;
        
        $this->populate();
    }

    /**
     * Populate
     *
     * @return null
     */
    abstract public function populate();

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
    }
}