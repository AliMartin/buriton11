# Drupal 11 appraisal evidence

**Current result:** the [technical POC is complete](../poc-handover.md) on
Drupal 11.4.10. Start with [the final close-out evidence](2026-10-19-poc-closeout/)
and the dated core/theme/module/path/asset subdirectories. The table below
describes the original 18 October pre-upgrade evidence only.

Captured **18 October 2026** against `buriton11`, running **Drupal 10.6.19**.
These files are pre-upgrade evidence, not proof that Drupal 11 has run here.
No database dumps, raw account hashes, credentials, mail recipients or private
backup contents are included. Review future evidence before adding it to Git.

| File(s) | What they show |
| --- | --- |
| `commands.json` | Main command arguments and exit codes |
| `runtime.json`, `baseline.json` | Bootstrap/platform, enabled modules/themes, configuration counts, content counts and selected-field fingerprints |
| `locked-packages.json` | Installed Drupal/Drush versions and declared core/PHP requirements |
| `composer-validate.txt`, `platform.txt`, `composer-audit.json`, `updates.txt` | Current manifest validation, platform requirements, security audit and no pending updates |
| `config-status.json` | Final configuration comparison returned `[]`; owner later confirmed deleting/restoring sync, explaining the earlier all-DB-only observation |
| `requirements.json` | Current Drupal status report, including warnings |
| `composer-prohibits-11.4.10.txt*` | Existing locked-package/platform blockers; DDEV puts the substantive Composer tree inside stderr |
| `metadata-commands.json`, `metadata-*.json` | Registry metadata: installed/default views plus explicitly queried target releases. Read `versions` to distinguish them |
| `metadata-drupal-ckeditor_iframe-3.1.2.json` | Explicit compatible iframe release; the unversioned query describes installed 2.2.0 requirements |
| `upgrade-status-custom.txt*` | Fresh scans of both custom themes: version declarations only |
| `upgrade-status-key-modules.txt*` | Fresh focused scans of Aggregator, Font Awesome and Image Field to Media |
| `scan-command.json`, `upgrade-status-enabled-contrib.json` | Full fresh scan of all enabled contributed projects; no skip-existing option |
| `upgrade-status-summary.json` | Grouped finding counts and the three major non-test-path findings; path classification is not a correctness judgment |
| `qa-commands.json`, `theme-*.json`, `libraries.json` | Fresh inherited PHP rendering/asset/layout and library checks; all passed on Drupal 10 |
| `target-core.libraries.yml`, `target-phpass.info.yml` | Official tagged 11.4.10 source observations: jQuery 4.0.0 and retained core Password Compatibility |
| `bootstrap-jquery-guard.json` | Actual Bootstrap preamble passes a 3.7.1 version stub and rejects 4.0.0; isolated guard test, not browser execution |
| `git-ignore-check.json` | Root-layout paths not adequately excluded by current Git rules |
| `git-ignore-check-after-2026-10-19.json` | Follow-up: verified corrected exclusions and preserved source/config/docs paths |
| `baseline-after-appraisal.json`, `appraisal-preservation-check.json` | Counts/selected fingerprints and configuration unchanged; legacy-hash account count changed from 5 to 4, cause unestablished |
| `http-boundaries.json` | HEAD status only: sync denied; private backup, Composer manifest and docs evidence returned 200 locally. Private backup path/body omitted |

Files suffixed `.stderr.txt` retain companion tool messages. Exit 1 on
`composer prohibits` is expected when constraints block the target; exit 1 on
Upgrade Status indicates findings. Neither means a major upgrade was attempted.
`updates.txt` is empty because Drush's success message is in its stderr file.
No PHP unit-test development stack was installed to silence scanner findings.
The inherited npm JavaScript fixtures were inspected, not rerun for this
appraisal, and no full browser or external-delivery acceptance was performed.

The baseline collector is [../check-baseline.php](../check-baseline.php):

```sh
ddev drush php:script docs/drupal11/check-baseline.php
```

It returns only counts, selected configuration inventory and hashes of the
listed fields. User passwords are counted by legacy prefix, never printed.
Table hashes are comparable only when field lists and ordering remain the same.
They do not hash physical file bytes or every node/content/revision field.
The new baseline's 2,315 feed items differs from the earlier study's 2,314;
the cause was not established. Compare migration results before deliberate
refreshes and record intentional differences.

Target sources fetched read-only from the official repository:

- [11.4.10 core libraries](https://git.drupalcode.org/project/drupal/-/raw/11.4.10/core/core.libraries.yml)
- [11.4.10 core phpass metadata](https://git.drupalcode.org/project/drupal/-/raw/11.4.10/core/modules/phpass/phpass.info.yml)

Checksums of the evidence files and key starting source files are recorded in
`manifest-sha256.json` as the 18 October snapshot. Documentation and `.gitignore`
were subsequently amended in the recorded 19 October follow-up; their new
checksums are in `manifest-sha256-after-2026-10-19.json`. Evidence is dated;
support/release/advisory metadata must be checked again at implementation time.
