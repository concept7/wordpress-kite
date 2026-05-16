<?php

namespace Concept7\WordPressKite\Support;

use Concept7\Kite\Enums\Ecosystem;

class WordPressPackages
{
    /** @return list<array{name: string, version: string, ecosystem: Ecosystem}> */
    public static function installed(): array
    {
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        return [
            ...static::plugins(),
            ...static::themes(),
        ];
    }

    /** @return list<array{name: string, version: string, ecosystem: Ecosystem}> */
    private static function plugins(): array
    {
        return collect(get_plugins())
            ->map(fn (array $plugin): array => [
                'name' => $plugin['TextDomain'] ?: $plugin['Name'],
                'version' => $plugin['Version'],
                'ecosystem' => Ecosystem::Wordpress,
            ])
            ->values()
            ->all();
    }

    /** @return list<array{name: string, version: string, ecosystem: Ecosystem}> */
    private static function themes(): array
    {
        return collect(wp_get_themes())
            ->map(fn (\WP_Theme $theme): array => [
                'name' => $theme->get_stylesheet(),
                'version' => $theme->get('Version'),
                'ecosystem' => Ecosystem::Wordpress,
            ])
            ->values()
            ->all();
    }
}
