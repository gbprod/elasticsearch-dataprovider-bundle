<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

use Elasticsearch\Client;
use GBProd\ElasticsearchDataProviderBundle\Event\HasProvidedDocument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract class for data providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
abstract class BulkDataProvider implements DataProvider
{
    const DEFAULT_BULK_SIZE = 1000;

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
     * @var int
     */
    private $bulkSize = self::DEFAULT_BULK_SIZE;

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
    public function run(Client $client, $index, $type, EventDispatcherInterface $dispatcher)
    {
        $this->initialize($client, $index, $type, $dispatcher);
        $this->populate();
        $this->commit();
    }

    private function initialize(Client $client, $index, $type, EventDispatcherInterface $dispatcher)
    {
        $this->client     = $client;
        $this->index      = $index;
        $this->type       = $type;
        $this->dispatcher = $dispatcher;

        $this->currentBulkSize = 0;
        $this->currentBulk     = ['body' => []];
    }

    /**
     * Populate
     *
     * @return null
     */
    abstract protected function populate();

    private function commit()
    {
        $this->flushBulk();

        $this->client->indices()->refresh([
            'index' => $this->index,
        ]);
    }

    /**
     * Index document
     *
     * @param string $id
     * @param array  $body
     */
    public function index($id, array $body)
    {
        $this->addBulkAction('index', $id, $body);
    }

    private function addBulkAction($action, $id, array $body = null)
    {
        $this->addBulkData($action, $id, $body);

        $this->incrementBulk();

        $this->dispatcher->dispatch(
            'elasticsearch.has_provided_document',
            new HasProvidedDocument($id)
        );
    }

    private function addBulkData($action, $id, array $body = null)
    {
        $this->currentBulk['body'][] = [
            $action => [
                '_index' => $this->index,
                '_type'  => $this->type,
                '_id'    => $id,
            ]
        ];

        if (null !== $body) {
            $this->currentBulk['body'][] = $body;
        }
    }

    private function incrementBulk()
    {
        if ($this->shouldFlushBulk()) {
            $this->flushBulk();
        }

        $this->currentBulkSize++;
    }

    private function shouldFlushBulk()
    {
        return $this->currentBulkSize >= $this->bulkSize
            && $this->currentBulkSize > 0
        ;
    }

    private function flushBulk()
    {
        $this->client->bulk($this->currentBulk);

        $this->currentBulkSize = 0;
        $this->currentBulk     = ['body' => []];
    }


    /**
     * Delete document
     *
     * @param string $id
     */
    public function delete($id)
    {
        $this->addBulkAction('delete', $id);
    }

    /**
     * Create a document
     *
     * @param string $id
     * @param array  $body
     */
    public function create($id, array $body)
    {
        $this->addBulkAction('create', $id, $body);
    }

    /**
     * Update a document
     *
     * @param string $id
     * @param array  $body
     */
    public function update($id, array $body)
    {
        $this->addBulkAction('update', $id, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return null;
    }

    /**
     * Change bulk size
     *
     * @param int $bulkSize
     *
     * @return self
     */
    public function changeBulkSize($bulkSize = self::DEFAULT_BULK_SIZE)
    {
        $this->bulkSize = $bulkSize;

        return $this;
    }
}
