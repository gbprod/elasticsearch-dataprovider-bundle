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
    const BATCH_SIZE = 1000;

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
     * @var array
     */
    private $currentBulk;

    /**
     * @var int
     */
    private $currentBulkSize;

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

        $this->currentBulkSize = 0;
        $this->currentBulk     = ['body' => []];

        $this->populate();

        $this->flushBulk();
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
        $this->currentBulk['body'][] = [
            'index' => [
                '_index' => $this->index,
                '_type'  => $this->type,
                '_id'    => $id,
            ]
        ];

        $this->currentBulk['body'][] = $body;

        if ($this->shouldFlushBulk()) {
            $this->flushBulk();
        }

        $this->currentBulkSize++;

        $this->dispatcher->dispatch(
            'elasticsearch.has_indexed_document',
            new HasIndexedDocument($id)
        );
    }

    protected function flushBulk()
    {
        $this->client->bulk($this->currentBulk);

        $this->currentBulkSize = 0;
        $this->currentBulk     = ['body' => []];
    }

    private function shouldFlushBulk()
    {
        return $this->currentBulkSize >= self::BATCH_SIZE;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return null;
    }
}
