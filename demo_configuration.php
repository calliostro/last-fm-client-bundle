<?php

require_once __DIR__ . '/vendor/autoload.php';

use Calliostro\LastFmClientBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

$configuration = new Configuration();
$processor = new Processor();

echo "=== Testing Configuration Validation ===\n\n";

// Test 1: Empty configuration (should fail)
echo "1. Testing empty configuration:\n";
try {
    $config = $processor->processConfiguration($configuration, []);
    echo "✅ Configuration processed successfully (this shouldn't happen!)\n";
    var_dump($config);
} catch (Exception $e) {
    echo "❌ Exception thrown (expected): " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Only api_key (should fail)
echo "2. Testing with only api_key:\n";
try {
    $config = $processor->processConfiguration($configuration, [
        ['api_key' => 'test_key']
    ]);
    echo "✅ Configuration processed successfully (this shouldn't happen!)\n";
    var_dump($config);
} catch (Exception $e) {
    echo "❌ Exception thrown (expected): " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Both api_key and secret (should work)
echo "3. Testing with api_key and secret:\n";
try {
    $config = $processor->processConfiguration($configuration, [
        [
            'api_key' => 'test_key',
            'secret' => 'test_secret'
        ]
    ]);
    echo "✅ Configuration processed successfully:\n";
    var_dump($config);
} catch (Exception $e) {
    echo "❌ Exception thrown: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Full configuration
echo "4. Testing full configuration:\n";
try {
    $config = $processor->processConfiguration($configuration, [
        [
            'api_key' => 'my_api_key',
            'secret' => 'my_secret',
            'session' => 'my_session_key',
            'http_client_options' => [
                'timeout' => 30,
                'headers' => ['User-Agent' => 'MyApp/1.0']
            ]
        ]
    ]);
    echo "✅ Configuration processed successfully:\n";
    var_dump($config);
} catch (Exception $e) {
    echo "❌ Exception thrown: " . $e->getMessage() . "\n";
}
