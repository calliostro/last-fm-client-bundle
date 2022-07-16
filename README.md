Last.fm Client Bundle
=====================

[![Build Status](https://app.travis-ci.com/calliostro/last-fm-client-bundle.svg?branch=main)](https://www.travis-ci.com/github/calliostro/last-fm-client-bundle)
[![Version](https://poser.pugx.org/calliostro/last-fm-client-bundle/version)](//packagist.org/packages/calliostro/last-fm-client-bundle)
[![License](https://poser.pugx.org/calliostro/last-fm-client-bundle/license)](//packagist.org/packages/calliostro/last-fm-client-bundle)

This bundle provides a simple integration of [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient)
into Symfony 5 or Symfony 6.


Installation
------------

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

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

First, you must register your application at https://www.last.fm/api/account/create to obtain the
`api_key` and `secret`.

For configuration create a new `config/packages/calliostro_last_fm_client.yaml` file. Here is an example:

```yaml
# config/packages/calliostro_last_fm_client.yaml
calliostro_last_fm_client:

  # Your API key
  api_key:              '' # Required

  # Your secret
  secret:               '' # Required

  # Optionally a fixed user session (e.g. for scrobbling)
  session:              ~
```

Usage
-----

This bundle provides multiple service for communication with Last.fm, which you can autowire by using the corresponding
type-hint.

### Client Credentials

This is the simpler option if no user-related endpoints are required.

```php
// src/Controller/SomeController.php

use LastFmClient\Service\Artist;
// ...

class SomeController
{
    public function index(Artist $artistService)
    {
        $artist = $artistService->getInfo('Cher');

        var_dump($artist->getData());

        // ...
    }
}
```

### Authorization Code

If you want to trade on behalf of a Last.fm user (e.g. for scrobbling), you must have a session token. If you want to
use the API only for a specific user, you can set the `session` value in the configuration. These session tokens do not 
expire.

You can also request a session token from Last.fm for the current user. First, you need an authorization token. Here is
an example:

```php
// src/Controller/SomeController.php

namespace App\Controller;

use LastFmClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SomeController extends AbstractController
{
    /**
     * @Route("/redirect")
     */
    public function redirectToLastFm(LastFmClient\Client $client)
    {
        $callbackUrl = $this->generateUrl('some_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $authUrl = $client->getAuthUrl($callbackUrl);

        return $this->redirect($authUrl);
    }

    /**
     * @Route("/callback", name="some_callback")
     */
    public function callbackFromLastFm(
        Request $request,
        LastFmClient\Auth $auth,
        LastFmClient\Service\Auth $authService
    ) {
        $token = $request->query->get('token');
        $auth->setToken($token);
        $sessionData = $authService->getSession()->getData();

        // You can store $sessionKey somewhere for later reuse
        $sessionKey = $sessionData['session']['key'];

        $auth->setSession($sessionKey);

        // Now you can use, for example, the LastFmClient\Service\Track service for scrobbling
        // ...
    }
}
```

Documentation
-------------

The services are provided by [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient). A documentation can
be found there.

For more documentation, see the [Last.fm API documentation](http://www.last.fm/api).


Contributing
------------

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.
