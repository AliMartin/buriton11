# Buriton local Drupal 9 → 10 → 11 upgrade

Completed locally on 24 November 2026. **Drupal 11.4.13** is available at
https://buriton9.ddev.site. Production was not modified or deployed. The DDEV
project name remains `buriton9`; its project type is now `drupal11`.

This record describes the actual fresh-data upgrade. The October POC remains
historical evidence in [the POC handover](../drupal11/poc-handover.md) and in the
unchanged `_upgrade-poc/buriton10-poc` and `_upgrade-poc/buriton11-poc` builds.
The resumable operation journal is [upgrade-progress.md](../upgrade-progress.md).

## Follow-up: Contact and History uninstalled

On 24 November 2026, at the owner's request, core Contact and History were
uninstalled after both dependency validators and a Drush simulation showed no
blockers. A fresh private database backup was taken first. Core Contact's
feedback/personal forms and view-mode configuration, Contact role permissions,
and History's read-tracking table were removed by uninstall. Stale core Contact
references in Entity Clone, CAPTCHA and Honeypot were also removed. Webform,
its two existing public contact forms and all 447 submissions remain intact.

Only resulting export deltas were applied: six configuration files updated and
six deleted. The same three pre-existing export differences remain. All 16
public HTTP checks, including both Webform pages, pass; no database updates
remain. The deprecated-module status warning is gone. Stable9 and the future
HTML5-validation default are the two remaining status warnings. Bundled core
module code remains supplied by Drupal; it was not manually deleted.

Evidence: [cleanup verification](evidence/contact-history-cleanup/verification.json).
Private before/after database/configuration checkpoints are retained in
`/Users/aam/Buriton-upgrade-backups/2026-11-24/contact-history-cleanup`.
The earlier final-upgrade checkpoint/evidence describes the state before this
follow-up; use the follow-up checkpoints for the latest database/configuration.

## Final runtime and release policy

| Component | Version |
| --- | --- |
| Drupal | 11.4.13 |
| PHP | 8.4.27 |
| Drush | 13.8.1 |
| Database | MariaDB 11.8.9 |
| DDEV | 1.25.5; `drupal11`; original root document layout |
| Public/admin themes | Zircon / Claro; Buriton5 retained as alternative |
| Core root requirements | `^11.4.13`, stable compatible Drupal 11 releases |

The Composer lock fixes the exact tested set. Deploy/build from it with
`composer install`; resolve updates separately and review patch compatibility.
Global minimum stability is stable, with the inherited explicit Entity Clone
beta exception. There are no newer compatible direct Drupal packages in the
final Composer outdated result, no reported security advisories and no
abandoned packages. These are observations on the upgrade date, not a guarantee
about later advisories.

