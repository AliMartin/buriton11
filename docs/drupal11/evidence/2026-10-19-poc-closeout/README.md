# POC close-out — 19 October 2026

Start with [the final handover](../../poc-handover.md).

The final change widens all four core requirements from `~11.4.10` to
`^11.4.10`, allowing stable compatible Drupal 11 minor/patch releases while
retaining the tested **11.4.10** lock. No package version, patch lock, installed
code, configuration or data was changed by this constraint update.

| Evidence | Meaning |
| --- | --- |
| `constraint-change.json` | All four caret constraints; identical package entries/versions and patch lock; only lock content hash changed |
| `composer-lock-refresh.txt`, `composer-validate.txt`, `composer-install-dry-run.txt` | Refreshed valid lock; no install/update/remove operations pending |
| `platform.txt`, `audit.json` | Real platform requirements pass; no current advisories or abandoned packages |
| `runtime.json`, `local-settings.json` | Successful current bootstrap, versions/themes, custom install paths, maintenance off, deliberate local cron override and Mailpit |
| `database-updates.txt`, `config-status.json` | No pending schema updates and no active/exported config difference |
| `baseline.json`, `closeout-summary.json` | Final counts/fingerprints/configuration compared with the preceding tested checkpoint |
| `http.json`, `admin-assets.json` | Nine public/search/form/RSS HTTP checks and fresh Claro status-report/eight real asset requests pass |
| `status-summary.json` | Final status errors/warnings only; no action tokens exported |
| `source-manifest.json`, `checksums.sha256` | Final source-bundle file list/hashes and close-out evidence/source checksums |

Deeper theme/contributed/normal/minified jQuery regression results remain in the
earlier dated evidence directories; the constraint-only change did not replace
installed code. No new browser, real-account login, editing/submission or full
restore claims are implied. The owner requested technical POC closure; the
production acceptance checklist remains for the real rehearsal.

The handover source archive and its SHA-256 sidecar live under the ignored
`.ddev/upgrade-study-archive` directory. It omits credentials, SQL dumps,
uploads, generated dependencies and prior private archives. It is not a
deployment artifact or a matching code/database/files backup.
