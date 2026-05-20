<?php

namespace Concept7\WordPressKite\ProjectInfo;

use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Concept7\Kite\Support\ComposerDependencies;
use Concept7\Kite\Support\NpmDependencies;
use Concept7\WordPressKite\Support\WordPressPackages;

class WordPressProjectInfoCollector implements ProjectInfoCollectorInterface
{
    public function collect(): array
    {
        $basePath = $this->findBasePath();

        return [
            'hostname' => gethostname(),
            'environment' => wp_get_environment_type(),
            'is_debug_mode_on' => defined('WP_DEBUG') && WP_DEBUG,
            'url' => get_site_url(),
            'packages' => array_merge(
                ComposerDependencies::all($basePath),
                NpmDependencies::installed($basePath),
                WordPressPackages::installed(),
            ),
        ];
    }

    private function findBasePath(): string
    {
        $path = rtrim(ABSPATH, '/');

        while ($path !== dirname($path)) {
            if (file_exists($path.'/composer.json')) {
                return $path;
            }

            $path = dirname($path);
        }

        return getcwd();
    }
}
