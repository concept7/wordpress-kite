<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use Concept7\WordPressKite\WordPressKitePlugin;

test('boot registers expected hooks', function () {
    Actions\expectAdded('init')->once();
    Actions\expectAdded('kite_daily_report')->once();
    Actions\expectAdded('kite_check_advisories')->once();

    $plugin = new WordPressKitePlugin;
    $plugin->boot();
});

test('scheduleCron schedules both hooks when not already scheduled', function () {
    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_daily_report')
        ->andReturn(false);

    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_check_advisories')
        ->andReturn(false);

    Functions\expect('wp_schedule_event')
        ->once()
        ->withArgs(function ($time, $recurrence, $hook) {
            return $recurrence === 'daily' && $hook === 'kite_daily_report';
        });

    Functions\expect('wp_schedule_event')
        ->once()
        ->withArgs(function ($time, $recurrence, $hook) {
            return $recurrence === 'hourly' && $hook === 'kite_check_advisories';
        });

    $plugin = new WordPressKitePlugin;
    $plugin->scheduleCron();
});

test('scheduleCron skips both hooks when already scheduled', function () {
    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_daily_report')
        ->andReturn(1234567890);

    Functions\expect('wp_next_scheduled')
        ->once()
        ->with('kite_check_advisories')
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

test('reportRanTooRecently is true when report ran within the configured interval', function () {
    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT=15');

    Functions\expect('get_transient')
        ->once()
        ->with(WordPressKitePlugin::LAST_RAN_AT_TRANSIENT)
        ->andReturn(time() - (5 * 60));

    $plugin = new WordPressKitePlugin;

    expect($plugin->reportRanTooRecently())->toBeTrue();

    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT');
});

test('reportRanTooRecently is false once the configured interval has passed', function () {
    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT=15');

    Functions\expect('get_transient')
        ->once()
        ->with(WordPressKitePlugin::LAST_RAN_AT_TRANSIENT)
        ->andReturn(time() - (20 * 60));

    $plugin = new WordPressKitePlugin;

    expect($plugin->reportRanTooRecently())->toBeFalse();

    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT');
});

test('reportRanTooRecently is false when report has never run', function () {
    Functions\expect('get_transient')
        ->once()
        ->with(WordPressKitePlugin::LAST_RAN_AT_TRANSIENT)
        ->andReturn(false);

    $plugin = new WordPressKitePlugin;

    expect($plugin->reportRanTooRecently())->toBeFalse();
});

test('reportRanTooRecently interval is configurable', function () {
    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT=60');

    Functions\expect('get_transient')
        ->once()
        ->with(WordPressKitePlugin::LAST_RAN_AT_TRANSIENT)
        ->andReturn(time() - (30 * 60));

    $plugin = new WordPressKitePlugin;

    expect($plugin->reportRanTooRecently())->toBeTrue();

    putenv('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT');
});
