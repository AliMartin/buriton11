# Drush 13 and unused-module cleanup — 19 October 2026

Later the same day, [contributed-module updates](2026-10-19-contributed-module-updates.md)
installed Font Awesome 3.0.0 and Similar By Terms 2.0.0 and resolved the recorded
PHP 8.4 Font Awesome/TB Mega Menu notices. This document retains the earlier
cleanup checkpoint and its observations.

Completed on the local `buriton11` copy at the owner's request. Drupal core
remains **10.6.19**. The owner raised PHP to **8.4.18** before this work;
**Drush 13.8.0** is now installed and locked. No production deployment occurred.

## Package changes

| Package | Before | After / reason |
| --- | --- | --- |
| `drush/drush` | 12.5.3 | 13.8.0, root requirement `^13.7` |
| `composer/installers` | 1.12.0 | 2.3.0, root requirement `^2.3`; resolves installer PHP 8.4 deprecations |
| `consolidation/config` | 2.1.2 | 3.2.1; resolves Drush dependency PHP 8.4 deprecations |
| `consolidation/robo` | 4.0.6 | 5.1.1; resolves Drush dependency PHP 8.4 deprecations |
| `laravel/prompts` | Absent | 0.3.26, Drush dependency |
| `consolidation/self-update` | 2.2.0 | Removed as an unused support dependency |

Drush was updated with a targeted dry run and install using
`--with-all-dependencies --minimal-changes`. Initial testing showed that the
older support libraries still emitted PHP 8.4 deprecations; these were then
updated through another reviewed, targeted plan. No Drupal core or retained
contributed module/theme package version changed.

## Usage decisions and uninstalls

| Item | Usage evidence and action |
| --- | --- |
| Adminimal Toolbar, Adminimal theme | No enabled extensions; Claro is active. Removed direct Composer requirements and downloaded code |
| CKEditor iFrame 2.x, CKEditor 4, FakeObjects | No enabled extensions; all configured editor integrations use CKEditor 5. Removed iframe root requirement and unused transitive modules |
| Google Analytics module | Not enabled. Removed package; hardcoded analytics in the custom theme was not altered |
| HTTPSWWW | Not enabled. Removed package |
| Taxonomy Menu | Not enabled. Removed package; existing ordinary menus retained |
| Image Field to Media | Retired enabled migration utility. Provides administrative conversion operations, no rendering fields/formatters, no dependent configuration. Uninstalled, then removed its package |
| Image CAPTCHA | All configured challenges use reCAPTCHA or the default reCAPTCHA selection. No image challenge configured. Uninstalled; CAPTCHA/reCAPTCHA retained |
| Tour | Only bundled Honeypot and Views UI tours found, no bespoke site tours. Uninstalled with their configuration and obsolete Tour entity view mode |

The uninstall validator returned no blocking dependencies for the three enabled
utilities. Image Field to Media's uninstall hook deletes its conversion hash
map (544 entries were present), **not media entities**. All 891 media records
remain. The matching database backup preserves the previous state if this
historical conversion tool must be investigated later; do not blindly repeat
its conversion after reinstalling it.

The package cleanup physically removed these **12 package directories**:

- Modules: `adminimal_admin_toolbar`, `ckeditor`, `ckeditor_iframe`,
  `fakeobjects`, `google_analytics`, `httpswww`, `image_field_to_media`,
  `taxonomy_menu`.
- Themes: `adminimal_theme`, `seven`.
- Browser libraries: CKEditor `iframe`, `fakeobjects`.

Composer removed these from the container. A final host check found a remaining
`libraries/iframe` directory in the local working tree; that confirmed-unused
remnant was also deleted. All twelve paths are absent in the final host check.

The old iframe library requirement was removed from `composer.libraries.json`.
`tools/check-libraries.php` now requires PHP-referenced assets only when their
consumer module is enabled; it continues to check the retained FitVids,
slideshow/hoverIntent and JSON2 files and reject unmanaged library directories.
The old study's reference files remain historical evidence, not active code.

**Package boundary:** Image CAPTCHA ships inside the retained CAPTCHA package,
and Tour ships in Drupal 10 core. Their bundled code has not been manually
deleted; doing so would create a non-reproducible Composer installation. Both
are uninstalled. Tour's core files disappear through the planned core 11 update.
Other unused submodules supplied inside required core/contrib packages likewise
remain uninstalled within their parent packages.

Retained features include Aggregator, core Password Compatibility, Similar
Terms, FitVids, Rules, CAPTCHA/reCAPTCHA, Webform and the existing themes/base
themes. Similar Terms is referenced by the news/events views, FitVids has
configured selectors, four accounts still need legacy password compatibility,
and Upgrade Status is being used for this proof of concept. There was no basis
to remove these as unused.

## Configuration and reproducibility

Only six sync-export files needed changing:

