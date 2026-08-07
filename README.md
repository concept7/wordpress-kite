# WordPress Kite

A WordPress mu-plugin that reports project metadata to the [Kite](https://gitlab.concept7.nl/workflow/kite-backend) monitoring API.

## Installation

Require the package in your Bedrock project:

```bash
composer require concept7/wordpress-kite
```

The package will automatically install as an mu-plugin in `web/app/mu-plugins/wordpress-kite/`.

## Configuration

Add the `KITE_TOKEN` to your `.env` file (generated from the [Kite Dashboard](https://kite-monitor.concept7.dev/)):

```env
KITE_TOKEN=your-kite-token
```

Optionally override the API base URL for development:

```env
KITE_URI=https://kite.test
```

## What gets reported

### Project info

| Field | Description |
|---|---|
| `hostname` | Server hostname |
| `environment` | WordPress environment type (`production`, `staging`, `development`, `local`) |
| `is_debug_mode_on` | Whether `WP_DEBUG` is enabled |
| `url` | Site URL |
| `packages` | Installed Composer, npm, and WordPress packages |

Packages are collected from three sources:

- **Composer** — all installed packages (direct and transitive), each flagged `is_direct` and listing `required_by`
- **npm** — all packages from `package-lock.json`, same `is_direct` / `required_by` treatment
- **WordPress** — all installed plugins and themes

Each package is tagged with its ecosystem (`composer`, `npm`, or `wordpress`) for proper categorization on the dashboard. Before sending, the SDK fetches your project's config from the Kite API and — unless the project is set to share all packages — filters the list down to only the packages Kite is configured to monitor.

### Meta (via pipeline actions)

The core SDK provides default actions for PHP, Node, and MySQL/MariaDB versions. WordPress-specific actions are added on top:

| Action | Meta key | Description |
|---|---|---|
| `GetWordPressVersionAction` | `wordpress_version` | WordPress core version |

Actions for values that can't be determined are automatically skipped.

### Security advisories

Every report also scans the reported Composer and npm packages for known security advisories and submits them alongside the report. A separate hourly `kite_check_advisories` cron hook re-runs this scan on its own — without sending a full report — so newly published advisories surface between daily reports. That hourly check is skipped if a full report already ran recently (see `KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT` below), so the two hooks never submit duplicate scans moments apart.

## WP-CLI

Run a report manually:

```bash
wp kite report
```

## Customizing actions

Add or remove actions using the `kite_actions` filter:

```php
add_filter('kite_actions', function (array $actions) {
    $actions[] = MyCustomAction::class;
    return $actions;
});
```

Custom actions must implement `Concept7\Kite\Contracts\ActionInterface`.

## Scheduling

Two WP-Cron hooks are registered on `init`:

| Hook | Frequency | Does |
|---|---|---|
| `kite_daily_report` | daily | Full report: meta, project info, packages, advisories |
| `kite_check_advisories` | hourly, offset 30 minutes | Advisory scan only, skipped if the daily report ran recently |

The hourly hook is anchored 30 minutes after the daily hook so their ticks never land on the same moment.

Failed runs are logged to `error_log`. The advisory-skip window defaults to 15 minutes and can be overridden:

```env
KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT=15
```

## Development

```bash
composer test     # Run tests
composer format   # Format code
```

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
