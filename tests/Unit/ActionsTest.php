<?php

use Brain\Monkey\Functions;
use Concept7\WordPressKite\Actions\GetWordPressVersionAction;
use Illuminate\Support\Collection;

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
