<?php

namespace Calliostro\LastFmClientBundle\Tests;

use Calliostro\LastFmClientBundle\CalliostroLastFmClientBundle;
use LastFmClient\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'some API key',
            'secret' => 'some secret',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $client = $container->get('calliostro_last_fm_client.client');
        $this->assertInstanceOf(Client::class, $client);
    }
}

class CalliostroLastFmClientTestingKernel extends Kernel
{
    private $calliostroLastFmClientConfig;

    public function __construct(array $calliostroLastFmClientConfig = [])
    {
        $this->calliostroLastFmClientConfig = $calliostroLastFmClientConfig;

        parent::__construct('test', true);
    }

    public function registerBundles(): array
    {
        return [
            new CalliostroLastFmClientBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('calliostro_last_fm_client', $this->calliostroLastFmClientConfig);
        });
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment . '/' . spl_object_hash($this);
    }
}