Official release references: [Drupal 11.4.13](https://www.drupal.org/project/drupal/releases/11.4.13),
[Drupal 10.6.22](https://www.drupal.org/project/drupal/releases/10.6.22), and
[Upgrade Status 5.0.0](https://www.drupal.org/project/upgrade_status/releases/5.0.0).

## Migration and implementation

1. Captured the fresh Drupal 9.5.11 database, full code/settings/uploads,
   active configuration, content counts and physical-file hashes. The old
   configured sync path was absent; root `sync` was compared independently.
2. Installed the documented prepared Drupal 9 lock, custom-theme repairs and
   Bootstrap5 base-theme preservation patch. Completed Drupal 9 post-updates
   and enabled Stable9. Unused Popup Message and duplicate contributed Zircon
   were removed by Composer after checking enabled extensions.
3. Installed the tested Drupal 10.6.19 build, completed its database migration
   and uninstalled obsolete Admin Toolbar Links Access Filter. Aggregator was
   never uninstalled: its contributed replacement migrated the existing data.
   Password Compatibility remains enabled. Verified both themes and data, then
   captured a matching full Drupal 10 code/database/upload checkpoint.
4. Validated and uninstalled unused Tour, Image CAPTCHA and the retired Image
   Field to Media conversion utility. Existing media stayed intact; no content
   conversion was run. CAPTCHA/reCAPTCHA, Rules, FitVids, feeds and existing
   ordinary menus remain. Disabled Adminimal/CKEditor 4/iframe/FakeObjects,
   HTTPSWWW, Google Analytics and Taxonomy Menu dependency chains were retired
   as documented in the POC. Custom-theme hardcoded integrations were retained.
5. Changed local PHP to 8.4, installed current compatible dependencies on
   Drupal 10.6.22 and ran their updates. Upgraded Font Awesome to 3, Similar
   Terms to 2, Upgrade Status to 5, and the other newer compatible releases
   listed below. Upgrade Status was initially disabled in the fresh DB and was
   enabled locally for diagnostics.
6. Restored the final tested custom themes/frontend assets from the POC.
   Zircon uses the pinned Entreprise7pro Bootstrap 3.4.9 JavaScript and patched
   Slick; its existing Bootstrap 3 CSS/look is retained. Rebuilt Buriton5 CSS
   and source map with Dart Sass 1.98.0 against Bootstrap5 4.0.9. Upstream Sass
   deprecation warnings are retained in private logs.
7. Reproduced all four Composer patches. TB Mega Menu and Bootstrap5 remain
   upstream packages managed by Composer under `modules/custom/tb_megamenu`
   and `themes/custom/bootstrap5`; further changes belong in patches, not
   direct generated-directory edits. Cycle stays in `libraries/jquery.cycle`.
8. Verified and checkpointed the prepared Drupal 10 source/database, then
   installed Drupal 11.4.13, completed core database updates and reviewed the
   new runtime scaffold (`autoload_runtime.php` and vendor runtime loader).
9. Applied only migration configuration deltas through a three-way merge of
   initial active, final active and existing root exports. No config import
   was performed. Merged 154 files, removed 6 obsolete exports, no conflicts.
   The same three pre-existing export differences intentionally remain:
   financial-report View only in DB, and both contact Webform exports differ.
   Review those before any future full config import.

Webform recipients/handlers were preserved in active configuration; generated
handler changes only add language settings. Core updated editor/list/source
settings, search modules/pages, Views/block defaults and performance settings.
Core-generated permission additions are recorded explicitly: `delete own files`
for authenticated/editor/parish_council/website_editor, and `rebuild node access
permissions` for website_editor. Owner role/access acceptance should review them.
See [the value-free configuration summary](evidence/config-change-summary.json).

## Data and file preservation

| Record | Before and after |
| --- | ---: |
| Nodes | 1,894 |
| User records including anonymous | 6 |
| Managed files | 2,035 |
| Taxonomy terms | 122 |
| Media | 897 |
| Webform submissions | 447 |
| Feeds | 2 |
| Feed items | 2,190 |
| Legacy-hash accounts | 5 |

The original backup was imported into a separate local verification database,
`buriton_upgrade9_verify`, without restoring over the working DB. All 66
selected content/revision/user/media/taxonomy/file/submission tables have
identical full-row hashes before/after. All recorded Aggregator identity and
item fingerprints match. Existing account hashes were not changed.

All **1,726 present managed files** are byte-identical. **309 managed references
were already absent** in the starting local files; the migration introduced no
additional missing managed files. This is higher than the October POC's 292
and needs comparison with the current live public/private uploads. The complete
original physical-file comparison found no loss/change of original material
uploads; generated CSS/JS/cache files were regenerated as expected. Private
URI/file-ID detail remains in the private checkpoints, not this repository.

## Verification and limits

Passed: no pending database updates; strict Composer validation/platform/audit;
empty direct-Drupal outdated list; install dry run; an independent clean install
from manifests/locks/patches; clean-build comparison of 60 TB Mega Menu, 8,421
Bootstrap5 and 3 Cycle files; runtime scaffold; all three themes' Twig/render/
layout/assets; real Claro status-report assets; password-service fixtures;
contributed integration and managed-library checks; normal/minified Zircon,
Webform-widget and Cycle/TB JavaScript fixtures using installed jQuery 4.0.0;
16 public HTTP routes; well-formed feed output; 3 real document downloads
matching disk hashes; HTTP 403 for actual private files and development/config
paths. Selected results are in [evidence](evidence/).

The browser-control host was unavailable. No graphical desktop/mobile visual,
keyboard/touch, real CKEditor save/reopen, full TB admin AJAX, actual legacy-user
password login/reset, CAPTCHA challenge, mail-delivery or controlled cron/feed
refresh acceptance is claimed. No real form submissions or external mail were
sent. These remain owner/editor acceptance tasks before production. Existing
password-service fixtures prove service compatibility, not a real user's login.
The separate [production acceptance sheet](../drupal11/acceptance-and-handover.md)
remains applicable; its historical POC results are not this run's signoff.

At initial upgrade close-out, status warnings concerned Drupal-12 preparation: deprecated Contact/History
modules, Stable9 theme and the future HTML5-validation default. Contact and History were subsequently removed at the owner's explicit request, as recorded above. Stable9 remains required by Buriton5. Pre-11 scans found no Drupal 11 custom-theme
blockers; Buriton5's optional search-block empty string has a Drupal-12-only
finding. Entity Clone remains beta; the Bootstrap 3 fork/assets and four local
patches still need maintenance when dependencies change.

## Local operations and future development

Automatic cron is disabled only inside the DDEV settings condition, so local
verification does not refresh feeds/send notifications/run scheduled backups.
PHP sendmail routes to Mailpit. Maintenance is off. Settings files are read-only;
make them temporarily writable deliberately before future local edits. Local
sync uses root `sync`. No production settings or credentials were copied from
the POC; the original local environment was retained.

Local nginx denies direct access to private-files, database, tmp, sync, tools,
docs and `_upgrade-poc`. Selected reproducible DDEV source rules are recorded in
[reference-files](reference-files/); working `.ddev` stays ignored. Keep these
protections if the local server is reconfigured. Apache equivalents must be
reviewed separately for hosting. No production configuration is implied.

Composer now manages `/libraries`; previous manually installed code is retained
in the private backup. Git ignore rules cover generated code, credentials,
uploads, local archives and POC copies. Previously tracked generated library
files can still appear in Git changes until an intentional repository cleanup;
no staging, commit or push was performed. Do not blindly stage the working tree:
existing configuration exports can contain environment-specific values/secrets.
The inherited `.cpanel.yml` was not used and its broad copy task still needs
replacement before a separately authorised production deployment.

The CLI collectors in `tools/` are retained for future checks. The merge/report
helpers use this run's `tmp/upgrade-*` snapshots and must not be rerun blindly on
a later site. Set `THEME_QA_BASE_URL` explicitly for checks on another URL.

## Recovery checkpoints

Private working checkpoint directory: `/private/tmp/buriton-upgrade-20261124`.
Durable private copy: `/Users/aam/Buriton-upgrade-backups/2026-11-24` (see the
progress record for copy verification).

- `pre-drupal9/site.tar.gz` + `database.sql.gz`: original full code/settings/
  dependency/public/private-file snapshot and matching DB; excludes `.git` and
  the immutable POC reference builds.
- `drupal10/site.tar.gz` + `database.sql.gz`: verified Drupal 10.6.19 full state,
  including both file trees and runtime settings.
- `pre-drupal11/source.tar.gz` + `database.sql.gz`: prepared Drupal 10.6.22 /
  PHP 8.4 source/settings/database; use public/private uploads from the full
  Drupal 10 checkpoint (verified identical managed files).
- `final-drupal11/source.tar.gz` + `database.sql.gz`: final Drupal 11 code/
  settings/database checkpoint; same verified original uploads as above.

Each checkpoint has SHA-256 checksums and gzip-integrity verification. The
original DB was restored separately and checked; a full isolated restored-site
boot/cutover rehearsal is still outstanding. The durable copy is on this
computer, not an off-machine backup. Keep the owner's independent backups.
Restore matching code/runtime/settings, DB and file trees together. Never put
Drupal 9/10 code over the upgraded database and treat that as rollback. The
final clean-build test directory is container-only `/tmp/buriton-clean-build-20261124`;
recreate it from the lock if the container is removed.

Do not repeat the migration on this now-upgraded database. Resume future work
from the current progress record and inspect actual state first.

## Retained contributed release inventory

| Package | Installed version |
| --- | --- |
| `drupal/admin_toolbar` | 3.6.3 |
| `drupal/aggregator` | 2.2.6 |
| `drupal/backup_migrate` | 5.1.5 |
| `drupal/bootstrap5` | 4.0.9 |
| `drupal/captcha` | 2.0.12 |
| `drupal/classy` | 2.0.3 |
| `drupal/ctools` | 4.1.2 |
| `drupal/entity_clone` | 2.2.0-beta1 |
| `drupal/fitvids` | 2.1.0 |
| `drupal/fontawesome` | 3.0.0 |
| `drupal/honeypot` | 2.2.2 |
| `drupal/pathauto` | 1.16.0 |
| `drupal/recaptcha` | 3.5.0 |
| `drupal/redirect` | 1.13.0 |
| `drupal/rules` | 4.0.1 |
| `drupal/similarterms` | 2.0.0 |
| `drupal/stable` | 2.1.0 |
| `drupal/tb_megamenu` | 1.10.0 |
| `drupal/token` | 1.18.0 |
| `drupal/typed_data` | 2.1.1 |
| `drupal/upgrade_status` | 5.0.0 |
| `drupal/views_slideshow` | 5.0.1 |
| `drupal/webform` | 6.3.1 |
