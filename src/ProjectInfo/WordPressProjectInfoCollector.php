<?php

namespace Concept7\WordPressKite\ProjectInfo;

use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Concept7\Kite\Support\ComposerDependencies;

class WordPressProjectInfoCollector implements ProjectInfoCollectorInterface
{
    public function collect(): array
    {
        return [
            'hostname' => gethostname(),
            'environment' => wp_get_environment_type(),
            'is_debug_mode_on' => defined('WP_DEBUG') && WP_DEBUG,
            'php_version' => phpversion(),
            'url' => get_site_url(),
            'packages' => ComposerDependencies::direct(),
        ];
    }
}
