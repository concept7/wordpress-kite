<?php

namespace Concept7\WordPressKite;

use Concept7\Kite\Http\Integrations\Kite\Dtos\ProjectReportDto;
use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\WordPressKite\Actions\GetAcfProVersionAction;
use Concept7\WordPressKite\Actions\GetWooCommerceVersionAction;
use Concept7\WordPressKite\Actions\GetWordpressKiteVersionAction;
use Concept7\WordPressKite\Actions\GetWordPressVersionAction;
use Concept7\WordPressKite\Commands\KiteCheckAdvisoriesCommand;
use Concept7\WordPressKite\Commands\KiteReportCommand;
use Concept7\WordPressKite\ProjectInfo\WordPressProjectInfoCollector;

class WordPressKitePlugin
{
    public function boot(): void
    {
        add_action('init', [$this, 'scheduleCron']);
        add_action('kite_daily_report', [$this, 'cronReport']);
        add_action('kite_hourly_advisory_check', [$this, 'cronCheckAdvisories']);

        if (defined('WP_CLI') && WP_CLI) {
            \WP_CLI::add_command('kite report', new KiteReportCommand($this));
            \WP_CLI::add_command('kite check-advisories', new KiteCheckAdvisoriesCommand($this));
        }
    }

    public function scheduleCron(): void
    {
        if (! wp_next_scheduled('kite_daily_report')) {
            wp_schedule_event(time(), 'daily', 'kite_daily_report');
        }

        if (! wp_next_scheduled('kite_hourly_advisory_check')) {
            wp_schedule_event(time(), 'hourly', 'kite_hourly_advisory_check');
        }
    }

    public function cronReport(): void
    {
        try {
            $this->report();
        } catch (\Throwable $e) {
            error_log(sprintf('[Kite] Report failed: %s', $e->getMessage()));
        }
    }

    public function cronCheckAdvisories(): void
    {
        try {
            $this->checkAdvisories();
        } catch (\Throwable $e) {
            error_log(sprintf('[Kite] Advisory check failed: %s', $e->getMessage()));
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

    public function checkAdvisories(): void
    {
        Kite::make($this->config())
            ->projectInfoCollector(new WordPressProjectInfoCollector)
            ->checkAdvisories();
    }

    public function config(): KiteConfig
    {
        $config = $this->loadConfig();

        return new KiteConfig(
            token: $this->env('KITE_TOKEN'),
            uri: $this->env('KITE_URI') ?: null,
            monitoredPackages: $config['monitored_packages'] ?? [],
        );
    }

    private function loadConfig(): array
    {
        $path = trailingslashit(ABSPATH).'kite.php';

        if (! file_exists($path)) {
            return [];
        }

        $config = require $path;

        return is_array($config) ? $config : [];
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
        return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $default);
    }
}
