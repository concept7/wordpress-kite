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

if (! defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (! defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}
