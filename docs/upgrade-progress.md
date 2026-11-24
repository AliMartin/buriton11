# Buriton local Drupal upgrade progress

## Follow-up — Database Logging enabled (24 November 2026)

- Enabled core Database Logging (`dblog`) at the owner's request. Recent log messages is available at `/admin/reports/dblog`; retention is the default 1,000 entries, pruned by cron.
- An empty inherited `watchdog` table blocked initial installation. Removed the incomplete installation through Drush uninstall and reinstalled successfully; no historical log entries existed.
- Verified schema version 10101 and storage of a real `site_monitoring` notice. Exported only the new logging settings/report and added `dblog` to the extension export, preserving unrelated configuration differences.
- Local automatic cron remains disabled; production unchanged.

**Current state: LOCAL UPGRADE COMPLETE — Drupal 11.4.13; maintenance off; checks passed. Checkpoint copy verification is recorded in the final journal entry. Do not repeat the migration.**

Started: 24 November 2026 (Europe/London).

## Scope and rules
Upgrade this local site from Drupal 9 through a completed Drupal 10 database migration to Drupal 11. Use `_upgrade-poc` documentation/source, select current compatible contributed releases, verify and document the result. No production deployment. Preserve user changes and do not blindly import historical configuration.

## Initial state (historical)
- Initial inspection only; no application code, dependencies or database changed.
- Root `docs` was absent and created for this record.
- Git has existing Finder metadata changes, untracked `_upgrade-poc/` and `tmp/`; retain these.
- No upgrade checkpoints created yet.

## Recovery and resumption
Read this file, inspect actual code/database/runtime and running processes, and check operation logs before repeating commands. An operation marked started may have completed despite interruption. Restore matching code, database and uploaded files together when rollback is required.

## Original action plan (historical; see latest journal entries)
1. Inspect project instructions, DDEV/runtime/site status, POC handovers and current Composer baseline.
2. Locate existing backups and create a verified matching pre-migration code/database/file checkpoint outside the document root.
3. Prepare and complete Drupal 10 migration, verify and preserve a matching checkpoint.
4. Prepare/update compatible dependencies and themes, complete Drupal 11 migration and verification.
5. Record final versions, changes, test evidence and any remaining owner/browser acceptance.

## Operation journal
- 2026-11-24: Created this record before upgrade changes. No upgrade commands have been run.

### Initial runtime and checkpoint (started)
- Drupal 9.5.11, PHP 8.2.35, Drush 11.6.0; MariaDB 11.8; local URL https://buriton9.ddev.site.
- No pending Drupal 9 updates. Enabled extensions broadly match POC baseline; CKEditor 5 already enabled.
- Current configured sync path is missing/empty (all configuration reported database-only); do not import historical sync. Preserve active DB configuration and root sync separately.
- Recovery/evidence directory: `/private/tmp/buriton-upgrade-20261124` (private temporary host storage; retain durable copy before cleanup).
- STARTED: full source/settings/upload archive excluding `.git` and immutable `_upgrade-poc`, plus separate database dump before any app mutation. POC source remains unchanged.
- DDEV requires elevated sandbox access to Docker; initial status succeeded with approved `ddev` prefix.

### Pre-migration checkpoint verified; Drupal 9 preparation STARTED
- `pre-drupal9/site.tar.gz` includes full code/dependencies, settings and uploaded files (excludes `.git` and immutable POC copies); gzip integrity passed. Database dump gzip integrity passed. SHA-256 sidecar recorded.
- Active configuration exported privately to `pre-drupal9/active-config`; 5,061 physical upload/private files hashed in `upload-files.json`.
- Baseline: nodes 1,894; user records 6; managed files 2,035; terms 122; media 897; Webform submissions 447; feeds 2; items 2,190.
- Local sendmail is Mailpit. No cron will be run during preservation comparisons.
- STARTED: restore the documented prepared-Drupal-9 Composer checkpoint and patched Drupal-10-compatible custom themes; apply required Drupal 9 module updates before core 10. Locked legacy bridge is temporary, not the final release. No security-blocking override is being configured.

### Drupal 9 bridge installed
- Prepared Drupal 9 lock installed successfully; unused disabled Popup Message and duplicate contributed Zircon removed by Composer.
- Bootstrap5 migration guard applied; both Bootstrap5 post-updates completed without uninstalling Classy/Stable. Stable9 enabled.
- Custom themes restored from Drupal 10 POC; original full sources retained in checkpoint.
- STARTED: install tested Drupal 10/library checkpoint, then migrate database. Intermediate uses tested 10.6.19 before final current Drupal 11 resolution; no public Drupal 10 deployment.

