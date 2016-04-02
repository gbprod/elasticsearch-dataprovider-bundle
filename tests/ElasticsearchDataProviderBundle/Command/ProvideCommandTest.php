<?php

namespace Tests\GBProd\ElasticsearchDataProviderBundle\Command;

use GBProd\ElasticsearchDataProviderBundle\Command\ProvideCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\Handler;

/**
 * Tests for ProvideCommand
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommandTest extends \PHPUnit_Framework_TestCase
{
    private $commandTester;
    private $handler;
    
    public function setUp()
    {
        $application = new Application();
        $application->add(new ProvideCommand());

        $command = $application->find('elasticsearch:provide');
        $this->commandTester = new CommandTester($command);
        
        $this->handler = $this
            ->getMockBuilder(Handler::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $container = $this->getMock(ContainerInterface::class);
        
        $container
            ->expects($this->once())
            ->method('get')
            ->with('gbprod.elasticsearch_dataprovider.handler')
            ->willReturn($this->handler)
        ;
        
        $command->setContainer($container);
    }    
    public function testExecute()
    {
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with('my_index', 'my_type')
        ;
        
        $this->commandTester->execute([
            'command' => 'elasticsearch:provide',
            'index'   => 'my_index',
            'type'    => 'my_type',
        ]);
    }
}