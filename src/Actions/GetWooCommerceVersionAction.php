<?php

namespace Concept7\WordPressKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetWooCommerceVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('woocommerce_version', ['wpackagist-plugin/woocommerce']);
    }
}
