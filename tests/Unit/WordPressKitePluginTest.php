<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use Concept7\WordPressKite\WordPressKitePlugin;

test('boot registers expected hooks', function () {
    Actions\expectAdded('init')->once();
    Actions\expectAdded('kite_daily_report')->once();

    $plugin = new WordPressKitePlugin;
    $plugin->boot();
});

test('scheduleCron schedules when not already scheduled', function () {
    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_daily_report')
        ->andReturn(false);

    Functions\expect('wp_schedule_event')
        ->once()
        ->withArgs(function ($time, $recurrence, $hook) {
            return $recurrence === 'daily' && $hook === 'kite_daily_report';
        });

    $plugin = new WordPressKitePlugin;
    $plugin->scheduleCron();
});

test('scheduleCron skips when already scheduled', function () {
    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_daily_report')
        ->andReturn(1234567890);

    $plugin = new WordPressKitePlugin;
    $plugin->scheduleCron();

    // wp_schedule_event should not have been called
    expect(true)->toBeTrue();
});

test('report throws when config is invalid', function () {
    // Ensure env vars are not set
    putenv('KITE_TOKEN');
    putenv('KITE_URI');

    Functions\when('apply_filters')->alias(function ($hook, $value) {
        return $value;
    });

    $plugin = new WordPressKitePlugin;
    $plugin->report();
})->throws(Exception::class, 'Project credentials are missing!');

test('actions returns array with default actions', function () {
    Functions\when('apply_filters')->alias(function ($hook, $value) {
        return $value;
    });

    $plugin = new WordPressKitePlugin;
    $actions = $plugin->actions();

    expect($actions)->toBeArray();
    expect($actions)->toHaveCount(1);
});
