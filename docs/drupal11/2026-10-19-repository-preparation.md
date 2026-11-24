# Repository preparation — 19 October 2026

Subsequent work: the owner has raised PHP to 8.4.18 and Drush/module cleanup is
complete; see [the later implementation record](2026-10-19-drush-and-module-cleanup.md).
The PHP 8.2 references below describe the earlier point in the day's work.

The owner confirmed that `sync/` had accidentally been deleted and was restored.
This explains the initial empty directory during the appraisal; its later
comparison returned no differences. No configuration import was needed.

The root `.gitignore` now matches this project's actual Drupal root layout.
It excludes downloaded core/contrib/vendor/library code, uploaded/public/private
files, database backups, temporary files, settings credentials, local overrides,
the private study archive, editor state and FTP connection settings. Existing
DDEV-generated nested exclusions continue to apply.

Composer manifests and locks, patch files and lock, custom modules/themes,
bundled theme assets, sync exports, docs, root scaffold files (including
`.htaccess`) and portable DDEV configuration remain available for versioning.
The earlier `/web/composer.json` and `/web/composer.lock` exclusion patterns
were deliberately not translated into root exclusions: these files are required
for a reproducible build. No files were deleted or staged and no commit was made.

`git check-ignore` was used to verify both excluded and retained representative
paths; results are in
[the follow-up evidence](evidence/git-ignore-check-after-2026-10-19.json).
Exclusions do not remove secrets already tracked by Git; only `.gitignore` and
`README.md` were tracked when checked, so no index cleanup was needed.

**PHP:** Drupal 11.4 requires at least PHP 8.3. Use **PHP 8.4** for the proof of
concept: it is supported by both the current Drupal 10.6 and target Drupal 11.4,
allowing the runtime change to be tested before replacing core.
[Official PHP support matrix](https://www.drupal.org/docs/getting-started/system-requirements/php-requirements).

`.ddev/config.yaml` still specifies PHP 8.2. The user requested the Git fix and
PHP guidance; the runtime has not been changed or restarted in this follow-up.
When beginning that step, use the runbook's PHP 8.4 configuration/restart and
platform/render checks. Keep `type: drupal10` until the actual core upgrade.

Private-file HTTP protection, off-machine backup/restore validation, dependency
changes and jQuery 4 compatibility remain outstanding as described in the
appraisal. Correcting Git exclusions does not resolve web access permissions.
