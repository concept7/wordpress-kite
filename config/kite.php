<?php

/**
 * Kite Configuration Reference
 *
 * This file documents the expected environment variables for wordpress-kite.
 * In a Bedrock project, define these in your .env file.
 *
 * KITE_TOKEN - API authentication token for the project
 * KITE_URI   - Optional: override the Kite API base URL (for development)
 * KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT - Optional: skip the hourly advisory
 *   check if the daily report ran more recently than this many minutes ago,
 *   so the two cron hooks never submit two independent advisory scans for the
 *   same project moments apart (default: 15)
 */

use Concept7\WordPressKite\Actions\GetWordPressVersionAction;

return [
    'token' => env('KITE_TOKEN', ''),
    'uri' => env('KITE_URI', ''),

    'advisories_min_minutes_after_report' => env('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT', 15),

    'actions' => [
        GetWordPressVersionAction::class,
    ],
];
