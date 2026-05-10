<?php

namespace Concept7\WordPressKite\Commands;

use Concept7\WordPressKite\WordPressKitePlugin;

class KiteCheckAdvisoriesCommand
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
            $this->plugin->checkAdvisories();
            \WP_CLI::success('Advisory check sent successfully.');
        } catch (\Throwable $e) {
            \WP_CLI::error($e->getMessage());
        }
    }
}
