<?php

/**
 * Kite Configuration Reference
 *
 * This file documents the expected environment variables for wordpress-kite.
 * In a Bedrock project, define these in your .env file.
 *
 * KITE_TOKEN - API authentication token for the project
 * KITE_URI   - Optional: override the Kite API base URL (for development)
 */

use Concept7\WordPressKite\Actions\GetWordPressVersionAction;

return [
    'token' => env('KITE_TOKEN', ''),
    'uri' => env('KITE_URI', ''),

    'actions' => [
        GetWordPressVersionAction::class,
    ],
];
