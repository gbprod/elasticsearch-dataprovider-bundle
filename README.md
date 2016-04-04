# Elasticsearch dataprovider bundle

Bundle that can easily provide data in elasticsearch indices with Symfony using [M6Web elasticsearch bundle](https://github.com/M6Web/ElasticsearchBundle).

[![Build Status](https://travis-ci.org/gbprod/elasticsearch-dataprovider-bundle.svg?branch=master)](https://travis-ci.org/gbprod/elasticsearch-dataprovider-bundle) 
[![Code Coverage](https://scrutinizer-ci.com/g/gbprod/elasticsearch-dataprovider-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gbprod/elasticsearch-dataprovider-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gbprod/elasticsearch-dataprovider-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gbprod/elasticsearch-dataprovider-bundle/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/gbprod/elasticsearch-dataprovider-bundle/v/stable)](https://packagist.org/packages/gbprod/doctrine-specification) 
[![Total Downloads](https://poser.pugx.org/gbprod/elasticsearch-dataprovider-bundle/downloads)](https://packagist.org/packages/gbprod/doctrine-specification) 
[![Latest Unstable Version](https://poser.pugx.org/gbprod/elasticsearch-dataprovider-bundle/v/unstable)](https://packagist.org/packages/gbprod/doctrine-specification) 
[![License](https://poser.pugx.org/gbprod/elasticsearch-dataprovider-bundle/license)](https://packagist.org/packages/gbprod/doctrine-specification)

## Installation

Download bundle using [composer](https://getcomposer.org/) :

```bash
composer require gbprod/elasticsearch-dataprovider-bundle
```

Declare in your `app/AppKernel.php` file:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new M6Web\Bundle\ElasticsearchBundle\M6WebElasticsearchBundle(),
        new GBProd\ElasticsearchDataProviderBundle\ElasticsearchDataProviderBundle(),
    );
}
```

## Usage

### Configure Elasticsearch clients

See [M6WebElasticsearchBundle](https://github.com/M6Web/ElasticsearchBundle) for configuring clients.

### Create a Data Provider

```php
<?php

namespace GBProd\AcmeBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProvider;

class SuperHeroDataProvider extends DataProvider
{
    protected function populate()
    {
        $this->index(
            'Spider-Man', // id of the document
            [
                "name" => "Spider-Man",
                "description" => "Bitten by a radioactive spider, high school student Peter Parker gained the speed, strength and powers of a spider. Adopting the name Spider-Man, Peter hoped to start a career using his new abilities. Taught that with great power comes great responsibility, Spidey has vowed to use his powers to help people.",
            ]
        );
        
        $this->index(
            'Hulk', // id of the document
            [
                "name" => "Hulk",
                "description" => "Caught in a gamma bomb explosion while trying to save the life of a teenager, Dr. Bruce Banner was transformed into the incredibly powerful creature called the Hulk. An all too often misunderstood hero, the angrier the Hulk gets, the stronger the Hulk gets.",
            ]
        );
    }
}
```

### Register your provider

```yml
# AcmeBundle/Resources/config/services.yml

services:
    acme_bundle.superhero_dataprovider:
        class: GBProd\AcmeBundle\DataProvider\SuperHeroDataProvider
        tags:
            - { name: elasticsearch.dataprovider, index: app, type: superheros }
```

### Provide

```bash
php app/console elasticsearch:provide app superheros
```

You also can provide a full index:

```bash
php app/console elasticsearch:provide app
```

Or run all providers:

```bash
php app/console elasticsearch:provide
```

You can set a specific client to use (if not default):

```bash
php app/console elasticsearch:provide app superheros --client=my_client
```

## Example using doctrine

```php
<?php

namespace GBProd\AcmeBundle\DataProvider;

use GBProd\ElasticsearchDataProviderBundle\DataProvider\DataProvider;
use Doctrine\ORM\EntityManager;

class SuperHeroDataProvider extends DataProvider
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    protected function populate()
    {
        $query = $this->em
            ->createQuery('select s from AcmeBundle\Model\SuperHero s')
        ;
        
        $results = $query->iterate();
        foreach ($results as $row) {
            $this->index(
                $row[0], 
                [
                    "name" => $row[0],
                    "description" => $row[1],
                ]
            );

            $this->em->detach($row[0]);
        }
    }
}
```

```yml
# AcmeBundle/Resources/config/services.yml

services:
    acme_bundle.superhero_dataprovider:
        class: GBProd\AcmeBundle\DataProvider\SuperHeroDataProvider
        arguments:
            - '@doctrine.orm.entity_manager'
        tags:
            - { name: elasticsearch.dataprovider, index: app, type: superheros }
```