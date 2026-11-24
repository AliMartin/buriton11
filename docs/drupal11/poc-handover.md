# Drupal 9 → 10 → 11 POC: final handover

**Technical POC complete — 19 October 2026.** The local upgrade has demonstrated
the route from the original Drupal 9.5.11 site through Drupal 10.6.19 to
**Drupal 11.4.10**, retaining Zircon's appearance and the site's existing data.
The owner requested POC close-out after reviewing the local upgrade. Production
has not been upgraded or deployed. The real upgrade starts with a fresh live
snapshot and the production rehearsal/checklist below.

This is the starting point for returning to the project without the chat.
Earlier dated records remain evidence of their own stages; this document and
the final reference locks describe the completed POC.

## Final tested position

| Item | Final POC state |
| --- | --- |
| Core | Drupal 11.4.10, locked |
| Core policy | All four root core packages use `^11.4.10`: stable compatible versions `>=11.4.10 <12.0.0` |
| Runtime | PHP 8.4.18; Drush 13.8.0; MariaDB 11.8.6; DDEV project type `drupal11` |
| Layout | Repository root is the document root; no `web/` relocation |
| Themes | Public Zircon → contributed Classy → Stable; alternative Buriton5 → Bootstrap5 → Stable9; admin Claro |
| Modified upstream module | TB Mega Menu 1.10.0 at `modules/custom/tb_megamenu`, installed/patched by Composer |
| Modified upstream theme | Bootstrap5 4.0.8 at `themes/custom/bootstrap5`, installed/patched by Composer |
| Site-specific themes | `themes/custom/zircon` and `themes/custom/buriton5`, maintained as source |
| Other selected releases | Font Awesome 3.0.0; Similar By Terms 2.0.0; Aggregator 2.2.6; Entity Clone 2.2.0-beta1 (explicit beta exception) |
| Configuration | 534 reviewed exports in `sync`, matching active configuration |
| Operations | Maintenance off; local automatic cron disabled deliberately; mail routed to Mailpit |
| Final files | [Reference manifests and locks](reference-files/final-drupal11/) |

The final policy change from `~11.4.10` to `^11.4.10` **did not update any
package version**. Only the lock's content hash changed. `composer install`
uses the tested lock; `composer update` deliberately resolves allowed updates.
Future minor versions must satisfy PHP and all package constraints and pass
the patch/runtime checks. This policy does not claim that future releases have
already been tested and does not allow Drupal 12.

## What was learned and changed

### Drupal 9 → 10 foundation

The [historical implementation](../feasibility-study/01-work-completed.md) and
[9 → 10 replay runbook](../feasibility-study/02-replay-runbook.md) preserve the
first stage: custom-theme repairs, CKEditor 5 preparation, necessary module
branch changes, contributed base themes, browser-library management and the
Aggregator handover. Keep the stage-specific reference locks and source
checkpoints. **Complete the Drupal 10 database updates before installing core
11. Do not uninstall/reinstall Aggregator:** its contributed package takes over
the existing module and data in place. Theme/base-theme updates must retain the
Bootstrap5 guard described below.

### Drupal 10 → 11 preparation and upgrade

- Raised the local runtime to PHP 8.4 and Drush 13, updating Drush support
  dependencies that emitted PHP 8.4 deprecations.
- Reviewed actual usage before removing packages. Uninstalled Tour, Image
  CAPTCHA and the retired Image Field to Media migration utility. Removed the
  unused Adminimal, CKEditor 4/iframe/FakeObjects, HTTPSWWW, Google Analytics and
  Taxonomy Menu package chains; ordinary menus, media and configured reCAPTCHA
  remain. The theme's hardcoded analytics was not removed. Full decisions are
  in [the cleanup record](2026-10-19-drush-and-module-cleanup.md).
- Selected contributed releases compatible with both core stages, including
  Font Awesome 3 and Similar By Terms 2. The explicit Entity Clone beta
  exception is retained; global minimum stability is stable. See
  [the version decisions](2026-10-19-contributed-module-updates.md).
- Added Drupal 11 declarations to both site-specific themes. Replaced Zircon's
  Bootstrap JavaScript with the maintained Entreprise7pro 3.4.9 distribution;
  retained Bootstrap 3.4.1 CSS/fonts. Patched bundled Slick 1.8.1's removed type
  helpers, preserving repeated/AJAX attachment, forms, sliders and layout.
  [Pinned sources, licences and replay](2026-10-19-theme-updates.md).
- Upgraded core and its coordinated Symfony/runtime dependencies, ran all
  database updates, reviewed the runtime scaffold and reconciled the exports.
  Core added `search_node` and `search_help`; 59 exports changed and two became
  obsolete. CKEditor list settings, Views defaults and block settings migrated.
  The core update added `rebuild node access permissions` to `website_editor`;
  review this role change in production acceptance.
  [Full migration record and diff](2026-10-19-core-upgrade.md).
