# Drupal 11 technical appraisal

**Current implementation: 19 October 2026.** The local site now runs Drupal
11.4.10 successfully. Read [the completed upgrade record](2026-10-19-core-upgrade.md)
for the verified result and remaining acceptance. The dated appraisal below
retains the original baseline and decisions, not the current runtime inventory.

Assessment date: **18 October 2026**. Scope: the copied `buriton11` project and
its running local DDEV database. Production hosting and the current live
database have not been inspected.

**19 October follow-up:** root Git exclusions are corrected; the owner restored
`sync` and upgraded PHP to 8.4.18. Drush is now 13.8.0. The unused legacy packages
have been removed, and Tour, Image CAPTCHA and Image Field to Media uninstalled.
See [the completed cleanup record](2026-10-19-drush-and-module-cleanup.md) for
exact changes and passing checks. Source files still need a reviewed commit.
The findings below retain the 18 October baseline unless explicitly qualified;
in particular, the old package table is an appraisal, not the current inventory.

**Later module update:** Font Awesome is now 3.0.0 and Similar By Terms 2.0.0.
The remaining contributed releases are current, and TB Mega Menu has a managed
PHP 8.4 patch. Real Drupal 10 regression checks and an isolated Composer solve
against Drupal 11.4.10 pass. See [the completed update record](2026-10-19-contributed-module-updates.md).
**Later theme update:** both custom themes now declare Drupal 11 support.
Zircon's Bootstrap JavaScript and Slick checks, plus managed Cycle/TB Mega Menu
patches, pass with jQuery 3.7.1 and the actual target jQuery 4.0.0 asset. Fresh
custom-theme scans have no findings. See [the completed theme record](2026-10-19-theme-updates.md).
The frontend findings below describe the original baseline and are superseded
by that record. The core switch, full browser acceptance and operational
prerequisites remain outstanding.

## Assessment

Proceed with a controlled local proof of concept. This is an in-place upgrade
of an established Composer-managed site, with no evidence that content needs
rebuilding or migrating to a new installation. The successful Drupal 9 → 10
work supplies the required intermediate stage.

The main remaining work is package cleanup/upgrades, PHP/Drush alignment,
Tour removal or replacement, and frontend JavaScript compatibility. **Changing
the two theme version declarations alone will not make the site work on the
selected Drupal 11 target.** There is a confirmed Bootstrap/jQuery conflict.
Recovery, Git exclusions and web protection also need attention before the
implementation starts. These have not been repaired as part of this appraisal.

