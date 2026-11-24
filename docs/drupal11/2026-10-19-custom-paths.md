# Modified contributed projects: custom installation paths

Completed locally on **19 October 2026**, after the Drupal 11.4.10 upgrade.
The owner requested that modified contributed modules/themes live under
`custom`. Their upstream identities, versions and managed patches are retained.

| Project | Current directory | Local changes |
| --- | --- | --- |
| TB Mega Menu 1.10.0 | `modules/custom/tb_megamenu` | Explicit nullable PHP 8.4 parameters; native JSON parsing for jQuery 4 |
| Bootstrap5 4.0.8 | `themes/custom/bootstrap5` | Post-update guard preserving base themes still needed by Zircon |
| Buriton Zircon | `themes/custom/zircon` (already custom) | Site-specific theme, Drupal 10/11 preparation and maintained frontend assets |
| Buriton Bootstrap 5 | `themes/custom/buriton5` (already custom) | Site-specific subtheme, layout/library overrides and compatibility preparation |

The modified **Cycle 3.0.3 browser library** remains at `libraries/jquery.cycle`.
It is not a Drupal module or theme, and Views Slideshow expects its library
location. Its locked jQuery 4 patch is retained.

The Composer source-status audit identified Bootstrap5 and TB Mega Menu as the
contributed extensions with changed code. Upgrade Status also contained a Finder
`.DS_Store` file; this is not a code modification requiring a custom project.
Other Composer/vendor source-status differences are outside the module/theme
relocation scope. No other contributed extension was moved or uninstalled.

## How the locations are maintained

`composer.json` now has exact-package installer rules **before** the generic
contributed-module/theme rules:

```json
"modules/custom/tb_megamenu": ["drupal/tb_megamenu"],
"themes/custom/bootstrap5": ["drupal/bootstrap5"]
```

These projects remain **Composer-managed upstream packages with local patches**.
They have not become independently maintained forks. `composer install` on a
clean build puts them at the custom paths and applies the patches from
`patches.lock.json`. Their machine names, base-theme declarations, Composer
requirements and package types are unchanged; upstream update tracking remains
available. The Composer lock was refreshed without changing package versions.

The two installed directories are explicitly excluded in `.gitignore`, as
other generated Composer dependencies are. Commit/review the manifest, lock,
patch files and patch lock to preserve the modifications. The genuinely custom
Zircon and Buriton5 theme sources remain visible to Git. A direct edit inside
the two managed directories can still be overwritten by a reinstall/update:
maintain any further changes as reviewed patches.

## Completed local relocation

1. Archived the pre-change manifests/lock, ignore rules, two packages and
   affected QA scripts outside the web root. Its private location is recorded
   in ignored `tmp/custom-paths-backup-location.txt`.
2. Recorded the Drupal/data/configuration baseline and relative file checksums.
3. Added the exact installer rules and briefly enabled local maintenance mode.
4. Moved the directories from `contrib` to `custom`, then ran:

   ```sh
   ddev composer update --lock --no-install --no-interaction
   ddev composer reinstall drupal/tb_megamenu drupal/bootstrap5 --no-interaction
   ddev drush cr
   ```

   The reinstall was deliberate: it updates Composer's installed-path metadata
   and verifies the packages and all three applicable patches reproduce in the
   new locations. The Cycle patch remains unchanged at its library location.
5. Updated Buriton5's Sass parent import to `../../bootstrap5/scss/style`.
   Dart Sass 1.98.0 compiles it successfully and produces CSS identical to the
   existing stylesheet after excluding the source-map comment. The committed
   compiled CSS was left unchanged.
6. Made the theme QA check ask Drupal for Bootstrap5's actual path. The TB
   JavaScript fixture reads Composer's installed metadata instead of assuming
   `modules/contrib`.
7. Rebuilt caches so Drupal discovers the relocated extensions and assets, then
   restored maintenance mode to zero. No schema update, configuration import,
   module uninstall, theme switch or content edit was required.

## Validation

Results are in [evidence/2026-10-19-custom-paths](evidence/2026-10-19-custom-paths/).

- Drupal and Composer resolve TB Mega Menu and Bootstrap5 to the new custom
  paths. Neither old contributed directory remains.
- All **60 TB Mega Menu files** and **8,355 Bootstrap5 files** match their
  pre-move relative-path/content checksums (excluding Finder `.DS_Store`).
  This confirms the existing local modifications survived the reinstall.
- Zircon and Buriton5 Twig/render/layout/asset checks pass. The parent CSS
  override is checked against its discovered path, so it is still meaningful.
- Contributed integrations, managed-library checks and the jQuery 4 Cycle/TB
  fixture pass. Public/search/contact/RSS HTTP smoke checks pass.
- Strict Composer validation and install dry run pass. Package versions and
  the patch lock are unchanged. Active/exported configuration and the recorded
  before/after data baseline match.

These are path/build/runtime checks, not a new owner/browser acceptance. The
remaining Drupal 11 acceptance items and the earlier missing-file finding are
unchanged; see [the core-upgrade handover](2026-10-19-core-upgrade.md).

## Replay on another checkout

**Fresh build:** use the current Composer manifest, lock, patches and patch
lock, plus the maintained custom-theme sources. Ordinary `composer install`
creates the correct directories. Do not copy older contributed duplicates
into that build.

**Existing installation using the previous paths:** changing installer rules
alone is insufficient to relocate an already-installed package. Back up first,
enable maintenance, check the destinations are absent, then move each old
directory to the new path and run the two-package reinstall and cache rebuild
shown above. Abort and investigate if both paths already exist; do not leave
duplicate copies with the same machine name. Retain the updated Buriton5 Sass
import and QA scripts with the same change. Confirm Drupal and Composer agree
on the new paths before reopening the site.

For rollback, restore the preceding manifest/lock/ignore rules and QA scripts,
move the two directories back, reinstall using those preceding rules, restore
Buriton5's old contributed-parent Sass import and rebuild caches. This step
did not change configuration or schema. The backup is in `/private/tmp`;
preserve a durable private copy before temporary cleanup. Production, Git
staging and commits were not changed.