- Moved the patched upstream module/theme to the requested custom paths.
  Exact Composer installer rules precede generic rules; reinstalling reproduced
  the patches byte for byte. Buriton5's Sass imports its sibling Bootstrap5.
  Existing installations need the [one-time relocation steps](2026-10-19-custom-paths.md);
  changing installer rules alone does not relocate an installed package.
- Corrected root Git exclusions and retained root entry-point/dotfile visibility.
  `extra.drupal-scaffold.gitignore` is false so scaffolding does not undo those
  rules. The official Symfony Runtime plugin is allowed; root
  `autoload_runtime.php` and the generated vendor runtime loader are required.
- Denied direct local nginx access to `sync`, `private-files`, `database` and
  `tmp`. An existing private file was checked, not merely a nonexistent path.
  Local settings select root `sync` and disable automatic cron inside the DDEV
  condition. These settings and nginx rules need deliberate hosting equivalents.

### Patches to keep with the source

| Package | Patch / reason |
| --- | --- |
| `drupal/bootstrap5` | `bootstrap5-preserve-required-base-themes.patch`: stop a post-update uninstalling Classy/Stable while Zircon needs them |
| `drupal/tb_megamenu` | `tb-megamenu-php84-nullable-parameters.patch`: explicit nullable arguments for PHP 8.4 |
| `drupal/tb_megamenu` | `tb-megamenu-jquery4-json.patch`: native JSON parsing for the admin block chooser |
| `malsup/jquery.cycle` | `jquery-cycle-jquery4.patch`: native function checks in the full/lite browser library |

All four Composer patches live in root `patches/` and are checksum-locked in
`patches.lock.json`. Cycle stays under `libraries/jquery.cycle`. Keep patches,
the lock, Composer library manifests/merge-plugin settings and explicit install
paths together. The moved upstream directories remain generated dependencies
and are ignored by Git; maintain further edits as patches. Retire a patch only
after checking an upstream replacement and rerunning the relevant checks.
The separate [Slick patch](checks/slick-jquery4.patch) documents modifications
already applied to the committed Zircon assets; do not apply it again.

## Evidence and its limits

The final [close-out evidence](evidence/2026-10-19-poc-closeout/) records current
runtime, baseline, constraints, Composer validation/platform/audit/install dry
run, no pending updates, matching configuration, local settings, HTTP routes
and real status-report assets. Earlier dated evidence records the deeper theme,
contributed-module, normal/minified JavaScript and library regressions.

Data at close-out: **1,880 nodes, 6 user records including anonymous, 2,018
managed-file records, 122 terms, 891 media, 443 submissions, 2 feeds and 2,310
feed items**. During the core migration the recorded identities/metadata/feed
fingerprints were unchanged. All **1,726 existing managed files** were checked
byte for byte against the checkpoint. **292 referenced files were absent both
before and after**; the private checkpoint holds their IDs/URIs. Investigate
that pre-existing files-copy/content issue against the fresh live snapshot.
There were no introduced missing files in the comparison. Earlier stages have
different feed counts; use each stage's matched baseline, not a historical
number after feeds/cron have run.

Password Compatibility remains enabled for four legacy-hash accounts. Existing
password hashes did not change during the upgrade, and the real password
service passed public legacy/modern fixtures. An actual old-password login
and reset delivery still belong to the production rehearsal. The baseline
fingerprints are not checksums of every content/revision column. jsdom and
HTTP 200/render checks do not constitute complete browser/editor/accessibility
acceptance. Those limits are retained in the [acceptance sheet](acceptance-and-handover.md).

The reported missing `tour` "theme"/`dirname(NULL)` warnings came through a JS
aggregate request. Fresh Claro status pages and all eight local assets were
clean before/after a cache rebuild. An old cached Tour library request is the
likely explanation, not a captured reproduction. Close old administration
tabs and hard-refresh after an upgrade; retain a failing asset URL if it recurs.
[Investigation and repeat asset check](2026-10-19-tour-asset-warning.md).

## Carry forward the actual implementation

The final source/documentation bundle is saved locally at:

```text
.ddev/upgrade-study-archive/drupal11-poc-final-2026-10-19.tar.gz
```

Its adjacent `.sha256` file verifies the archive. It includes final root
manifests/locks, patches, both maintained custom themes, sync exports, QA tools,
all handover records and historical reference locks, root scaffolds and the
selected DDEV configuration/nginx rules. The included source manifest lists
the bundled files and hashes. It excludes downloaded dependencies (including
the two Composer-managed custom paths), SQL dumps, credentials/settings,
TLS material, uploads, temporary data and existing private archives.
The archive copy of `sync/recaptcha.settings.yml` has its populated secret key
removed. The working export/site is unchanged; supply the key securely from
the fresh live environment before testing forms. A notice at the archive root
and the source manifest record this sanitisation. Three older status-report
captures had action-token links redacted at close-out; their original dated
checksum manifests are historical.

