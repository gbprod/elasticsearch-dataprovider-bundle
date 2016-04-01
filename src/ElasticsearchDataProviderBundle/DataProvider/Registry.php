<?php

namespace GBProd\ElasticsearchDataProviderBundle\DataProvider;

/**
 * Registry for DataProviders
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class Registry
{
    /**
     * @var array<DataProvider>
     */
    private $providers = [];
    
    /**
     * Add a provider to the registry
     * 
     * @param DataProvider $provider
     * @param string       $index
     * @param string       $type
     */
    public function addProvider(DataProvider $provider, $index, $type)
    {
        $this->providers[] = [
            'provider' => $provider,
            'index'    => $index,
            'type'     => $type,
        ];
        
        return $this;    
    }
    
    /**
     * Get providers for index and type
     * 
     * @param string $index
     * @param string $type
     * 
     * @return array<DataProvider>
     */
    public function getProviders($index = null, $type = null)
    {
        $filteredProviders = $this->filter($index, $type);
        
        return $this->mapProviders($filteredProviders);
    }
    
    private function filter($index, $type)
    {
        return array_filter(
            $this->providers,
            function ($providerData) use ($index, $type) {
                return $this->match($providerData, $index, $type);
            }
        );
    }

    private function match($providerData, $index, $type)
    {
        return (null === $index && $type === null)
            || ($providerData['index'] == $index && $type === null)
            || ($providerData['index'] == $index && $providerData['type'] == $type)
        ;
    
    }
    
    private function mapProviders(array $providers)
    {
        return array_map(
            function ($providerData) {
                return $providerData['provider'];    
            },
            $providers
        );
    }
}