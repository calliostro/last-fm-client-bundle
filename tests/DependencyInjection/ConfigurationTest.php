<?php

namespace Calliostro\LastFmClientBundle\Tests\DependencyInjection;

use Calliostro\LastFmClientBundle\DependencyInjection\CalliostroLastFmClientExtension;
use Calliostro\LastFmClientBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigurationTest extends TestCase
{
    public function testConfigurationStructure(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = $processor->processConfiguration(
            $configuration,
            [
                [
                    'api_key' => 'test_key',
                    'secret' => 'test_secret',
                    'session' => 'test_session',
                ]
            ]
        );

        $this->assertEquals('test_key', $config['api_key']);
        $this->assertEquals('test_secret', $config['secret']);
        $this->assertEquals('test_session', $config['session']);
    }

    public function testConfigurationDefaults(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = $processor->processConfiguration($configuration, []);

        $this->assertEquals('', $config['api_key']);
        $this->assertEquals('', $config['secret']);
        $this->assertArrayNotHasKey('session', $config);
    }

    public function testExtensionLoadsServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new CalliostroLastFmClientExtension();

        $extension->load([
            [
                'api_key' => 'test_key',
                'secret' => 'test_secret',
            ]
        ], $container);

        // Check that all services are defined
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.auth'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.client'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.album'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.artist'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.auth_service'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.track'));
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.user'));
    }
}
