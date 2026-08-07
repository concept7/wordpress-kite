<?php

namespace Concept7\WordPressKite;

use Concept7\Kite\Http\Integrations\Kite\Dtos\ProjectReportDto;
use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\WordPressKite\Actions\GetWordPressVersionAction;
use Concept7\WordPressKite\Commands\KiteReportCommand;
use Concept7\WordPressKite\Http\WordPressGuzzleSender;
use Concept7\WordPressKite\ProjectInfo\WordPressProjectInfoCollector;
use Saloon\Config;

class WordPressKitePlugin
{
    /**
     * When the last report actually ran, so checkAdvisories() can skip a
     * scan that would otherwise race the advisories that report just sent.
     */
    public const LAST_RAN_AT_TRANSIENT = 'kite_report_last_ran_at';

    public function boot(): void
    {
        Config::$defaultSender = WordPressGuzzleSender::class;

        add_action('init', [$this, 'scheduleCron']);
        add_action('kite_daily_report', [$this, 'cronReport']);
        add_action('kite_check_advisories', [$this, 'cronCheckAdvisories']);

        if (defined('WP_CLI') && WP_CLI) {
            \WP_CLI::add_command('kite report', new KiteReportCommand($this));
        }
    }

    public function scheduleCron(): void
    {
        if (! wp_next_scheduled('kite_daily_report')) {
            wp_schedule_event(time(), 'daily', 'kite_daily_report');
        }

        if (! wp_next_scheduled('kite_check_advisories')) {
            wp_schedule_event(time(), 'hourly', 'kite_check_advisories');
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

        $dto = Kite::make($this->config())
            ->projectInfoCollector(new WordPressProjectInfoCollector)
            ->addActions($actions)
            ->report();

        set_transient(self::LAST_RAN_AT_TRANSIENT, time(), 2 * HOUR_IN_SECONDS);

        return $dto;
    }

    public function checkAdvisories(): void
    {
        if ($this->reportRanTooRecently()) {
            return;
        }

        Kite::make($this->config())
            ->projectInfoCollector(new WordPressProjectInfoCollector)
            ->checkAdvisories();
    }

    /**
     * True when report() ran more recently than KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT,
     * meaning it already submitted a fresh advisory scan for this project.
     */
    public function reportRanTooRecently(): bool
    {
        $lastRanAt = get_transient(self::LAST_RAN_AT_TRANSIENT);
        $minMinutesAfterReport = (int) $this->env('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT', 15);

        return $lastRanAt && $lastRanAt > (time() - $minMinutesAfterReport * 60);
    }

    public function config(): KiteConfig
    {
        return new KiteConfig(
            token: $this->env('KITE_TOKEN'),
            uri: $this->env('KITE_URI') ?: null,
        );
    }

    public function actions(): array
    {
        $actions = [
            GetWordPressVersionAction::class,
        ];

        return apply_filters('kite_actions', $actions);
    }

    protected function env(string $key, string|int|null $default = null): string|int|null
    {
        return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $default);
    }
}
