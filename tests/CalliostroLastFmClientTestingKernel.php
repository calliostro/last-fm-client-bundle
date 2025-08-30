<?php

namespace Calliostro\LastFmClientBundle\Tests;

use Calliostro\LastFmClientBundle\CalliostroLastFmClientBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class CalliostroLastFmClientTestingKernel extends Kernel
{
    public function __construct(
        private readonly array $calliostroLastFmClientConfig = []
    ) {
        parent::__construct('test', true);
    }

    public function registerBundles(): array
    {
        return [
            new CalliostroLastFmClientBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container): void {
            $container->loadFromExtension('calliostro_last_fm_client', $this->calliostroLastFmClientConfig);
        });
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment . '/' . spl_object_hash($this);
    }
}
