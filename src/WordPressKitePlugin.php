<?php

namespace Concept7\WordPressKite;

use Concept7\Kite\Http\Integrations\Kite\Dtos\ProjectReportDto;
use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\WordPressKite\Actions\GetAcfProVersionAction;
use Concept7\WordPressKite\Actions\GetWooCommerceVersionAction;
use Concept7\WordPressKite\Actions\GetWordpressKiteVersionAction;
use Concept7\WordPressKite\Actions\GetWordPressVersionAction;
use Concept7\WordPressKite\Commands\KiteReportCommand;
use Concept7\WordPressKite\ProjectInfo\WordPressProjectInfoCollector;

class WordPressKitePlugin
{
    public function boot(): void
    {
        add_action('init', [$this, 'scheduleCron']);
        add_action('kite_daily_report', [$this, 'cronReport']);

        if (defined('WP_CLI') && WP_CLI) {
            \WP_CLI::add_command('kite report', new KiteReportCommand($this));
        }
    }

    public function scheduleCron(): void
    {
        if (! wp_next_scheduled('kite_daily_report')) {
            wp_schedule_event(time(), 'daily', 'kite_daily_report');
        }
    }

    public function cronReport(): void
    {
        try {
            $this->report();
        } catch (\Throwable) {
            //
        }
    }

    public function report(): ProjectReportDto
    {
        $actions = array_map(fn ($action) => new $action, $this->actions());

        return Kite::make($this->config())
            ->projectInfoCollector(new WordPressProjectInfoCollector)
            ->addActions($actions)
            ->report();
    }

    public function config(): KiteConfig
    {
        return new KiteConfig(
            uri: $this->env('KITE_URI'),
            projectId: $this->env('KITE_PROJECT_ID'),
            projectKey: $this->env('KITE_PROJECT_KEY'),
        );
    }

    public function actions(): array
    {
        $actions = [
            GetWordPressVersionAction::class,
            GetWooCommerceVersionAction::class,
            GetAcfProVersionAction::class,
            GetWordpressKiteVersionAction::class,
        ];

        return apply_filters('kite_actions', $actions);
    }

    protected function env(string $key, mixed $default = null): mixed
    {
        return getenv($key) ?: $default;
    }
}
