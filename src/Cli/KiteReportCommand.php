<?php

namespace Concept7\WordPressKite\Cli;

use Concept7\WordPressKite\WordPressKitePlugin;

class KiteReportCommand
{
    public function __construct(
        private readonly WordPressKitePlugin $plugin,
    ) {}

    public function __invoke(array $args, array $assocArgs): void
    {
        $config = $this->plugin->config();

        if (! $config->isValid()) {
            \WP_CLI::error('Project credentials are missing!');

            return;
        }

        $result = $this->plugin->report();

        if ($result === null) {
            \WP_CLI::error('Project credentials are missing!');

            return;
        }

        if (! $result->success) {
            \WP_CLI::error($result->message);

            return;
        }

        \WP_CLI::success('Kite report sent successfully.');
    }
}
