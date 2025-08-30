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
                    'http_client_options' => [
                        'timeout' => 30,
                        'headers' => ['User-Agent' => 'TestApp/1.0']
                    ]
                ]
            ]
        );

        $this->assertEquals('test_key', $config['api_key']);
        $this->assertEquals('test_secret', $config['secret']);
        $this->assertEquals('test_session', $config['session']);
        $this->assertEquals(30, $config['http_client_options']['timeout']);
        $this->assertEquals(['User-Agent' => 'TestApp/1.0'], $config['http_client_options']['headers']);
    }

    public function testConfigurationDefaults(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = $processor->processConfiguration($configuration, []);

        $this->assertEquals('', $config['api_key']);
        $this->assertEquals('', $config['secret']);
        $this->assertArrayNotHasKey('session', $config);
        $this->assertEquals([], $config['http_client_options']);
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

        // Check that the main service is defined
        $this->assertTrue($container->hasDefinition('calliostro_last_fm_client.api_client'));
    }
}