- Updated `core.extension.yml` to remove the three uninstalled modules.
- Updated `entity_clone.cloneable_entities.yml` to remove obsolete `tour`.
- Removed `core.entity_view_mode.tour.token.yml`, `image_captcha.settings.yml`,
  `tour.tour.honeypot.yml` and `tour.tour.views-ui.yml`.

A full active-config export was staged in the container's private `/tmp`
directory and compared by filename before copying only the two reviewed updates
and deleting the four retired exports. No configuration import was performed.
The final active/sync count is **536** with no differences; Webform handlers,
recipients, text formats and other unrelated configuration were not rewritten.

The Bootstrap5 patch, patch lock, custom-theme dependency chains and Composer
library merge setup were retained. The installed Bootstrap5 guard is still
present. A Composer install dry run reports no outstanding operations.

## Verification

All recorded verification commands exited successfully:

- Drush bootstrap/runtime: Drupal 10.6.19, PHP 8.4.18, Drush 13.8.0; no Drush
  startup deprecations in the final status command.
- Composer manifest/lock validation, platform checks, audit and install dry run.
  The audit reports no known advisories or abandoned packages.
- No pending database updates, no missing-extension status errors, no
  configuration differences.
- Zircon, Buriton5 and Claro render/Twig/asset checks pass with no reported
  missing files or runtime warnings in those checks; library verification passes.
- All measured data counts and selected-field fingerprints match the fresh
  pre-cleanup baseline. Existing themes and editor configuration are unchanged.

| Records | Before | After |
| --- | ---: | ---: |
| Nodes | 1,880 | 1,880 |
| User table records | 6 | 6 |
| Managed files | 2,018 | 2,018 |
| Taxonomy terms | 122 | 122 |
| Media | 891 | 891 |
| Webform submissions | 443 | 443 |
| Feeds | 2 | 2 |
| Feed items | 2,307 | 2,307 |
| Legacy-hash accounts | 4 | 4 |

Feed items already differed from the 18 October appraisal when this task began;
the cause of that prior difference was not established. The cleanup itself
preserved the fresh 2,307-item baseline. The collector's fingerprints cover only
the fields it lists; file bytes/full content/revision history were not hashed.
No browser form submission, external mail or full editorial acceptance test was
performed during this maintenance step.

There are still **PHP 8.4 deprecation notices in retained Font Awesome 2.26 and
TB Mega Menu 1.10 code**, visible during cache rebuild/uninstall. They were not
suppressed globally. Those active features were not removed or upgraded under
the unused-module cleanup. Font Awesome 3.0 remains a required next dependency
step for Drupal 11; TB Mega Menu needs its recorded compatibility review. Drupal
status warnings now concern writable local settings and the Drupal 10 support
window; the Tour warning is gone.

## Replay commands

Use these against the reviewed **pre-cleanup Drupal 10 baseline**, after taking
backups and verifying equivalent usage. They are a record, not a script to run
blindly against a later state.

```sh
ddev composer require 'drush/drush:^13.7' --with-all-dependencies --minimal-changes --dry-run --no-interaction
ddev composer require 'drush/drush:^13.7' --with-all-dependencies --minimal-changes --no-interaction
ddev composer require 'composer/installers:^2.3' --no-update --no-interaction
ddev composer update composer/installers consolidation/config consolidation/robo --with-all-dependencies --minimal-changes --dry-run --no-interaction
ddev composer update composer/installers consolidation/config consolidation/robo --with-all-dependencies --minimal-changes --no-interaction
ddev drush pm:uninstall image_field_to_media image_captcha tour --yes
ddev composer remove drupal/adminimal_admin_toolbar drupal/adminimal_theme drupal/ckeditor_iframe drupal/google_analytics drupal/httpswww drupal/taxonomy_menu drupal/image_field_to_media --no-update --no-interaction
```

Remove the explicit CKEditor iframe library requirement, apply the small
consumer-aware library check change, then:

```sh
ddev composer update drush/drush --with-all-dependencies --minimal-changes --dry-run --no-interaction
ddev composer update drush/drush --with-all-dependencies --minimal-changes --no-interaction
ddev drush cache:rebuild
```

Remove `tour` from Entity Clone's cloneable entity-type list and reconcile the
six configuration exports described above. Use the final tested locks with
`composer install` for deployment; the retained update commands explain how
the local dependency set was prepared.

## Evidence, backup and next step

Evidence is in [the cleanup evidence folder](evidence/2026-10-19-cleanup/README.md).
The pre-change database and matching source/configuration archive are in a
private host temporary directory; the actual path is recorded in ignored
`tmp/drush13-backup-path.txt`. Gzip integrity and archive readability passed.
This is a local rollback aid, not an off-machine backup or an exercised restore.
Uploaded files were not copied or modified by this task.

Continue with Font Awesome compatibility and the confirmed Zircon Bootstrap
3/jQuery 4 conflict before attempting core 11. The private-file HTTP exposure
and production deployment concerns in the appraisal also remain outstanding.
