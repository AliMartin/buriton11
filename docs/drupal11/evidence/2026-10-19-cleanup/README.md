# Drush and module-cleanup evidence

Captured 19 October 2026 on local `buriton11`. See
[the implementation record](../../2026-10-19-drush-and-module-cleanup.md).

| Files | Purpose |
| --- | --- |
| `before-baseline.json`, `before-extensions.json`, `before-config-status.json` | Fresh PHP 8.4 pre-change state |
| `module-usage-audit.json`, `unused-root-packages.json` | Usage, configuration/dependency checks and removal candidates |
| `backup-summary.json` | Backup scope and gzip integrity, without private paths or database contents |
| `drush13-*`, `php84-support-*`, `installer-manifest.*` | Reviewed Drush/support-library plans and actual Composer results |
| `uninstall-unused.*`, `remove-manifest.*`, `removal-*` | Enabled-module uninstalls and package/code removal |
| `package-changes.json`, `code-removal-check.json` | Exact versions, removed paths and retained Bootstrap5 guard |
| `finish-clone-config.*`, `staged-export.*`, `config-diff.*`, `sync-changes.json` | Obsolete clone-type cleanup and six reviewed configuration-export changes |
| `after-*`, `data-comparison.json` | Final runtime, configuration, content comparisons and Drupal status |
| `verification-commands.json`, `composer-*`, `theme-*`, `libraries.json` | Verification arguments, results and exit codes |
| `remaining-drupal11-blockers.*` | Dated post-cleanup Composer precheck; failure is expected while still on core 10 / Font Awesome 2 |
| `manifest-sha256.json` | Checksums of this evidence and changed source/configuration files |

Companion stderr files preserve tool notices and warnings. Old Drush 12 emitted
PHP 8.4 deprecations before its update; only the initial read-only baseline
commands used a per-command `error_reporting=24575` to keep JSON parseable.
Final verification used normal reporting. Retained Font Awesome/TB Mega Menu
deprecations appear in uninstall/cache-rebuild logs and remain documented work.

The initial attempted inline PHP audit did not run because DDEV's command shell
expanded its variables. It made no site changes; the successful audit used a
CLI-only script instead. Temporary audit/mutation scripts were removed after use.
The private backup-location note remains in ignored `tmp`, outside this evidence.

These are maintenance-step checks on Drupal 10, not a completed Drupal 11 POC.
