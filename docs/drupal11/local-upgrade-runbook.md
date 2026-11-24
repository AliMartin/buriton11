# Local Drupal 10 → 11 upgrade runbook

Prepared 18 October 2026. **Executed locally on 19 October: Drupal 11.4.10.**
See [the completed core-upgrade record](2026-10-19-core-upgrade.md) for actual
results, migrations, the private checkpoint and remaining acceptance. The
technical POC was closed on 19 October; start with [the final handover](poc-handover.md).
The steps below are retained for replay against a fresh production copy. Commands
are for the isolated `buriton11` copy, from the repository root. Follow each
stage and review its result before continuing. Resolve the decisions below
before treating the example package commands as the final plan.

19 October update: Git exclusions, PHP 8.4 and Drush 13 preparation are complete.
The unused legacy packages have been removed; Tour, Image CAPTCHA and Image
Field to Media are uninstalled, and the 536 remaining configuration exports
match the database. See [the implementation record](2026-10-19-drush-and-module-cleanup.md).
The preparation examples below describe an earlier/fresh baseline; do not rerun
package removals against the cleaned copy. Font Awesome 3.0.0 and Similar By
Terms 2.0.0 are now installed; the remaining contributed set and TB Mega Menu
patch are recorded in [the module update](2026-10-19-contributed-module-updates.md).
Theme/frontend preparation is also complete: see [the theme record](2026-10-19-theme-updates.md)
for the declarations, Bootstrap/Slick changes, managed Cycle/TB patches and
jQuery 3/4 regression commands. The subsequent core switch is complete.
Full browser acceptance and production operational prerequisites remain pending.

The patched TB Mega Menu and Bootstrap5 projects subsequently moved to custom
installation paths. Retain their explicit Composer rules and follow the
[one-time relocation instructions](2026-10-19-custom-paths.md) for an existing
checkout; a clean build installs directly to the new paths.

The intended scope is to preserve Zircon's appearance, content and administration
workflow, retain Buriton5 as the existing alternative, and avoid a document-root
move during the core upgrade. Production replay starts with the historical
[Drupal 9 → 10 runbook](../feasibility-study/02-replay-runbook.md).

## 1. Establish a recoverable, isolated starting point

1. Confirm `ddev describe` identifies **buriton11**, not buriton10 or production.
   Check the database/bootstrap with `ddev drush status`. Wait for Mutagen and
   the copy operation to settle. Record `git status --short` without staging.
2. Verify the root `.gitignore` against the actual fresh-copy layout. The POC
   corrected its original omissions; retain those rules. Exclude credentials, public/private uploads, database dumps,
   `.ddev/upgrade-study-archive`, temporary data, FTP/SFTP settings and generated
   dependencies. Review `git check-ignore` against the examples in
   [the recorded check](evidence/git-ignore-check.json). Check actual staged
   paths with `git diff --cached --name-only` before committing anything.
3. Preserve Composer manifests/locks, patch files/lock, full custom themes,
   reviewed configuration, `tools/`, docs and deliberately selected DDEV source
   configuration in version control. Downloaded core/contrib/vendor/library
   code should rebuild from manifests, locks and patches. Do not use `git add .`
   against the current exclusion rules.
4. Close direct web access to private storage. Prefer a path outside the web
   root, updating only local settings and the matching Backup and Migrate
   destination configuration. Alternatively add explicit nginx denial of
   `/private-files` and `/private-files/` before retesting. Apache `.htaccess`
   is insufficient in DDEV nginx. Check an **existing** private file with HEAD
   and require 403/404; separately prove authorised `/system/files/...` access
   and anonymous denial where the application uses private downloads.
5. Verify local mail goes to Mailpit. Review Rules, Webform handlers, remote
   backup destinations and external integrations before running submissions or
   cron. Suppress automatic local cron/feed refresh during baseline comparisons;
   a local-only settings override of
   `$config['automated_cron.settings']['interval'] = 0;` can do this. Keep the
   override inside the DDEV/local condition and remove it deliberately when
   controlled cron testing begins. Avoid real external notifications in the POC.
6. Back up matching database, source/config/settings, public files and private
   files to **private storage outside the document root**, plus a durable
   off-machine copy. DDEV snapshots can be an additional convenience, not the
   only backup. Exercise restoration before destructive work and record paths
   privately. A database-only backup is not a complete rollback.

Example backup command; replace the host path first:

```sh
ddev export-db --file=/ABSOLUTE/PRIVATE/BACKUP/buriton11-before-d11.sql.gz
```

`export-db --file` uses a **host** path. Paths passed through `ddev exec` refer
to the container, where this project's root is `/var/www/html`.

Capture the baseline privately and retain reviewed summaries in evidence:

```sh
ddev drush php:script docs/drupal11/check-baseline.php
ddev drush config:status --format=json
ddev drush updatedb:status
ddev composer validate --no-check-publish
ddev composer check-platform-reqs
ddev composer audit
```

