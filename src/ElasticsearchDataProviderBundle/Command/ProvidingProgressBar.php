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
    const PROGRESS_BAR_TEMPLATE = ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

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
            '<info> - Running <comment>%s</comment> provider into <comment>%s/%s</comment></info>',
            get_class($event->getEntry()->getProvider()),
            $event->getEntry()->getIndex(),
            $event->getEntry()->getType()
        ));

        $count = $event->getEntry()->getProvider()->count();
        if (null !== $count) {
            $this->progressBar = new ProgressBar($this->output, $count);
            $this->progressBar->setFormat(self::PROGRESS_BAR_TEMPLATE);
        }
    }

    public function onFinishedProviding(HasFinishedProviding $event)
    {
        $this->progressBar->finish();
        $this->output->writeln('');
        $this->progressBar = null;
    }

    public function onIndexedDocument(HasIndexedDocument $event)
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }
}
