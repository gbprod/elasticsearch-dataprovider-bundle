<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

/**
 * Entry for dataprovider registry
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class RegistryEntry
{
    /**
     * @var DataProviderInterface
     */
    private $provider;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @param DataProvider $provider
     * @param string       $index
     * @param string       $type
     */
    public function __construct(DataProviderInterface $provider, $index, $type)
    {
        $this->provider = $provider;
        $this->index    = $index;
        $this->type     = $type;
    }

    /**
     * Get provider
     *
     * @return DataProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    public function match($index, $type)
    {
        return ($this->getIndex() == $index && $this->getType() == $type)
            || ($this->getIndex() == $index && $type === null)
            || (null === $index && $type === null)
        ;
    }
}