The baseline collector reads configuration and hashes selected ordered database
fields. It does not export settings, account passwords or raw content. Freeze
feed changes before recording the before/after files; use new filenames so the
pre-upgrade evidence remains intact.

## 2. Raise PHP while still on Drupal 10

Keep MariaDB 11.8 and the root document layout. Test PHP 8.4 before replacing
core so PHP problems can be separated from the major Drupal upgrade.

```sh
ddev config --php-version=8.4
ddev restart
ddev exec php --version
ddev composer --version
ddev composer check-platform-reqs
ddev drush status
```

Check all target extensions, including zlib, and public/admin rendering. If this
fails, resolve it on Drupal 10 or restore the matching checkpoint; do not add
`--ignore-platform-reqs` or falsify `config.platform.php` to pass the solver.

## 3. Prepare packages and removed modules on Drupal 10

Recheck module/theme state against the fresh copy before removing code.

Recommended baseline decisions:

- Retain Font Awesome and upgrade it to 3.0; retain Image Field to Media and
  upgrade it to 3.0.2. Do not run its content-conversion operation.
- Remove downloaded-only Adminimal packages and the unused CKEditor 4 iframe
  chain. These are not enabled in the appraisal baseline. Do not uninstall a
  feature merely because it is inconvenient if it is enabled on a newer copy.
- Review iframe-containing content and editor requirements. If an iframe toolbar
  is needed, use `ckeditor_iframe:^3.1` instead of removing the package, configure
  its CKEditor 5 integration, and test save/reopen. Do not retain CKEditor 4.
- Uninstall Tour on Drupal 10 if unused. If it is required, add/test contributed
  Tour before the core switch instead. Keep core `phpass` enabled.
- Keep Aggregator, Classy, Stable, Bootstrap5 and the existing Bootstrap5 patch.
  Review the patch against any changed parent release; never silently drop it.
- Review `composer.libraries.json` and remove the explicit CKEditor 4 iframe
  library requirement only if its consumers have been retired. Preserve the
  Webform catalogue, hoverIntent, FitVids and slideshow libraries.

Example manifest preparation for the recommended unused-package cleanup:

```sh
ddev composer remove drupal/adminimal_admin_toolbar drupal/adminimal_theme drupal/ckeditor_iframe --no-update
ddev composer require 'drupal/fontawesome:^3.0' 'drupal/image_field_to_media:^3.0.2' 'drush/drush:^13.7' --no-update
ddev composer update --with-all-dependencies --dry-run --no-interaction
```

This prepares **Drupal 10** requirements first. Read the proposed operations:
unused CKEditor/Seven/FakeObjects may disappear as transitives, while required
data-bearing modules and base themes must remain. Reject unexplained downgrades,
new unstable versions or unrelated removals. This dry run has not been performed
as part of the appraisal. When reviewed, install that plan on the local copy:

```sh
ddev composer update --with-all-dependencies --no-interaction
ddev drush updatedb --yes
ddev drush cache:rebuild
```

Run `ddev drush pm:uninstall tour --yes` **only after choosing Tour retirement**.
For a contributed replacement, keep its existing configuration and follow its
upgrade path instead. Never uninstall Aggregator, core `phpass` or required
base themes as generic cleanup. Record the package diff and all update output.

## 4. Resolve frontend compatibility before the core switch

**Completed in this checkout on 19 October:** use the sources and checks in
[the theme implementation record](2026-10-19-theme-updates.md). The checklist
below remains the replay/review gate for a fresh production copy; do not
reapply the Slick patch to already patched custom-theme sources.

1. Repair/replace the loaded Bootstrap 3 JavaScript's jQuery 4 incompatibility
   through a maintainable source change or Composer-managed patch. Keep the
   appearance if that is the agreed scope. Retest tooltip/popover/collapse/menu
   behavior; simply deleting Bootstrap's version guard is insufficient.
2. Exercise Smooth Scroll, Slick, FlexSlider, Views Slideshow/Cycle, TB Mega Menu
   and Webform widgets with jQuery 4. A successful PHP scan does not cover them.
3. Make a Drupal 11 regression fixture load
   `core/assets/vendor/jquery/jquery.min.js` from the target installation and
   print/assert its version. The inherited Zircon fixture's npm jQuery 3.7.1
   must not be used as evidence of target compatibility. Keep its repeat-attach,
   AJAX, slideshow-content, search-placeholder and form-preservation assertions.
4. Rebuild Buriton5's CSS if its Bootstrap5 parent or Sass inputs change, following
   its README. Preserve its parent CSS override and both themes' 3/6/3 layout.
5. Add Drupal 11 to each custom theme's `core_version_requirement` as part of
   this change; retain the explicit base-theme dependencies. Commit the actual
   sources, compiled assets and any maintained patches together after review.
6. Rerun Upgrade Status and triage results. Keep manual findings and explanations
   visible. Do not broadly suppress failures or patch contributed metadata just
   to force installation where a supported release exists.

