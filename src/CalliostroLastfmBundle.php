<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle;

use Calliostro\LastfmBundle\DependencyInjection\CalliostroLastfmExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CalliostroLastfmBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new CalliostroLastfmExtension();
        }

        \assert($this->extension instanceof ExtensionInterface);

        return $this->extension;
    }
}
