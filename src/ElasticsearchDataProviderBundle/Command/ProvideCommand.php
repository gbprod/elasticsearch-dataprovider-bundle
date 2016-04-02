<?php

namespace GBProd\ElasticsearchDataProviderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to run providing
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:provide')
            ->setDescription('Provide data to Elasticsearch')
            ->addArgument(
                'index',
                InputArgument::OPTIONAL,
                'Index to provide'
            )
            ->addArgument(
                'type',
                null,
                InputArgument::OPTIONAL,
                'Type to provide'
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getContainer()
            ->get('gbprod.elasticsearch_dataprovider.handler')
        ;
        
        $handler->handle(
            $input->getArgument('index'),
            $input->getArgument('type')
        );
    }
}
