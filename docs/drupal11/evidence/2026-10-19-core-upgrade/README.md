# Actual Drupal 11 upgrade evidence — 19 October 2026

See [the completed implementation record](../../2026-10-19-core-upgrade.md).
These checks run against the upgraded **Drupal 11.4.10** local site, rather
than the earlier dependency-only solve or isolated target-asset probe.

| Evidence | Meaning |
| --- | --- |
| `backup-summary.json`, `database-restore-probe.txt` | Private pre-stage archive readback, separate database import, matching counts and selected restored file byte comparisons; temporary verification database removed after comparisons |
| `baseline-before.json`, `baseline-after.json`, `baseline-final.json` | Before core, after database migrations, and final state after reviewed sync export |
| `preservation-summary.json`, `changed-password-hashes.txt` | Counts/fingerprints preserved, new search provider modules and zero changed existing password hashes |
| `managed-file-integrity.json` | All 1,726 existing managed files byte-identical; 292 already absent before and after; no introduced differences |
| `core-release-metadata.json`, `composer-plan.txt`, `composer-upgrade.txt`, `package-changes.json` | Target metadata, reviewed solve and actual locked package changes; no contributed Drupal project version change |
| `database-update.txt`, `updates-{before,planned,after}.txt` | Planned and completed core updates; no updates remaining |
| `scaffold-and-ddev.diff`, `ddev-restart.txt` | Runtime scaffold and DDEV type changes; restart succeeded despite workstation-wide router-count warning |
| `configuration-changes.json`, `configuration.diff`, `config-{before,after}.json` | 59 reviewed migrations, two retired exports, 534 final synced configurations |
| `runtime-after.json`, `local-runtime-settings.json` | Actual Drupal/PHP/Drush/themes; maintenance off, local automatic cron disabled, Mailpit delivery path |
| `theme-*.json`, `contrib.json`, `libraries.json` | Actual core 11 theme and contributed integration checks |
| `javascript-*.json` | jsdom regressions load the **installed** core jQuery 4.0.0; these do not constitute graphical-browser acceptance |
| `password-compatibility.json` | Real enabled password service accepts/rejects public legacy/modern fixtures; no account edits or real-account login |
| `http.json` | Anonymous GETs for nine public/search/form/RSS routes; all HTTP 200; RSS XML parsed; no submissions |
| `web-protection-{before,after}.json` | Existing private file, sync export and temporary file return 403 |
| `composer-final-lock-refresh.txt`, `composer-validate.txt`, `platform.txt`, `audit.json`, `composer-install-dry-run.txt` | Strict validation, real platform requirements, no advisories/abandoned packages and nothing pending in the lock install |
| `requirements.json`, `status-summary.json`, `update-metadata-refresh.txt` | Final status has no errors and four nonblocking warnings; release metadata refreshed without full cron; action tokens redacted |

The private archive/database and missing-file list are outside the web root;
their path is recorded only in ignored `tmp/core11-backup-location.txt`.
The current backup is in `/private/tmp`, so retain a secure durable copy before
temporary cleanup. No password, credential, raw content, SQL dump or private
file URI list is intentionally included here.

The baseline hashes cover the named fields in `check-baseline.php`, not every
content/revision column. Database restore testing checked an isolated import and
selected source/file restoration, not a full restored-site boot. Browser,
real-account credentials, editor save/reopen, form delivery/CAPTCHA, full TB UI,
controlled cron/feed refresh and authorised private-download acceptance remain
on the handover checklist. No production deployment or Git commit was made.
