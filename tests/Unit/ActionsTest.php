<?php

use Brain\Monkey\Functions;
use Concept7\Kite\Actions\GetComposerPackageVersionAction;
use Concept7\Kite\Support\Collection;
use Concept7\WordPressKite\Actions\GetAcfProVersionAction;
use Concept7\WordPressKite\Actions\GetWooCommerceVersionAction;
use Concept7\WordPressKite\Actions\GetWordPressVersionAction;

test('GetWordPressVersionAction returns wordpress version', function () {
    Functions\expect('get_bloginfo')
        ->once()
        ->with('version')
        ->andReturn('6.5.2');

    $action = new GetWordPressVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result->toArray())->toBe([
        ['key' => 'wordpress_version', 'value' => '6.5.2'],
    ]);
});

test('GetWordPressVersionAction skips when version is empty', function () {
    Functions\expect('get_bloginfo')
        ->once()
        ->with('version')
        ->andReturn('');

    $action = new GetWordPressVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(0);
});

test('GetWooCommerceVersionAction extends GetComposerPackageVersionAction', function () {
    expect(new GetWooCommerceVersionAction)->toBeInstanceOf(GetComposerPackageVersionAction::class);
});

test('GetAcfProVersionAction extends GetComposerPackageVersionAction', function () {
    expect(new GetAcfProVersionAction)->toBeInstanceOf(GetComposerPackageVersionAction::class);
});
