**Current site: Drupal 11.4.13, upgraded locally on 24 November 2026.** See [the current development handover](local-upgrade-2026-11-24/README.md) and [progress record](upgrade-progress.md). The October POC material below is historical.

---

# Buriton website upgrade handover

**Technical POC complete — 19 October 2026.** The copied site has successfully
progressed from Drupal 9.5.11 through Drupal 10.6.19 to **Drupal 11.4.10**, with
PHP **8.4.18**, Drush **13.8.0** and the existing Zircon public theme. Maintenance
mode is off. Production has not been changed.

**Start with [the final POC handover](drupal11/poc-handover.md).** It brings the
whole migration together: what changed, exact checkpoints, patch ownership,
custom installation paths, data/file evidence, backup references, the source
bundle and the sequence to replay against a fresh live snapshot.

For council correspondence, [the copy-ready ChatGPT briefing](drupal11/bpc-email-chatgpt-briefing.txt)
exports the verified result and remaining risks in plain language. Paste its
full text into a new chat; it is self-contained and needs no project files or
earlier conversation. This communication export was added after the frozen POC
source bundle and does not change that checkpoint.

All four core requirements now use **`^11.4.10`**, allowing compatible stable
Drupal 11 minor/patch releases below Drupal 12. The final lock still installs
11.4.10; the policy change did not upgrade dependencies. The final manifests
and locks are preserved in [the reference checkpoint](drupal11/reference-files/final-drupal11/).

A source/documentation archive with a checksum sidecar is saved in the ignored
`.ddev/upgrade-study-archive/drupal11-poc-final-2026-10-19.tar.gz`. It excludes
credentials, database dumps, uploaded files and generated dependencies. It is
a handover bundle, not a production deployment artifact or data backup.

The production rehearsal and workflow/accessibility acceptance remain separate
from the completed technical POC. Known findings are explicit: **292 managed
file references were already absent before core 11**, while all 1,726 existing
managed files were byte-identical afterwards. Existing temporary private
checkpoints need durable/off-machine preservation. The inherited broad cPanel
copy task must be replaced before deployment. See the handover and checklist.

## Read for the real upgrade

| Document | Purpose |
| --- | --- |
| [Final POC handover](drupal11/poc-handover.md) | Start here: complete findings, retained implementation and real-upgrade sequence |
| [Production acceptance checklist](drupal11/acceptance-and-handover.md) | Verified POC results and remaining live-rehearsal/cutover gates |
| [Drupal 9 → 10 replay](feasibility-study/02-replay-runbook.md) | Required intermediate stage and Aggregator/data handover |
| [Drupal 10 → 11 replay](drupal11/local-upgrade-runbook.md) | Preparation, migration, validation and matching rollback |
| [Final close-out evidence](drupal11/evidence/2026-10-19-poc-closeout/) | Constraint change, final runtime, lock checks, baseline and source manifest |

## Detailed implementation records

| Record | Subject |
| --- | --- |
| [Previous Drupal 9 → 10 study](feasibility-study/README.md) | Historical work, stage-specific locks/source references and reusable checks |
| [Drupal 11 technical appraisal](drupal11/technical-appraisal.md) | Original 18 October baseline and decisions; later implementation supersedes its outstanding-work statements |
| [Repository preparation](drupal11/2026-10-19-repository-preparation.md) | Root Git exclusions and restored configuration exports |
| [Drush and module cleanup](drupal11/2026-10-19-drush-and-module-cleanup.md) | PHP 8.4/Drush preparation, usage decisions and retired package chains |
| [Contributed compatibility review](drupal11/2026-10-19-contributed-compatibility-review.md) | Scanner findings and supported package decisions |
| [Contributed module updates](drupal11/2026-10-19-contributed-module-updates.md) | Selected releases, PHP compatibility and integration checks |
| [Theme/frontend preparation](drupal11/2026-10-19-theme-updates.md) | Drupal 11 declarations, pinned assets, jQuery 4 and managed patches |
| [Core upgrade](drupal11/2026-10-19-core-upgrade.md) | Actual core/database/configuration migration and data/file verification |
| [Modified contributed-project paths](drupal11/2026-10-19-custom-paths.md) | Composer-managed custom paths and one-time existing-install relocation |
| [Tour asset warning](drupal11/2026-10-19-tour-asset-warning.md) | Clean current status-report assets and stale-browser-request diagnosis limits |

Dated records and evidence are retained rather than rewritten into a fictional
single baseline. Use each stage's matching code/lock/database/files and measured
before/after values. Source commits, production hosting, live integrations and
the current live database require their own review during the real upgrade.