Use **Drupal 11.4.10** as the dated proof-of-concept target. It is an official
security release; recheck the supported patch release when implementation
starts. [Release notes](https://www.drupal.org/project/drupal/releases/11.4.10).

## Verified starting point

| Area | Observed in this copy |
| --- | --- |
| Drupal / Drush | 10.6.19 / 12.5.3 |
| Local environment | DDEV 1.25.1, `buriton11`, nginx-fpm, root document directory, Mutagen |
| PHP / database | PHP 8.2.30 / MariaDB 11.8.6 |
| URL | `https://buriton11.ddev.site` |
| Default / admin themes | Custom Zircon / core Claro |
| Other installed themes | Buriton5, Bootstrap5 4.0.8, Classy 2.0.3, Stable 2.1.0, core Stable9 |
| Custom backend code | No `modules/custom` directory found; two custom themes carry the site-specific code |
| Configuration | 540 active records, 540 sync records; final `config:status` returned `[]` |
| Sync / private paths | `/var/www/html/sync` / `/var/www/html/private-files` in DDEV |
| Pending database updates | None |
| Composer validation / platform / audit | Passed; audit returned no known advisories or abandoned packages |

The sync directory initially appeared empty during inspection, then populated;
the owner subsequently confirmed deleting and restoring it. The later successful
comparison is the recorded baseline. The previous study's five configuration differences are historical,
not the current state. Confirm sync has settled before future comparisons.

| Database records | Count |
| --- | ---: |
| Nodes | 1,880 |
| User table records, including the anonymous record | 6 |
| Managed files | 2,018 |
| Taxonomy terms | 122 |
| Media | 891 |
| Webform submissions | 443 |
| Aggregator feeds / items | 2 / 2,315 |
| Accounts with legacy `$S$` password hashes | 5 |

The previous study recorded 2,314 feed items. The extra item has not been
attributed; use the fresh baseline rather than assuming a migration lost or
duplicated content. Freeze local feed refresh during migration comparisons.
Counts do not verify uploaded file bytes or every content field. The supplied
baseline script also fingerprints selected identity, file and feed fields;
its recorded field lists define exactly what those hashes cover.

At the end of the appraisal, counts, selected-field fingerprints, enabled
modules/themes, editors and configuration counts were unchanged, but the
legacy-password count was **4**, down from the initial 5. No password login or
password-changing command was run by this appraisal, and the cause was not
established. Retain both snapshots and recheck before migration; core `phpass`
is still required. See `evidence/appraisal-preservation-check.json`.

The site has seven content types (`event`, `files`, `news`, `page`,
`parish_council`, `quickevent`, `webform`), media, document/report listings,
RSS feeds and council/village-hall forms. Four configured editor integrations
use CKEditor 5. Webforms are `contact`, `contact_buriton_village_hall` and
`test_webform`; a `rules.reaction.new_comment` configuration is present.
These are specific regression-test targets, not just a generic homepage check.

## Platform and core changes

PHP 8.2 blocks Drupal 11.4.10. Prefer **PHP 8.4** for this rehearsal, first
testing it on Drupal 10.6.19; PHP 8.3 is the target's minimum. Both Drupal
versions support PHP 8.4. [PHP requirements](https://www.drupal.org/docs/getting-started/system-requirements/php-requirements).

Local MariaDB already exceeds Drupal 11's MariaDB 10.6 minimum. Production
must independently satisfy that or MySQL 8.0+, including InnoDB/PDO support.
[Database requirements](https://www.drupal.org/docs/getting-started/system-requirements/database-server-requirements).

Use Composer 2.7+ (local platform output reports Composer plugin API 2.9.0),
and verify all required extensions, especially Drupal 11's `ext-zlib`, after
the PHP change. [Composer requirements](https://www.drupal.org/docs/getting-started/system-requirements/composer-requirements).

Drush 13 is a suitable bridge across Drupal 10 and 11 on PHP 8.3+; 13.7.3
metadata was checked. Replace the root `^12.5` constraint. Let core-recommended
coordinate Symfony/Twig and other transitive versions rather than pinning
individual Symfony packages. [Drush compatibility](https://www.drush.org/13.x/install/).

Tour is enabled and removed from core 11. Decide whether to preserve it through
contributed Tour or uninstall it while still on Drupal 10. The other removed
core modules Actions UI (`action`), Tracker, Book, Forum and Statistics are not
enabled in this baseline. Drupal 10.6.19 satisfies the minimum upgrade source
version of 10.3. [Upgrade guide](https://www.drupal.org/docs/upgrading-drupal/upgrading-drupal/how-to-upgrade-from-drupal-10-to-drupal-11).

**Keep core Password Compatibility (`phpass`) enabled.** It is present in the
11.4.10 source, and legacy-hash accounts still need password verification.
Do not install the unrelated contributed `drupal/phpass` project. Test an
existing password, its rehash on login and the reset flow on the isolated copy.
[Password Compatibility](https://www.drupal.org/docs/develop/core-modules-and-themes/core-modules/password-compatibility-module).

Drupal 11.4 adds the `symfony/runtime` Composer plugin and runtime bootstrap
scaffolding. Allow that specific plugin and verify generated root entry points
and `vendor/autoload_runtime.php`. Preserve required custom web rules when
scaffolding changes. The project must retain `web-root: ./`; tutorials using
`web/` require adaptation. This is covered by the [upgrade guide](https://www.drupal.org/docs/upgrading-drupal/upgrading-drupal/how-to-upgrade-from-drupal-10-to-drupal-11).

## Dependency decisions

The current `composer prohibits` report identifies **locked versions**, not
the absence of compatible releases. Full locked versions/constraints are in
[locked-packages.json](evidence/locked-packages.json).

| Package or feature | Actual use / locked state | Proposed treatment before core 11 |
| --- | --- | --- |
| Four root core packages | All constrained to `^10.6.19` | Move core-recommended, scaffold, project-message and vendor-hardening together |
| Font Awesome | Enabled, 2.26.0; blocks 11 and calls a removed API | Upgrade to 3.0.0; test icons/fields and commands; do not just relax metadata |
| Image Field to Media | Enabled, 2.0.2; blocks 11 | Prefer 3.0.2 to preserve availability; do not rerun image-to-media conversions as an upgrade step |
| CKEditor iframe / CKEditor 4 / FakeObjects | Downloaded, not enabled; iframe 2.2.0 pulls CKEditor 4 | Remove unused root iframe dependency and let Composer remove unused transitives; inspect embedded content first |
| Optional CKEditor iframe feature | Current CKEditor 5 toolbars have no iframe button | If editors need it, use/test 3.1.2 (`^10.5 || ^11.2`) instead of retaining the 2.x chain |
| Adminimal Toolbar / Adminimal / Seven | Downloaded, not enabled or installed; Claro is active | Remove the two direct Adminimal requirements; unused Seven should leave with dependency resolution |
| Aggregator | Enabled contributed 2.2.6; declares 11 support | Retain module/data and validate refresh/output; never uninstall it to solve a dependency issue |
| Bootstrap5 / Classy / Stable | Installed, declare 11 support | Retain both custom-theme dependency chains and the Bootstrap5 post-update guard |
| TB Mega Menu | Enabled 1.10.0, declares 11 support | Retain initially; verify admin save and responsive menu. Scanner's deprecated-method hit is guarded by `method_exists()` |
| Webform | Enabled 6.3.1, declares 11 support | Retain initially; test the actual forms, stored submissions, handlers and widget libraries |
| Entity Clone | Enabled 2.2.0-beta1 | Retain only as a deliberate beta dependency; exercise cloning and record acceptance |
| Rules / Typed Data | Enabled 4.0.1 / 2.1.1 | Preserve versions or upgrade deliberately; test the comment rule without sending real mail |
| Remaining packages | Locked core constraints generally include 11 | Keep review focused; metadata is not runtime proof |

Published compatible branches were verified on [Font Awesome](https://www.drupal.org/project/fontawesome),
[Image Field to Media](https://www.drupal.org/project/image_field_to_media) and
[CKEditor iframe](https://www.drupal.org/project/ckeditor_iframe), with matching
Composer metadata saved in evidence. Adminimal metadata still excludes 11.

Other enabled contributed features include Admin Toolbar, Backup and Migrate,
CAPTCHA/Image CAPTCHA/reCAPTCHA, CTools, FitVids, Honeypot, Pathauto, Redirect,
Similar Terms, Token and Views Slideshow/Cycle. Google Analytics, HTTPSWWW and
Taxonomy Menu are downloaded but not enabled. Zircon nevertheless hardcodes
analytics and the UserWay widget in its HTML template; module status alone
does not establish whether those external services run.

Root `minimum-stability: dev` and `prefer-stable: true` permit unstable
dependencies; stable preference is not a ban on beta/dev packages. Review the
solver plan and preserve the tested lock. Keep both merged library catalogues,
hoverIntent and the patch lock; removing the old iframe chain should include a
usage review of its explicit `composer.libraries.json` library requirement.

## Themes: original findings and completed remediation

**19 October implementation:** the declaration and Bootstrap/Slick/Cycle/TB
JavaScript issues described here have been repaired and regression checked.
Read [the current theme record](2026-10-19-theme-updates.md) for the final assets,
patches and test limits. This section retains the evidence that motivated them.

Zircon → Classy → Stable preserves the current public layout. Buriton5 →
Bootstrap5 → Stable9 is the installed alternative. Claro remains administration.
Both custom themes passed fresh Drupal 10 Twig/render/asset checks, with no
missing resolved assets or warnings; their 3/6/3 layout checks passed.

Fresh Upgrade Status scans found **one version-declaration warning per custom
theme**, with no other reported findings. Add `^11` only alongside the actual
compatibility work and validation. The PHP scanner does not establish that
JavaScript libraries support the target runtime.

**Confirmed blocker:** the tagged 11.4.10 `core.libraries.yml` supplies jQuery
**4.0.0**. Zircon loads Bootstrap **3.4.1**, whose preamble rejects jQuery 4
before registering its plugins. Executing that actual preamble with version-only
stubs passed for 3.7.1 and threw for 4.0.0. See
[the guard evidence](evidence/bootstrap-jquery-guard.json) and
[target library source](evidence/target-core.libraries.yml).

Preserving the visual design therefore still requires a maintained solution
for Bootstrap JavaScript and testing Smooth Scroll, FlexSlider, Slick, menu,
slideshow and Webform plugins on **the actual core jQuery**. Investigate a
reviewed upstream fix/replacement or a maintained project patch; document the
maintenance cost. Removing the version guard alone does not repair uses such
as Bootstrap's `$.isFunction`. Do not silently replace Drupal's jQuery with an
older global copy. A full Bootstrap 5 redesign remains a separate scope option,
but jQuery 4 compatibility is mandatory for this proof of concept.

The inherited `check-zircon.cjs` loads npm jQuery 3.7.1. Its old success cannot
prove Drupal 11 compatibility. Adapt a new fixture to load the installed core
jQuery, as `check-libraries.cjs` already does, and verify the version in output.
Preserve the existing `core/once`, repeated attachment and AJAX fixes.

The complete enabled-contributed scan produced 4,295 findings: 3 major,
4,235 minor and 57 informational; 3,907 have `/tests/` paths. The two Font
Awesome major hits require its upgrade. The third, TB Mega Menu's old Symfony
method, is behind a fallback to `getContentTypeFormat()` and is not an
unconditional Drupal 11 failure. Many other findings involve newer-core
attributes or unavailable optional/test dependencies on this Drupal 10 runtime.
Retain and review the remaining findings; rerun after preparation and on Drupal
11. These counts are not a tally of 4,295 production defects or a clean bill of
health. See [scan summary](evidence/upgrade-status-summary.json).

## Recovery, Git and deployment findings

1. **Git is not a recoverable source checkpoint yet.** Only `.gitignore` and
   `README.md` were tracked at inspection. Most application/source/docs files
   are untracked. The root ignore file targets `/web/...`, while this project
   uses the repository root. Fresh checks show root settings, uploaded files,
   private files, archive, database path and FTP/SFTP settings are not generally
   excluded. Correct exclusions and review staged paths before any commit.
2. **A real private backup path returned HTTP 200 to HEAD locally.** DDEV nginx
   ignores the Apache `.htaccess` inside `private-files`. No backup body was
   downloaded. `/sync/core.extension.yml` correctly returned 403. Move private
   storage outside the web root or apply and test explicit nginx denial before
   further rehearsal. Verify actual production rules separately.
3. **Root deployment copies too much.** The inherited `.cpanel.yml` recursively
   copies `*` into `public_html`. It can include `private-files`, `database`,
   `docs`, `tools` and sync exports; the wildcard omits root dotfiles such as
   `.htaccess`. It is not a demonstrated Drupal 11 release procedure. Replace
   it with a reviewed artifact/deployment process before production use.
4. **Private backups are present locally, not a verified recovery service.**
   `.ddev/upgrade-study-archive` exists. This appraisal did not restore its
   contents or prove an off-machine copy. Preserve matching source, database,
   settings and both public/private file trees in private durable storage and
   exercise restoration. Do not put those archives into GitHub or a web root.
5. **Operational files are web-visible in this layout.** HEAD returned 200 for
   `composer.json` and a documentation evidence JSON. Keep operational docs
   and build/development files out of public deployment artifacts. No new
   production exposure has been established by these local observations.

## Readiness and remaining uncertainty

Current Composer checks, no pending updates, working theme checks and available
compatible package branches support attempting the upgrade. They do not
establish a fixed production price or a successful Drupal 11 migration.

The next implementation must resolve the JavaScript conflict, complete the
package plan, preserve legacy logins/feed data, and pass the acceptance sheet.
Production PHP/database, CLI/build access, cron, mail, private storage, deployment,
backups and rollback remain unverified. Accessibility review and a redesign are
separate scope decisions. The local status report currently has warnings for
writable settings, Tour and Drupal 10 coverage ending 9 December 2026; no error
severity entries were observed. Verify support dates before scheduling cutover.
