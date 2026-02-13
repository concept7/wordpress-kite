<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

// Pre-load Brain Monkey hook functions so they're available globally
Brain\Monkey\setUp();

if (! defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (! defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}
