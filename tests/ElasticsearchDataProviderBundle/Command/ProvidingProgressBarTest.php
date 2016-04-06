<?php

namespace ElasticsearchDataProviderBundle\Command;

use GBProd\ElasticsearchDataProviderBundle\Command\ProvidingProgressBar;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProviderInterface;
use GBProd\ElasticsearchDataProviderBundle\DataProvider\RegistryEntry;
use GBProd\ElasticsearchDataProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticsearchDataProviderBundle\Event\HasIndexedDocument;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticsearchDataProviderBundle\Event\HasStartedProviding;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ProvidingProgressBar
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvidingProgressBarTest extends \PHPUnit_Framework_TestCase
{
    private $testedInstance;
    private $dispatcher;
    private $output;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->output     = $this->getMock(OutputInterface::class);

        $this->testedInstance = new ProvidingProgressBar(
            $this->dispatcher,
            $this->output
        );
    }

    public function testOnStartedHandlingDisplayNumberOfEntries()
    {
        $event = new HasStartedHandling([1, 2, 3]);

        $this
            ->output
            ->expects($this->once())
            ->method('writeln')
            ->with(
                $this->stringContains('<comment>3</comment>')
            )
        ;

        $this->testedInstance->onStartedHandling($event);
    }

    public function testOnStartedProvidingDisplayProviderName()
    {
        $provider = $this->getMock(DataProviderInterface::class);
        $entry = new RegistryEntry($provider, 'my_index', 'my_type');

        $event = new HasStartedProviding($entry);

        $this
            ->output
            ->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains(get_class($provider)))
        ;

        $this->testedInstance->onStartedProviding($event);

        $this->assertEmpty($this->testedInstance->progressBar);
    }

    public function testOnStartedProvidingCreateProgressBar()
    {
        $provider = $this->getMock(DataProviderInterface::class);
        $provider
            ->expects($this->any())
            ->method('count')
            ->willReturn(42)
        ;

        $entry = new RegistryEntry($provider, 'my_index', 'my_type');
        $event = new HasStartedProviding($entry);

        $this->testedInstance->onStartedProviding($event);

        $this->assertNotEmpty($this->testedInstance->progressBar);
        $this->assertInstanceOf(
            ProgressBar::class,
            $this->testedInstance->progressBar
        );
        $this->assertEquals(42, $this->testedInstance->progressBar->getMaxSteps());
    }

    public function testOnIndexedDocumentAdvanceProgress()
    {
        $provider = $this->getMock(DataProviderInterface::class);
        $entry = new RegistryEntry($provider, 'my_index', 'my_type');
        $event = new HasIndexedDocument($entry);

        $this->testedInstance->progressBar = $this
            ->getMock(ProgressBar::class, [], [$this->output])
        ;

        $this->testedInstance
            ->progressBar
            ->expects($this->once())
            ->method('advance')
        ;

        $this->testedInstance->onIndexedDocument($event);
    }

    public function testOnFinishedProvidingFinishProgress()
    {
        $provider = $this->getMock(DataProviderInterface::class);
        $entry = new RegistryEntry($provider, 'my_index', 'my_type');
        $event = new HasFinishedProviding($entry);

        $this->testedInstance->progressBar = $this
            ->getMock(ProgressBar::class, [], [$this->output])
        ;

        $this->testedInstance
            ->progressBar
            ->expects($this->once())
            ->method('finish')
        ;

        $this->testedInstance->onFinishedProviding($event);

        $this->assertEmpty($this->testedInstance->progressBar);
    }
}
