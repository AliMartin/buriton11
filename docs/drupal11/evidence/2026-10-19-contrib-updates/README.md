# Contributed-module update evidence — 19 October 2026

See [the implementation record](../../2026-10-19-contributed-module-updates.md).
This is an update of the local Drupal 10 site, with an isolated future-core
constraint probe. It is not a completed Drupal 11 website migration.

| Evidence | Purpose |
| --- | --- |
| `outdated-before.json`, `outdated-after.json` | Available updates before work; final list contains only core packages |
| `available-*.json`, `selected-*.json`, `candidate-*.json` | Fresh registry metadata for each project and explicitly queried candidates |
| `release-selection.json`, `verified-selection.json` | Candidate versions, stability and Semver checks for both target core versions |
| `*-target-source.diff` | Relevant public module PHP/metadata source changes inspected before installation |
| `before-baseline.json`, `after-baseline.json`, `preservation-comparison.json` | Data/configuration/module/theme preservation and eight related-view result comparisons |
| `before-contrib-regression*`, `after-contrib-regression.json` | Actual related-content, settings/admin-route and menu-load regression checks |
| `constraint-changes.json`, `package-changes.json` | Changed root requirement floors/stability policy and the two actual module version updates |
| `update-command.json`, `update-dry-run.*`, `update-install.*` | Exact targeted plan and installation results |
| `tb-patch-dry-run.*`, `patches-relock.*`, `patches-repatch.*` | Managed patch preparation/application; Bootstrap5 preservation patch retained |
| `maintenance-*`, `implementation-commands.json`, `database-updates.*`, `cache-rebuild.*` | Maintenance-state handling and actual migration/cache commands |
| `maintenance-final-correction.json`, `maintenance-final.json`, `homepage-http.json` | Final scalar maintenance-state restoration and real homepage/redirect verification |
| `verification-commands.json`, `composer-*`, `theme-*`, `libraries.json`, `after-*` | Final checks and exit codes on the real Drupal 10 site |
| `drupal11-probe-*`, `drupal11-probe.*` | Isolated exact-version Composer constraint resolution against core 11.4.10; no real-site install |
| `fresh-upgrade-status*` | New scans of the three affected projects; findings retained with manual interpretation |
| `fontawesome-report-followup-*` | Later confirmation that the pasted 2.26/18 October report is stale; running 3.0.0 has five manual findings and no major findings |
| `backup-summary.json` | Private checkpoint scope/integrity without credentials, raw database content or backup path |
| `manifest-sha256.json` | Checksums of evidence and changed/selected source files |

The before-update functional baseline's raw output includes the old startup
deprecation notices; the JSON record was extracted from it without changing its
results. The final output parses directly as JSON and has no warning output.
The new CLI harness was corrected to supply Drupal render contexts before the
successful baseline was recorded. Its temporary metadata/probe preparation
scripts were removed after use; `tools/check-contrib.php` is the reusable check.

The final operational check caught Drush's keyed JSON wrapper being restored as
the maintenance-state value. It was corrected to the original scalar 0 and
caches rebuilt before handover. `/home` has an existing canonical redirect; the
recorded final GET chain follows only the local hostname and ends with HTTP 200.

Upgrade Status exit 1 is expected when findings remain. The one major finding is
the TB Mega Menu method behind its compatibility guard; the real JSON callback
was exercised successfully. The core 11 Composer probe is a dry run with plugins
and scripts disabled: its output may say “Installing” while describing planned
operations, but it did not install packages or modify the site's core/vendor.

Earlier evidence folders remain dated historical observations. The fresh feed
baseline for this step is 2,310 items and was preserved by the update.
