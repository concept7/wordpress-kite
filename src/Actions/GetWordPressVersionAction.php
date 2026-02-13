<?php

namespace Concept7\WordPressKite\Actions;

use Closure;
use Concept7\Kite\Contracts\ActionInterface;
use Concept7\Kite\Support\Collection;

class GetWordPressVersionAction implements ActionInterface
{
    public function handle(Collection $data, Closure $next): Collection
    {
        $version = get_bloginfo('version');

        if (filled($version)) {
            $data->push([
                'key' => 'wordpress_version',
                'value' => $version,
            ]);
        }

        return $next($data);
    }
}
