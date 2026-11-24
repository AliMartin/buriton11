# Contributed-module updates — 19 October 2026

**Later theme/frontend follow-up:** the Bootstrap/Slick/Cycle/TB JavaScript
issues and custom-theme declarations have now been addressed in
[the theme preparation record](2026-10-19-theme-updates.md). Remaining-work
statements below describe the checkpoint before that follow-up.

Completed on the local `buriton11` copy. **Core remains Drupal 10.6.19**, PHP
8.4.18 and Drush 13.8.0. All retained contributed module releases were checked
against both Drupal 10.6.19 and the selected future target, Drupal 11.4.10.
Production and the actual Drupal 11 core/database upgrade remain unchanged.

## Result and selection policy

Two modules had newer suitable stable releases:

- **Font Awesome 2.26.0 → 3.0.0**: removes the remaining contributed-package
  core-11 constraint blocker and fixes its PHP 8.4 nullable-parameter notices.
  [Official release](https://www.drupal.org/project/fontawesome/releases/3.0.0).
- **Similar By Terms 1.10.0 → 2.0.0**: the newer stable branch supports Drupal
  10.3, 11 and 12. Its hooks move to services with legacy forwarding hooks;
  the site's two dependent views were tested before and after the change.
  [Official project](https://www.drupal.org/project/similarterms).

The other installed modules were already on their selected latest suitable
releases. Composer's final `outdated drupal/*` check lists **only core packages**,
not contributed projects. Three contributed theme/base-theme packages were also
checked and remain on their existing suitable releases.

The root requirement floors were advanced to the verified module releases so a
future replay cannot resolve back to older pre-compatibility versions. Composer
now uses **`minimum-stability: stable`**, retaining `prefer-stable: true` and an
explicit **Entity Clone `^2.2@beta`** exception. Entity Clone 2.2.0-beta1 remains
its latest available release with no stable alternative; this is a continuing
maintenance limitation, not a claim of stable security coverage.

## TB Mega Menu patch

Retained **stable 1.10.0**, which declares both core versions, rather than
adopting 3.x alpha merely for a newer major number. A project-maintained patch
makes the four nullable parameters of `saveError()` explicit (`?string`/`?array`),
preserving their existing NULL defaults and behavior on PHP 8.4:

`patches/tb-megamenu-php84-nullable-parameters.patch`

The patch is declared in `composer.json` and checksummed in `patches.lock.json`.
It is a local compatibility patch, not asserted to be an upstream accepted fix.
The existing Bootstrap5 base-theme-preservation patch remains in the same lock.
Both packages were reinstalled with `composer patches-repatch`; both patches
applied and the Bootstrap5 guard is still present.

The real menu-load callback and admin listing pass. Its older Symfony
`getContentType()` fallback still appears in static scans, but is guarded by
`method_exists(getContentTypeFormat)` and is not used on these target runtimes.
That finding is retained for manual review, not suppressed or misrepresented as
an unconditional runtime failure.

## Complete selected inventory

The registry metadata was captured afresh for all 23 retained contributed
module/theme packages. Composer Semver checks confirm that every selected core
requirement accepts **both 10.6.19 and 11.4.10**.

| Package | Selected version | Outcome |
| --- | --- | --- |
| `drupal/admin_toolbar` | 3.6.3 | Already on the selected stable release |
| `drupal/aggregator` | 2.2.6 | Already on the selected stable release |
| `drupal/backup_migrate` | 5.1.5 | Already on the selected stable release |
| `drupal/bootstrap5` | 4.0.8 | Already on the selected stable release |
| `drupal/captcha` | 2.0.10 | Already on the selected stable release |
| `drupal/classy` | 2.0.3 | Already on the selected stable release |
| `drupal/ctools` | 4.1.1 | Already on the selected stable release |
| `drupal/entity_clone` | 2.2.0-beta1 | Retained latest beta; no stable release available |
| `drupal/fitvids` | 2.1.0 | Already on the selected stable release |
| `drupal/fontawesome` | 3.0.0 | Updated 2.26.0 → 3.0.0 |
| `drupal/honeypot` | 2.2.2 | Already on the selected stable release |
| `drupal/pathauto` | 1.15.0 | Already on the selected stable release |
| `drupal/recaptcha` | 3.5.0 | Already on the selected stable release |
| `drupal/redirect` | 1.13.0 | Already on the selected stable release |
| `drupal/rules` | 4.0.1 | Already on the selected stable release |
| `drupal/similarterms` | 2.0.0 | Updated 1.10.0 → 2.0.0 |
| `drupal/stable` | 2.1.0 | Already on the selected stable release |
| `drupal/tb_megamenu` | 1.10.0 | Retained stable 1.10.0; applied managed PHP 8.4 patch |
| `drupal/token` | 1.17.0 | Already on the selected stable release |
| `drupal/typed_data` | 2.1.1 | Already on the selected stable release |
| `drupal/upgrade_status` | 4.3.10 | Already on the selected stable release |
| `drupal/views_slideshow` | 5.0.1 | Already on the selected stable release |
| `drupal/webform` | 6.3.1 | Already on the selected stable release |

## Implementation and configuration

A fresh matching database and source/configuration checkpoint was taken before
changes. Maintenance mode was enabled during installation and restored to its
prior value afterwards. The reviewed targeted Composer plan changed **only the
two module versions**, with no new or removed packages and no core/Drush change.

After installing the selected set and reapplying the managed patches:

```sh
ddev drush updatedb --yes
ddev drush cache:rebuild
```

Drush reported **no pending database updates**. The active/sync configuration
remains **536 records with no differences**, so no configuration export/import
or unrelated handler/recipient/editor changes were needed. Existing Font Awesome
CDN/shim settings were retained; no Font Awesome field definitions were found in
the configuration. The custom themes and base-theme choices were unchanged.

See `evidence/2026-10-19-contrib-updates/update-command.json` for the exact
reviewed package list/command, and `constraint-changes.json` for every root
requirement change. Do not replay an unreviewed broad update during production
cutover. Build with the final tested `composer.json`, `composer.lock`, both patch
files, `patches.lock.json`, and library manifests using `composer install`. If
reusing already-installed same-version patched packages, reapply the managed
patches as in this rehearsal; a fresh artifact build is preferable.

## Validation completed

- Composer manifest/lock validation, platform requirements and audit pass;
  no known advisories or abandoned packages are reported. Install dry run has
  no outstanding operations. No contributed project remains outdated.
- Core bootstrap, status and pending-update checks pass. Cache rebuild completes
  without the former Font Awesome/TB Mega Menu PHP 8.4 notices. Remaining Drupal
  status warnings concern writable local settings and the core 10 support window.
- All three inherited theme checks (Zircon, Buriton5, Claro) pass, including Twig,
  resolved local assets, 3/6/3 layout and the Buriton5 stylesheet override.
- Composer-managed library checks pass with no missing or unmanaged libraries.
- New `tools/check-contrib.php` executes and renders the actual news/events
  related-content blocks against four published tagged nodes: **eight cases
  return the same ordered node IDs before and after the major module update**.
- The Font Awesome settings page, TB Mega Menu admin listing and actual JSON
  menu-load callback return 200. The callback uses the read-only load action;
  no menu configuration is saved. The regression check confirms every active
  configuration record is unchanged and reports **zero warnings** after update.
- The final served-site check confirms maintenance mode is the original scalar
  0. The existing `/home` → `/` canonical redirect ends in HTTP 200. A maintenance
  state parsing mistake found in the final check was corrected and caches rebuilt
  before this verification; the correction is retained in evidence.
- Counts and recorded field fingerprints are unchanged: 1,880 nodes, six user
  table records, 2,018 managed files, 122 terms, 891 media records, 443 submissions,
  two feeds and **2,310 feed items** in this fresh baseline. Four legacy-hash
  accounts remain; core Password Compatibility stays enabled. The fingerprint
  scope remains the field lists in the baseline collector, not full file bytes
  or every content/revision field.

The new regression check is CLI-only:

```sh
ddev drush php:script tools/check-contrib.php
```

Only the before-update baseline used `CONTRIB_QA_ALLOW_WARNINGS=1` to record
existing notices without failing the check. Final verification used normal
reporting and the strict default; no global deprecation suppression was added.

## Drupal 11 dependency rehearsal and limits

An **isolated Composer dry run** in the container's `/tmp` resolved the exact
23 selected contributed versions together with **core-recommended 11.4.10,
Drush 13.8.0 and PHP 8.4.18**. Plugins/scripts were disabled and nothing was
installed into the real site. The probe manifest and successful output are
saved in evidence. This proves package constraints can be resolved together;
it does not run custom themes, browser libraries or database updates on Drupal 11.

Fresh Upgrade Status scans of Font Awesome, Similar By Terms and TB Mega Menu
retain 139 findings (137 minor, one informational, one major). The sole major
finding is TB Mega Menu's guarded Symfony fallback described above. Other
findings include test/optional APIs and newer-core attributes unavailable to
the scanner on Drupal 10. Findings were preserved, not blanket-suppressed, and
must be reassessed against the actual core 11 runtime where relevant.

The **Zircon Bootstrap 3 / core 11 jQuery 4 conflict and the two custom themes'
core-version declarations still need their separate implementation**. No claim
is made that the full website now runs on Drupal 11. Full browser/editor/form,
accessibility and external-integration acceptance also remain pending.

## Backup and handover evidence

[Evidence index](evidence/2026-10-19-contrib-updates/README.md) includes registry
metadata, source comparisons, both plans, exact package/constraint changes,
patch application, before/after results and the isolated core 11 probe.

The pre-update database and matching source archive are in private host temporary
storage; the actual path is in ignored `tmp/contrib-updates-backup-path.txt`.
Database gzip integrity passed. This is a local rollback aid, not an off-machine
backup or a demonstrated restore. Uploaded file trees were not copied or changed.
The existing private-file HTTP exposure and production deployment prerequisites
remain outside this module-update step and still need resolving.

## Follow-up: pasted Font Awesome report

A subsequent pasted report still described **8.x-2.26 scanned on 18 October at
12:52**, including the two removed `system_retrieve_file()` errors. The running
`buriton11` installation was verified as **3.0.0**, and a forced fresh Font
Awesome scan now has **five manual findings and no major findings**: two YAML
wrapper recommendations, two unresolved PHPUnit assertion methods in test code,
and one library lookup for the uninstalled optional icon-picker widget. The
removed-function calls are absent from current module source.

No further Font Awesome patch or package change was made for the old report.
Use the refreshed results; if the browser still shows 2.26, refresh Upgrade
Status and confirm the URL is `buriton11.ddev.site`, not the older project.
Evidence is saved as `fontawesome-report-followup-*` in this step's evidence
folder; it supplements the earlier checksum snapshot.
