# 🎵 Last.fm Client Bundle (Legacy 0.x Branch)

[![Latest Stable Version](https://img.shields.io/packagist/v/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![License](https://img.shields.io/packagist/l/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI (Legacy 0.x)](https://github.com/calliostro/last-fm-client-bundle/actions/workflows/ci.yml/badge.svg?branch=legacy/0.x)](https://github.com/calliostro/last-fm-client-bundle/actions)
[![codecov](https://codecov.io/gh/calliostro/last-fm-client-bundle/graph/badge.svg?branch=legacy%2F0.x)](https://codecov.io/gh/calliostro/last-fm-client-bundle?branch=legacy%2F0.x)

> **Easy integration of [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient) into Symfony 6.4, 7 & 8**
>
> **Legacy Branch:** This is the final version (v0.4.3) for existing projects.  
> **New projects:** Use [v1.0.0](https://github.com/calliostro/last-fm-client-bundle) with modern [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client).

## ✨ Features

- Simple integration with Symfony 6.4, 7 & 8
- Supports Client API access & User authentication flows
- Autowire Last.fm API services
- Easy configuration
- Comprehensive API coverage for Last.fm

## 📦 Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### ⚡ Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require calliostro/last-fm-client-bundle
```

### 🛠️ Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require calliostro/last-fm-client-bundle
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

> **Supports Symfony 6.4 (LTS), 7.x and 8.x! 🎉**

## ⚙️ Configuration

First, you must register your application at <https://www.last.fm/api/account/create> to obtain the
`api_key` and `secret`.

For configuration create a new `config/packages/calliostro_last_fm_client.yaml` file. Here is an example:

```yaml
# config/packages/calliostro_last_fm_client.yaml
calliostro_last_fm_client:

    # Your API key
    api_key: '' # Required

    # Your secret
    secret: '' # Required

    # Optionally a fixed user session (e.g., for scrobbling)
    session: ~
```

> **💡 Tip**: Store your credentials securely using environment variables:
>
> ```yaml
> calliostro_last_fm_client:
>     api_key: '%env(LASTFM_API_KEY)%'
>     secret: '%env(LASTFM_SECRET)%'
> ```

## 🎬 Usage

This bundle provides multiple services for communication with Last.fm, which you can autowire by using the corresponding
type-hint.

### 🔑 Client Credentials

This is the simpler option if no user-related endpoints are required.

```php
// src/Controller/SomeController.php

use LastFmClient\Service\Artist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// ...

class SomeController
{
    #[Route('/artist/{name}', name: 'artist_info')]
    public function index(string $name, Artist $artistService): JsonResponse
    {
        try {
            $artist = $artistService->getInfo($name);
            $artistData = $artist->getData();

            return new JsonResponse($artistData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Artist not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
```

### 🧑‍💻 Authorization Code

If you want to trade on behalf of a Last.fm user (e.g., for scrobbling), you must have a session token. If you want to
use the API only for a specific user, you can set the `session` value in the configuration. These session tokens do not
expire.

You can also request a session token from Last.fm for the current user. First, you need an authorization token. Here is
an example:

```php
// src/Controller/LastFmController.php

namespace App\Controller;

use LastFmClient\Client;
use LastFmClient\Auth;
use LastFmClient\Service\Auth as AuthService;
use LastFmClient\Service\Track;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LastFmController extends AbstractController
{
    public function __construct(
        private readonly Client $client,
        private readonly Auth $auth,
        private readonly AuthService $authService,
        private readonly Track $trackService
    ) {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response('
            <h1>Last.fm API Demo</h1>
            <p>Welcome to the Last.fm Client Bundle demonstration!</p>
            <p><a href="/authorize">Click here to authorize with Last.fm</a></p>
        ', 200, ['Content-Type' => 'text/html']);
    }

    #[Route('/authorize', name: 'authorize')]
    public function authorize(): Response
    {
        $callbackUrl = $this->generateUrl('lastfm_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $authUrl = $this->client->getAuthUrl($callbackUrl);

        return $this->redirect($authUrl);
    }

    #[Route('/callback', name: 'lastfm_callback')]
    public function callback(Request $request): Response
    {
        $token = $request->query->get('token');
        
        if (!$token) {
            return $this->redirectToRoute('authorize');
        }

        $this->auth->setToken($token);
        $sessionData = $this->authService->getSession()->getData();

        // Store session key for future use
        $sessionKey = $sessionData['session']['key'];
        $this->auth->setSession($sessionKey);

        // Example: Scrobble a track
        $this->trackService->scrobble('Pink Floyd', 'Wish You Were Here', new \DateTime());

        return new Response('
            <h1>Last.fm Authorization Successful!</h1>
            <p>Welcome, ' . htmlspecialchars($sessionData['session']['name'] ?? 'Last.fm User') . '!</p>
            <p>Track scrobbled successfully!</p>
            <pre>' . htmlspecialchars(var_export($sessionData, true)) . '</pre>
            <p><a href="/">Back to Home</a></p>
        ', 200, ['Content-Type' => 'text/html']);
    }
}
```

> **⚠️ Note**: This example assumes proper service configuration. The Last.fm API services are automatically autowired when properly configured.

## 📚 Documentation

The services are provided by [snapshotpl/LastFmClient](https://github.com/snapshotpl/LastFmClient). Documentation can
be found there.

For more documentation, see the [Last.fm API documentation](http://www.last.fm/api).

## ⚡ Supported Versions

- **PHP 8.1 - 8.5**
- **Symfony 6.4 (LTS)**
- **Symfony 7.x**
- **Symfony 8.x**

## 🤝 Contributing

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.

## 🏁 Quick Start

1. Install the bundle with Composer 📦
2. Configure your Last.fm credentials 🔑
3. Autowire the service and start using the API! 🚀

## 💬 Support

For questions or help, feel free to open an issue or reach out! 😊
