# Gemini Code Assistant Context

## Project Overview

**concept7/wordpress-kite** is a WordPress mu-plugin that reports project metadata (PHP version, WordPress version, installed plugins, Composer packages) to the Kite monitoring API on a daily schedule via WP-Cron.

This is part of a three-project ecosystem in the `Monitor/` directory.

## Project Ecosystem

- **kite-backend** (`../kite-backend`): Laravel 12 + Filament admin panel that receives and displays project data. Projects authenticate via Sanctum API tokens. API endpoint: `POST /api/project/{project:uuid}`.
- **concept7/kite** (`../kite`): Framework-agnostic core PHP package containing the pipeline, actions, HTTP client, and config. Linked via Composer path repository.
- **concept7/wordpress-kite** (this repo): WordPress adapter adding WP-specific actions, plugin info collection, and WP-Cron scheduling.

## Key Technologies

-   **Language:** PHP 8.2+
-   **Framework:** WordPress
-   **Dependency Management:** Composer
-   **Testing:** Pest (a testing framework for PHP) with Mockery and Brain/Monkey.
-   **Code Styling:** Laravel Pint

## Commands

```bash
composer test              # Run tests (Pest v4 + Brain Monkey)
composer test -- --filter=TestName  # Run a single test
composer format            # Format code (Laravel Pint)
wp kite report             # Trigger a report manually
```

## Architecture

### Two-Package Design

- **concept7/kite** (core): Framework-agnostic pipeline, actions, HTTP client, config, Composer dependency collection.
- **concept7/wordpress-kite** (this repo): Mu-plugin entry point, WP-Cron scheduling, WordPress-ecosystem actions, project info collector, WP-CLI command.

### Reporting Flow

`wordpress-kite.php` boots `WordPressKitePlugin` → registers `init` hook for cron scheduling + `kite_daily_report` hook for reporting → `report()` builds `KiteConfig` from env vars → creates `Kite::make($config)` → adds WordPress-specific actions → runs pipeline → filters empty values → collects project info via `WordPressProjectInfoCollector` → POSTs to Kite API with bearer token auth → returns `ReportResult`.

### Key Patterns

- **Pipeline pattern**: Actions implement `ActionInterface` (`handle(Collection $data, Closure $next)`), chained via the core Pipeline.
- **Base action classes** in core for reuse: `GetComposerPackageVersionAction` (checks `Composer\InstalledVersions`). WordPress-specific actions extend these.
- **WP-Cron scheduling**: Uses `wp_schedule_event('daily')` with idempotent scheduling check.
- **Filter extensibility**: `apply_filters('kite_actions', $actions)` allows themes/plugins to add custom actions.
- **Value objects**: `KiteConfig` (token, uri — immutable with validation), `ReportResult` (static constructors `success()`/`failure()`).
- **Fluent API**: `Kite::make($config)->projectInfoCollector(...)->addAction(...)->report()`.

### Key Files

- `wordpress-kite.php` — Mu-plugin entry point (plugin headers + boot)
- `src/WordPressKitePlugin.php` — Central orchestrator (hooks, config, reporting)
- `src/Cli/KiteReportCommand.php` — WP-CLI: `wp kite report`
- `src/ProjectInfo/WordPressProjectInfoCollector.php` — Collects WP environment info, Composer packages, and plugin list
- `src/Actions/` — WordPress-ecosystem actions (WordPress, WooCommerce, ACF Pro)
- `config/kite.php` — Reference config (documentation only, not loaded at runtime)

## Environment Variables

The plugin is configured using environment variables in the `.env` file:

- `KITE_TOKEN` — API authentication token (required)
- `KITE_URI` — Optional: override the Kite API base URL (for development)

## Conventions

- PHP 8.2+ with readonly properties and named arguments
- Package type `wordpress-muplugin` for Bedrock auto-installation via composer/installers
- Missing packages/tools are silently skipped (no exceptions)
- Namespace: `Concept7\WordPressKite` (this package), `Concept7\Kite` (core package)
- **Testing**: Uses Pest v4 with Brain Monkey for WordPress function mocking. Tests use `Brain\Monkey\setUp()` / `tearDown()` via Pest's `beforeEach` / `afterEach`.
