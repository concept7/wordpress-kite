<?php

namespace Concept7\WordPressKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetAcfProVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('acf_pro_version', ['wpackagist-plugin/advanced-custom-fields-pro']);
    }
}
