# WordPress Kite

A WordPress mu-plugin that reports project metadata to the [Kite](https://gitlab.concept7.nl/workflow/kite-backend) monitoring API.

## Installation

Require the package in your Bedrock project:

```bash
composer require concept7/wordpress-kite
```

The package will automatically install as an mu-plugin in `web/app/mu-plugins/wordpress-kite/`.

## Configuration

Add the following environment variables to your `.env` file:

```env
KITE_URI=https://kite.example.com
KITE_PROJECT_ID=your-project-uuid
KITE_PROJECT_KEY=your-api-key
```

## What Gets Reported

### Project Info

- Hostname
- Environment type (`production`, `staging`, `development`, `local`)
- Debug mode status
- PHP version
- Site URL
- Installed Composer packages (name + version)

### Meta (via pipeline actions)

- WordPress version
- WooCommerce version (if installed)
- ACF Pro version (if installed)

## WP-CLI

Run a report manually:

```bash
wp kite report
```

## Customizing Actions

Add or remove actions using the `kite_actions` filter:

```php
add_filter('kite_actions', function (array $actions) {
    $actions[] = MyCustomAction::class;
    return $actions;
});
```

Custom actions must implement `Concept7\Kite\Contracts\ActionInterface`.

## Scheduling

Reports are sent automatically once daily via WP-Cron. The cron event `kite_daily_report` is registered on the `init` hook.

## Development

```bash
composer test     # Run tests
composer format   # Format code
```

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
