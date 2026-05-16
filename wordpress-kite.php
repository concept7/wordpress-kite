<?php

/**
 * Plugin Name: WordPress Kite
 * Plugin URI: https://gitlab.concept7.nl/workflow/wordpress-kite
 * Description: Reports project metadata to the Kite monitoring API.
 * Version: 1.0.0
 * Author: Concept7
 * Author URI: https://concept7.nl
 * License: MIT
 */

use Concept7\WordPressKite\WordPressKitePlugin;

if (! defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require_once __DIR__.'/vendor/autoload.php';
}

$wordpressKitePlugin = new WordPressKitePlugin;
$wordpressKitePlugin->boot();
