<?php

namespace Concept7\WordPressKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetWordpressKiteVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('wordpress_kite_version', ['concept7/wordpress-kite']);
    }
}
