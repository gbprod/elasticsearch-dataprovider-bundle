<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface for dataprovider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
interface DataProvider
{
    /**
     * Populate index
     *
     * @param Client                   $client
     * @param string                   $index
     * @param string                   $type
     * @param EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function run(Client $client, $index, $type, EventDispatcherInterface $dispatcher);

    /**
     * Number of documents that should be indexed
     *
     * @return int
     */
    public function count();
}
