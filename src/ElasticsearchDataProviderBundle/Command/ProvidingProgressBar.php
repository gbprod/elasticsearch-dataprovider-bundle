<?php

namespace GBProd\ElasticsearchDataProviderBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticsearchDataProviderBundle\Event\HasIndexingDocument;

/**
 * Progress bar for providing
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvidingProgressBar
{
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @param OutputInterface          $output
     */
    public function __construct(
        EventDispatcherInterface $dispatcher, 
        OutputInterface $output
    ) {
        $dispatcher->addListener(
            'elasticsearch.has_started_handling',
            function (HasStartedHandling $event) use ($output) {
                $output->writeln(sprintf(
                    '<info>Start running <comment>%d</comment> providers</info>', 
                    count($event->getEntries())
                ));
            }
        );

        $dispatcher->addListener(
            'elasticsearch.has_started_providing',
            function (HasStartedProviding $event) use ($output) {
                $output->writeln(sprintf(
                    '<info>Start running <comment>%s</comment> provider</info>',
                    get_class($event->getEntry()->getProvider())
                ));
            }
        );
        
        $dispatcher->addListener(
            'elasticsearch.has_indexed_document',
            function (HasIndexingDocument $event) use ($output) {
                $output->writeln(sprintf(
                    '<info>Indexing <comment>%s</comment> document</info>',
                    get_class($event->getEntry()->getId())
                ));
            }
        );
    }
}