Retain a **prepared Drupal 10 checkpoint** with its own lock, database and files
before the next stage. This is the last recoverable intermediate state and is
also the bridge required when replaying from live Drupal 9.

## 5. Resolve the Drupal 11 set, then migrate locally

Reconfirm the supported stable release. POC close-out widened all four root
constraints to `^11.4.10` (compatible stable 11.x releases, below 12), while the
final tested lock remains 11.4.10. The original core-switch record used the
narrower `~11.4.10`; that dated observation is historical. For a fresh rehearsal,
review the whole selected plan and record the exact resulting version. Do not
assume the caret range tests future minor versions or that `composer install`
will select a newer release than the lock.

```sh
ddev composer config allow-plugins.symfony/runtime true
ddev composer require 'drupal/core-recommended:^11.4.10' 'drupal/core-composer-scaffold:^11.4.10' 'drupal/core-project-message:^11.4.10' 'drupal/core-vendor-hardening:^11.4.10' --no-update
ddev composer update --with-all-dependencies --dry-run --no-interaction
```

Inspect the whole coordinated plan. Use `composer prohibits drupal/core VERSION
--tree` for diagnostics, substituting the actual target. Do not delete the lock
or bypass security blocking/platform checks to hide a conflict. Revisit the
specific constraint or package instead. Do not add a direct root requirement
for `drupal/core`; this project uses core-recommended.

After the plan is resolved and the prepared checkpoint is verified:

```sh
ddev drush state:set system.maintenance_mode 1 --input-format=integer
ddev drush cache:rebuild
ddev composer update --with-all-dependencies --no-interaction
ddev drush updatedb:status
ddev drush updatedb --yes
ddev drush cache:rebuild
ddev config --project-type=drupal11
ddev restart
```

Review regenerated scaffold files and the allowed Symfony runtime plugin. Verify
root `index.php`/`update.php` resolve the generated runtime autoloader. Ensure web
access rules survived DDEV regeneration/scaffolding and that private storage is
still protected. Verify no missing extension requirements and no pending updates.

Do not run `drush config:import` against a stale export. Review active-versus-file
differences, including module removals and text formats. Stage a fresh export
privately, inspect it for environment-specific settings/handlers/secrets, and
merge the intended changes into the project's sync set. Protect the export path.

Leave maintenance mode after bootstrap and basic checks succeed:

```sh
ddev drush state:set system.maintenance_mode 0 --input-format=integer
ddev drush cache:rebuild
```

## 6. Validate and record the result

Run the [acceptance sheet](acceptance-and-handover.md), not just a homepage test.
Use the existing PHP checks with the **new URL** explicitly set:

```sh
ddev exec env THEME_QA_THEME=zircon THEME_QA_BASE_URL=https://buriton11.ddev.site vendor/bin/drush php:script tools/check-theme.php
ddev exec env THEME_QA_THEME=buriton5 THEME_QA_BASE_URL=https://buriton11.ddev.site vendor/bin/drush php:script tools/check-theme.php
ddev exec env THEME_QA_THEME=claro THEME_QA_BASE_URL=https://buriton11.ddev.site vendor/bin/drush php:script tools/check-theme.php
ddev drush php:script tools/check-libraries.php
ddev drush webform:libraries:status
ddev drush php:script docs/drupal11/check-baseline.php
ddev drush updatedb:status
ddev drush core:requirements
ddev drush config:status --format=json
ddev composer validate --no-check-publish
ddev composer check-platform-reqs
ddev composer audit
```

Compare counts/fingerprints before deliberate feed refresh or editing. Explain
intentional differences and investigate others. Recount legacy-hash accounts
and confirm their passwords remain usable; one-time login links do not test password compatibility.
Then test controlled cron/feed fetching and local form delivery. Restore intended
local cron settings only after those checks are safe. Record browser console,
network and visual results on desktop/mobile as well as automated results.

## 7. Rollback if a stage fails

Stop writes and local cron. Restore the matching **code/manifests/locks/patches,
database, settings, PHP configuration and both file trees** from the checkpoint
before the failed stage. Rebuild dependencies with that checkpoint's lock,
restore its database using `ddev import-db --file=/PRIVATE/HOST/PATH.sql.gz`,
restart as needed and rebuild caches. Verify baseline counts, private access,
login and representative pages before reopening the copy.

Restoring old code over an already-updated database is not a rollback. A DDEV
database snapshot alone does not restore uploaded files or code. Record a
successful restore exercise in the handover rather than assuming backups work.

## Production replay distinction

After the local POC passes, replay **9 → prepared 10 → 11** against a fresh live
snapshot using both intermediate locks and database-update stages. Use tested
lock files and built artifacts (`composer install`), not a fresh dependency
resolution during the live window. Replace the inherited blanket cPanel copy
task, verify production runtime/storage/mail/cron and web rules, define the
content freeze and complete a restore rehearsal. The production release needs
its own reviewed cutover plan; these local commands are not that deployment.
