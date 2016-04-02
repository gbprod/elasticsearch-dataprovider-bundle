<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;

/**
 * Interface for dataprovider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
interface DataProviderInterface
{
    /**
     * Populate index
     *
     * @param Client $client
     * @index string $index
     * @index string $type
     */
    public function run(Client $client, $index, $type);
}