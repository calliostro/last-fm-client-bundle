<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit;

use Calliostro\LastfmBundle\CalliostroLastfmBundle;
use Calliostro\LastfmBundle\DependencyInjection\CalliostroLastfmExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class CalliostroLastfmBundleTest extends UnitTestCase
{
    public function testGetPath(): void
    {
        $bundle = new CalliostroLastfmBundle();

        $path = $bundle->getPath();

        // The bundle path should point to the root directory (parent of src)
        $this->assertStringContainsString('last-fm', $path);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path.'/src/CalliostroLastfmBundle.php');
    }

    public function testGetContainerExtensionReturnsValidExtension(): void
    {
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        /* @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
        $this->assertInstanceOf(CalliostroLastfmExtension::class, $extension);
    }

    public function testGetContainerExtensionReturnsSameInstanceOnMultipleCalls(): void
    {
        $bundle = new CalliostroLastfmBundle();

        $extension1 = $bundle->getContainerExtension();
        $extension2 = $bundle->getContainerExtension();

        // Should return the same instance (lazy initialization)
        $this->assertSame($extension1, $extension2);
        $this->assertEquals('calliostro_lastfm', $extension1->getAlias());
    }

    public function testBundleIsProperSymfonyBundle(): void
    {
        $bundle = new CalliostroLastfmBundle();

        /* @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertInstanceOf(CalliostroLastfmBundle::class, $bundle);
        /* @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertInstanceOf(\Symfony\Component\HttpKernel\Bundle\Bundle::class, $bundle);
    }
}
