Last.fm Client Bundle
=====================

[![Build Status](https://api.travis-ci.com/calliostro/last-fm-client-bundle.svg)](https://www.travis-ci.com/github/calliostro/last-fm-client-bundle)
[![Version](https://poser.pugx.org/calliostro/last-fm-client-bundle/version)](//packagist.org/packages/calliostro/last-fm-client-bundle)
[![License](https://poser.pugx.org/calliostro/last-fm-client-bundle/license)](//packagist.org/packages/calliostro/last-fm-client-bundle)

This bundle provides a simple integration of [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient)
into Symfony 5.


Installation
------------

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require calliostro/last-fm-client-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require calliostro/last-fm-client-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Calliostro\LastFmClientBundle\CalliostroLastFmClientBundle::class => ['all' => true],
];
```


Configuration
-------------

For configuration create a new `config/packages/calliostro_discogs.yaml` file. The default values are:

```yaml
# config/packages/calliostro_last_fm_client.yaml
calliostro_last_fm_client:

  # Your API key
  api_key: ~

  # Your secret
  secret: ~

  # Optionally a fixed user token
  token: null

  # Optionally a fixed user session
  session: null
```


Usage
-----

This bundle provides multiple service for communication with Last.fm, which you can autowire by using the corresponding
type-hint.

```php
// src/Controller/SomeController.php

use LastFmClient\Service;
// ...

class SomeController
{
    public function index(Service\Artist $artistService)
    {
        $artist = $artistService->getInfo('Cher');

        var_dump($artist->getData());

        // ...
    }
}
```

The services are provided by [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient). A documentation can
be found there.


Documentation
-------------

For more documentation, see the [Last.fm API documentation](http://www.last.fm/api).


Contributing
------------

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.
