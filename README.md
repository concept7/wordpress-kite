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
| `php_version` | PHP version |
| `url` | Site URL |
| `packages` | Installed Composer, npm, and WordPress packages |

Packages are collected from three sources:

- **Composer** — direct dependencies from `composer.json`
- **npm** — installed packages from `package-lock.json`
- **WordPress** — all installed plugins and themes

Each package is tagged with its ecosystem (`composer`, `npm`, or `wordpress`) for proper categorization on the dashboard.

### Meta (via pipeline actions)

The core SDK provides default actions for PHP, MySQL/MariaDB, Tailwind CSS, and Kite SDK versions. WordPress-specific actions are added on top:

| Action | Meta key | Description |
|---|---|---|
| `GetWordPressVersionAction` | `wordpress_version` | WordPress core version |
| `GetWooCommerceVersionAction` | `woocommerce_version` | WooCommerce version (if installed) |
| `GetAcfProVersionAction` | `acf_pro_version` | ACF Pro version (if installed) |
| `GetWordpressKiteVersionAction` | `wordpress_kite_version` | WordPress Kite package version |

Actions for packages that aren't installed are automatically skipped.

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

Reports are sent automatically once daily via WP-Cron. The cron event `kite_daily_report` is registered on the `init` hook. Failed reports are logged to `error_log`.

## Development

```bash
composer test     # Run tests
composer format   # Format code
```

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
