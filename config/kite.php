<?php

/**
 * Kite Configuration Reference
 *
 * This file documents the expected environment variables for wordpress-kite.
 * In a Bedrock project, define these in your .env file.
 *
 * KITE_URI         - Base URL of the Kite API (e.g. https://kite.example.com)
 * KITE_PROJECT_ID  - Project UUID from the Kite dashboard
 * KITE_PROJECT_KEY - API authentication key for the project
 */

return [
    'uri' => env('KITE_URI', ''),
    'project_id' => env('KITE_PROJECT_ID', ''),
    'project_key' => env('KITE_PROJECT_KEY', ''),

    'actions' => [
        \Concept7\WordPressKite\Actions\GetWordPressVersionAction::class,
        \Concept7\WordPressKite\Actions\GetWooCommerceVersionAction::class,
        \Concept7\WordPressKite\Actions\GetAcfProVersionAction::class,
    ],
];
