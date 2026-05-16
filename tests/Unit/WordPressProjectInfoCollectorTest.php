<?php

use Brain\Monkey\Functions;
use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Concept7\WordPressKite\ProjectInfo\WordPressProjectInfoCollector;

test('implements ProjectInfoCollectorInterface', function () {
    expect(new WordPressProjectInfoCollector)->toBeInstanceOf(ProjectInfoCollectorInterface::class);
});

test('collect returns expected array keys', function () {
    Functions\when('wp_get_environment_type')->justReturn('production');
    Functions\when('get_site_url')->justReturn('https://example.com');
    Functions\when('get_plugins')->justReturn([]);
    Functions\when('wp_get_themes')->justReturn([]);

    $collector = new WordPressProjectInfoCollector;
    $result = $collector->collect();

    expect($result)->toHaveKeys([
        'hostname',
        'environment',
        'is_debug_mode_on',
        'url',
        'packages',
    ]);
});

test('collect uses correct WordPress functions', function () {
    Functions\when('wp_get_environment_type')->justReturn('staging');
    Functions\when('get_site_url')->justReturn('https://staging.example.com');
    Functions\when('get_plugins')->justReturn([]);
    Functions\when('wp_get_themes')->justReturn([]);

    $collector = new WordPressProjectInfoCollector;
    $result = $collector->collect();

    expect($result['environment'])->toBe('staging');
    expect($result['url'])->toBe('https://staging.example.com');
});