**This is a handover/source bundle, not a production deployment artifact or a
data backup.** Import a fresh private database and complete public/private file
trees, supply local settings, and run `composer install` to rebuild dependencies.
The current working `sync/recaptcha.settings.yml` still contains its existing
secret: review/move that into environment-specific settings before publishing
source or staging a public commit. Do not blindly stage the working tree.
The inherited `.cpanel.yml` is retained as source context but its broad copy
task is unsafe for the real deployment and must be replaced before cutover.

Carry the original ignored `.ddev/upgrade-study-archive` private checkpoints
separately if the historical source/database states are needed. The newer
pre-core/theme/path-change backups still exist outside the web root in
`/private/tmp`; their locations are recorded in ignored
`tmp/core11-backup-location.txt`, `tmp/theme11-backup-location.txt` and
`tmp/custom-paths-backup-location.txt`. Preserve secure durable/off-machine
copies before cleanup. A separate database import and selected source/settings/
upload restores were verified; a complete restored-site boot is still a
production-rehearsal task. No source commit, push or deployment was made.

## Real-upgrade sequence

1. **Capture fresh live state:** code/lock, database, full public/private files,
   live PHP/database versions and enabled extensions. Freeze external mail,
   cron/feed changes and remote integrations on the isolated rehearsal copy.
   Compare live changes since the POC and verify matching private backups.
2. **Replay 9 → prepared 10:** follow the historical runbook and stage-appropriate
   locks/source. Preserve Aggregator data, base themes and password support.
   Complete database updates, check the live-specific configuration delta and
   retain a recoverable prepared Drupal 10 checkpoint.
3. **Prepare the 10 → 11 package/theme/runtime work:** use the current
   [local runbook](local-upgrade-runbook.md) and detailed records linked above.
   Recheck supported security releases and host requirements; resolve and test
   a new lock on the rehearsal build if releases have changed. Do not assume
   the final POC lock or broad caret range is a future production sign-off.
4. **Rehearse core 11:** maintenance on; install the reviewed dependency build;
   apply database updates; review scaffold, extension paths, roles/editors/
   search and fresh configuration exports; rebuild caches. Do not import this
   POC's complete `sync` over new live settings/handlers blindly. Compare data
   and files before deliberately running cron, feeds or editing.
5. **Accept the real workflows:** owner/editor desktop/mobile review, keyboard/
   touch/console checks, CKEditor save/reopen, real legacy login, reset and
   contact delivery/CAPTCHA through Mailpit, full TB admin UI, Rules, controlled
   cron/feed refresh, public documents and authorised private downloads. Use
   [the production checklist](acceptance-and-handover.md); time the rehearsal.
6. **Prepare and execute cutover separately:** replace the blanket cPanel copy,
   build a clean reviewed deployment artifact, verify production web rules,
   private storage, PHP/CLI, permissions, mail and cron ownership. Agree content
   freeze, operator, rollback trigger/deadline and post-launch checks. Deploy
   the tested lock with `composer install`, not a fresh dependency solve.

Rollback restores the **matching code/lock/configuration, database and uploaded
files** for the preceding stage. Reverting code alone against a migrated
database is not a rollback. Rehearse the full restore before live cutover.

## Useful repeat checks

Run from the project root; set `THEME_QA_BASE_URL` to the actual rehearsal URL
when it differs from this copy. The detailed runbooks list every stage's checks.

```sh
ddev composer validate --strict
ddev composer check-platform-reqs
ddev composer audit
ddev drush updatedb:status
ddev drush config:status
ddev drush php:script docs/drupal11/check-baseline.php
ddev drush php:script tools/check-password-compatibility.php
ddev drush php:script tools/check-contrib.php
ddev drush php:script tools/check-libraries.php
ddev drush php:script tools/check-http.php
ddev drush php:script tools/check-admin-assets.php
```

For each theme, use `tools/check-theme.php` with `THEME_QA_THEME` set to
`zircon`, `buriton5` or `claro` and the explicit base URL. JavaScript fixtures in
`docs/drupal11/checks` use locked npm dependencies (`npm ci --ignore-scripts`)
and accept the installed core jQuery path and expected version (4.0.0 in this
POC). Run the Zircon normal/minified, Webform-widget and Cycle/TB fixtures after
any core/library/patch update. Treat the four current status warnings—local
settings protection, deprecated Contact/History, deprecated Stable9 and the
future HTML5-validation default—as documented follow-up decisions, not reasons
to remove required functionality during generic cleanup.