### Drupal 10 installation and history
- Tested Drupal 10.6.19 dependency build installed; the legacy lock audit reports advisories, to be resolved in the final current Drupal 11 dependency build. This intermediate is local/maintenance only.
- Drupal 10 database migration STARTED; log: `logs/drupal10-updatedb.txt` in private recovery directory. Do not install Drupal 11 until updates finish and checkpoint is verified.
- Copied historical POC documentation into root `docs/` with original relative paths, excluding downloaded Node dependencies. Original `_upgrade-poc` remains immutable.
- Adopted POC Git exclusions plus explicit exclusions for `_upgrade-poc` and local `.ddev`; no commit/staging/deployment performed.
- Added local nginx denials for source archives/docs/tools and private/config/tmp storage; activate on next DDEV restart.

### Drupal 10 migration VERIFIED; checkpoint STARTED
- Drupal 10.6.19 / PHP 8.2.35 / Drush 12.5.3; database updates complete; obsolete Admin Toolbar Links Access Filter uninstalled.
- All initial counts and selected-field fingerprints match; all 5 legacy-password accounts retained (fresh database differs from POC's 4).
- Zircon and Buriton5 Twig/render/layout/asset checks pass, zero missing assets/warnings.
- STARTED: full matching Drupal 10 code/settings/uploads archive and separate database dump under `/private/tmp/buriton-upgrade-20261124/drupal10`. Maintenance remains enabled until frontend checks are ready; local automatic cron disabled.

### Drupal 10 checkpoint COMPLETE; Drupal 11 preparation STARTED
- Drupal 10 full archive/database gzip integrity passed and SHA-256 sidecar recorded. Matching active configuration and detailed content/file baseline saved under `drupal10/`.
- All original non-generated uploaded/private files unchanged; only generated CSS/JS/cache files removed/refreshed (comparison log retained).
- 2,035 managed-file records: 1,726 physical files present, 309 missing. Original file metadata fingerprints unchanged from Drupal 9 and physical-file comparison shows no material loss. Likely incomplete/older local uploads relative to fresh DB; investigate/sync missing source uploads separately.
- Current release metadata checked via Composer and official Drupal releases. Target stable core 11.4.13; newer Bootstrap5 4.0.9, CAPTCHA 2.0.12, CTools 4.1.2, Pathauto 1.16.0, Token 1.18.0 and Upgrade Status 5.0.0 identified.
- Uninstall validators return no blockers for retired Image Field to Media utility, unused Image CAPTCHA (configured challenge is reCAPTCHA), and bundled Tour (only Honeypot/Views UI tours). All editors already CKEditor 5. Disabled legacy chains match POC removal decisions.
- STARTED: uninstall these three unused utilities on Drupal 10, then move local runtime to PHP 8.4 and prepare the final POC theme/Composer setup against current releases.
- Final POC custom themes/frontend sources and all four checksum-locked patches restored. Upstream TB Mega Menu/Bootstrap5 moved to Composer-managed custom paths; exact installer rules retained. Packages must be reinstalled to verify installed-path metadata/patches.
- Preparing a current Drupal 10.6.22 dependency set with final Drupal 11-compatible contributed constraints, Upgrade Status ^5.0, stable minimum stability and explicit Entity Clone beta exception. This ensures contributed database updates run before core 11.
- Current Drupal 10 dry-run resolved successfully: core 10.6.22; Bootstrap5 4.0.9; CAPTCHA 2.0.12; CTools 4.1.2; Pathauto 1.16.0; Token 1.18.0; Upgrade Status 5.0.0; Font Awesome 3.0.0; Similar Terms 2.0.0; Drush 13.8.1. Removal plan matches retired disabled chains. No unrelated data-bearing modules removed and no unstable releases added beyond retained Entity Clone exception.
- STARTED: install reviewed current-Drupal-10 plan, log `logs/current-drupal10-update.txt`. Check exit/log and patch application before running updates.

### Current prepared Drupal 10 dependencies installed
- Core 10.6.22 / PHP 8.4 runtime / Drush 13.8.1 installed successfully. Composer reports no security advisories.
- All four patches reproduced through explicit package reinstall, including Bootstrap5 4.0.9; correct custom package paths confirmed by reinstall.
- CAPTCHA post-update removed obsolete plain session IDs successfully; no content-conversion operation executed.
- Buriton5 Sass rebuilt successfully using Dart Sass 1.98.0 against Bootstrap5 4.0.9; upstream Sass deprecations logged.
- STARTED: repeat content/theme checks before resolving final core 11. Snapshot prior to this preparation remains under `drupal10/`.

### Prepared Drupal 10 validation complete
- Restored original Drupal 9 database into separate local `buriton_upgrade9_verify`; verified 1,894 nodes, 897 media and 447 submissions. The working database was not restored/overwritten.
- Detailed unordered-row hashes of all 66 selected content/revision/user/media/taxonomy/file/submission tables are IDENTICAL between restored Drupal 9 and prepared Drupal 10; all managed file URI/existence/byte hashes identical, including the same 309 missing references.
- Current custom-theme runtime checks pass. Upgrade Status 5 was installed but disabled in the fresh DB; enabled locally for diagnostics. Prior command-discovery failures were caused by the disabled module, not an upstream incompatibility.
- Upgrade Status: Zircon zero findings. Buriton5 has one Drupal-12-only optional search-block empty-string deprecation, explicitly classified ignore for this Drupal 11 migration; no Drupal 11 blocker.
- STARTED: pre-core11 source/database checkpoint. Public/private upload snapshot is shared with verified `drupal10/site.tar.gz`, since all managed bytes are identical.
- Pre-core11 source/database checkpoint complete; gzip integrity and checksums verified. Restore its source/database plus public/private trees from Drupal 10 full checkpoint when rolling back to the prepared bridge.
- STARTED: resolve core ^11.4.13 with current stable contributed set. Inspect dry-run before actual update. Current working DB is still Drupal 10.6.22 with completed migrations.

### Drupal 11 core installation STARTED
- Dry-run resolves Drupal 11.4.13 with coordinated Symfony/runtime dependencies, no unexpected contributed removals/downgrades and no security advisories.
- Pre11 code/database recovery checkpoint verified; Drupal 10 database migrations are complete.
- STARTED: `composer update --with-all-dependencies --no-interaction`, log `logs/drupal11-update.txt`; then run core database migrations only after install success. Do not restore Drupal 10 code against a migrated database.

### Drupal 11 core/database migration COMPLETE
- Drupal 11.4.13 installed, Symfony Runtime plugin allowed and root `autoload_runtime.php` scaffold present; Composer reports no security advisories.
- Core database migration completed successfully (`logs/drupal11-updatedb.txt`).
- STARTED: final cache rebuild, no-pending-update check, content/file integrity collection and DDEV type `drupal11` restart. Maintenance remains enabled pending runtime checks.
- Locked Node fixture dependencies installed using `npm ci --ignore-scripts --no-audit --no-fund`.

### Final integrity and JavaScript checks passed
- Drupal 11 full-row content hashes (66 tables) and managed-file URI/existence/SHA-256 maps exactly match restored original Drupal 9. Counts and all 5 legacy hashes preserved.
- All four JavaScript regressions pass against installed Drupal 11 jQuery 4.0.0: Zircon normal/minified, Webform widget samples, Cycle/TB Mega Menu legacy-plugin fixtures.
- DDEV project type now `drupal11`; final restart in progress. Next: runtime/theme/integration checks, reviewed fresh configuration export, clean dependency rebuild and browser inspection.

### Final runtime/build verification passed; configuration reconciliation STARTED
- Zircon, Buriton5, Claro, password-service fixtures, contributed integrations, managed libraries and real status-report local assets all pass.
- Independent clean Composer install completed successfully with all four patches and runtime scaffold. Comparing patched package files next.
- Update metadata refreshed without cron; local settings made read-only; maintenance disabled; anonymous public/form/login/search/feed HTTP checks pass.
- Status has no errors. Remaining warnings concern Drupal-12 retirement of Contact/History/Stable9 and future HTML5-validation default; settings-protection warning resolved.
- Browser UI host unavailable; no graphical browser/editor/mobile acceptance claimed. JS fixtures and server rendering are verified separately.
- Original root sync differs from fresh baseline active config in exactly three pre-existing files: financial-report View and both contact Webforms. Preserve those differences while applying upgrade-generated deltas; do not overwrite their unrelated handlers/settings. Core-generated permission additions: `delete own files` for authenticated/editor/parish_council/website_editor and `rebuild node access permissions` for website_editor; record for role acceptance.
- STARTED: three-way merge of upgrade changes (initial active → final active) into existing sync exports. No configuration import and no changes to active handlers/recipients.

### Final configuration and reproducibility VERIFIED
- Three-way configuration merge: 154 written, 6 removed, zero conflicts. Exactly the three baseline export differences remain; no config import performed. Financial-report View remains database-only and both contact form exports retain prior differences.
- All 16 expanded public routes pass; 3 existing PDF/document downloads match disk SHA-256; actual private file, sync, POC, docs, tools and tmp requests return 403.
- Final Composer direct Drupal outdated list is empty; audit has zero advisories and zero abandoned packages; strict validate/platform/install dry-run pass.
- Clean rebuild matches all 60 TB Mega Menu files, 8,421 Bootstrap5 files and 3 Cycle files byte-for-byte; runtime scaffold present.
- Sanitised selected evidence copied to `docs/local-upgrade-2026-11-24/evidence/`; full private recovery material stays outside web root. Action tokens removed from current requirements evidence.
- STARTED: final checkpoint, final no-pending-update/state checks and development handover. No browser acceptance, actual-password login, form submission/mail delivery or live cron/feed refresh claimed.

### Final handover written; checkpoint close-out STARTED
- Current development history: `docs/local-upgrade-2026-11-24/README.md`. Root README and docs index link it; October POC records retained as history.
- Final local site: https://buriton9.ddev.site; Drupal 11.4.13 / PHP 8.4.27 / MariaDB 11.8.9 / Drush 13.8.1; original root layout, Zircon/Claro retained.
- STARTED: final source/settings/database checkpoint; uploaded files shared with verified full Drupal 10 checkpoint. Durable private copy planned at `/Users/aam/Buriton-upgrade-backups/2026-11-24`, outside website and temporary storage. Keep owner's off-machine backup separately.

## COMPLETE — 24 November 2026

- Local Drupal 9 → 10 → 11 upgrade completed. Final version Drupal 11.4.13, PHP 8.4.27, Drush 13.8.1, MariaDB 11.8.9.
- Site available at https://buriton9.ddev.site; maintenance OFF; local automatic cron interval 0; Mailpit retained. Production untouched; no commit/push/deployment.
- Final no-pending-update check passes. Repeated full content/file comparison after all runtime/HTTP checks remains identical to restored original Drupal 9.
- Durable private backup copy COMPLETE: `/Users/aam/Buriton-upgrade-backups/2026-11-24`. All EIGHT source/full-site/database archives verified against SHA-256 sidecars (`copy-verification.json`). Directory permissions 0700. Retain independent/off-machine backups; this is a durable local copy only.
- Final development handover: `docs/local-upgrade-2026-11-24/README.md`; selected sanitised verification evidence is alongside it. Private full-row hashes/config exports and original uploaded-file manifests stay in recovery storage.
- Remaining acceptance/follow-up: owner graphical desktop/mobile/editor/admin checks, real legacy-password login/reset and forms/CAPTCHA/delivery, controlled cron/feeds, role changes and eventual production restore/cutover rehearsal. Browser host was unavailable, not a failed site check.
- Existing issues retained explicitly: 309 pre-existing missing managed-file references; three pre-existing active/export configuration differences; Entity Clone beta exception; Drupal-12 status warnings; four maintained patches and Bootstrap 3 assets.
- No upgrade operations are pending. For future work, inspect actual state and read this record plus the current development handover; do not replay the migration against this upgraded DB.

## Follow-up — uninstall Contact and History (24 November 2026)

- User explicitly requested uninstall of deprecated core Contact and History.
- Both uninstall validators report no blockers; Drush simulation lists only these two modules.
- Existing public contact pages use retained Webforms, not core Contact. Core Contact's feedback/personal form configuration will be removed; History read-tracking data will be retired with its module.
- STARTED: private database/configuration checkpoint under `/private/tmp/buriton-contact-history-20261124`, then uninstall and reconcile only resulting configuration changes. Preserve the existing three unrelated export differences.

### Contact/History follow-up COMPLETE
- Uninstalled both requested modules; removed History tracking table and obsolete Contact forms/settings/view mode/permissions.
- Removed only stale core Contact references from Entity Clone, CAPTCHA and Honeypot. Webform contact pages and all 447 submissions retained.
- Reconciled 6 changed and 6 deleted exports; the same three pre-existing unrelated configuration differences remain.
- All 16 public HTTP checks (including both Webform contact pages) pass; no pending DB updates; no status errors or deprecated-module warning. Only Stable9 and HTML5-validation status warnings remain.
- Cleanup evidence and current handover updated under `docs/local-upgrade-2026-11-24/`.
- Fresh pre-cleanup DB backup gzip integrity verified; before/after config snapshots retained. Durable follow-up checkpoints: `/Users/aam/Buriton-upgrade-backups/2026-11-24/contact-history-cleanup`.
- Bundled Drupal module source remains Composer/core-managed; no manual code deletion, production change or deployment.
