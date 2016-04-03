<?php

namespace GBProd\ElasticsearchDataProviderBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticsearchDataProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticsearchDataProviderBundle\Event\HasIndexedDocument;

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
     * @var ProgressBar
     */
    private $progressBar;
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @param OutputInterface          $output
     */
    public function __construct(
        EventDispatcherInterface $dispatcher, 
        OutputInterface $output
    ) {
        $this->output = $output;
        
        $dispatcher->addListener(
            'elasticsearch.has_started_handling',
            [$this, 'onStartedHandling']
        );

        $dispatcher->addListener(
            'elasticsearch.has_started_providing',
            [$this, 'onStartedProviding']
        );
        
        $dispatcher->addListener(
            'elasticsearch.has_finished_providing',
            [$this, 'onFinishedProviding']
        );
        
        $dispatcher->addListener(
            'elasticsearch.has_indexed_document',
            [$this, 'onIndexedDocument']
        );
    }
    
    public function onStartedHandling(HasStartedHandling $event) 
    {
        $this->output->writeln(sprintf(
            '<info>Start running <comment>%d</comment> providers</info>', 
            count($event->getEntries())
        ));
    }
    
    public function onStartedProviding(HasStartedProviding $event)
    {
        $this->output->writeln(sprintf(
            '<info>Start running <comment>%s</comment> provider</info>',
            get_class($event->getEntry()->getProvider())
        ));
        
        $count = $event->getEntry()->getProvider()->count();
        if (null !== $count) {
            $this->progressBar = new ProgressBar($this->output, $count);
            $this->progressBar->setFormat(
                ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%'
            );
        } 
    }
    
    public function onFinishedProviding(HasFinishedProviding $event)
    {
        $progressBar->finish();
        $this->progressBar = null;
    }

    public function onIndexedDocument(HasIndexedDocument $event) 
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }
}
