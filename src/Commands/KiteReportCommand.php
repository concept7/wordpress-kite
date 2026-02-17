<?php

namespace Concept7\WordPressKite\Commands;

use Concept7\WordPressKite\WordPressKitePlugin;

class KiteReportCommand
{
    public function __construct(
        private readonly WordPressKitePlugin $plugin,
    ) {}

    public function __invoke(array $args, array $assocArgs): void
    {
        if (! $this->plugin->config()->isValid()) {
            \WP_CLI::error('Project credentials are missing!');

            return;
        }

        try {
            $this->plugin->report();
            \WP_CLI::success('Kite report sent successfully.');
        } catch (\Throwable $e) {
            \WP_CLI::error($e->getMessage());
        }
    }
}
