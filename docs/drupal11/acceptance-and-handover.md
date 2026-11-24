# Acceptance and production handover

Recorded 18 October; closed out 19 October 2026. **The technical Drupal
9 → 10 → 11 POC is complete at the owner's request.** Drupal 11.4.10 is installed
and the recorded local checks pass. This checklist carries the remaining
workflow/hosting/cutover acceptance forward to the real production rehearsal;
it does not retrospectively claim browser tests that were not performed.

Start with [the final POC handover](poc-handover.md), then use the two replay
runbooks against a fresh live snapshot. The core constraints are now
`^11.4.10`; the tested lock still installs exactly 11.4.10.

## Production rehearsal acceptance and POC evidence

| Check | Baseline / expected result | Drupal 11 result |
| --- | --- | --- |
| Version/platform | Supported 11.x patch, agreed PHP, all platform checks pass | PASS: 11.4.10 / PHP 8.4.18 |
| Database updates/status | No pending updates, missing extensions or unexplained status errors | PASS; nonblocking warnings documented |
| Dependency build | Reviewed fixed lock, patches apply, clean install reproduces required libraries/runtime scaffold | Upgrade/install dry run and scaffold pass; final clean deployment build pending |
| Configuration | Reviewed module/theme/editor changes; no unintended handler/recipient/permission changes | PASS: 534 synced; core-generated editor permission addition documented |
| Data preservation | Compare fresh node/user/file/media/submission/feed counts and recorded fingerprints before refresh/editing | PASS; 1,726 files unchanged, 292 already missing before upgrade |
| Legacy login | Existing `$S$` account logs in with its original password and upgrades its hash; reset flow works through Mailpit | Pending |
| Roles/access | Anonymous, authenticated, editor/admin access; direct private-file denial and authorised application download | Pending |
| Editing | CKEditor 5 create/edit/save/reopen; images/media, links, source editing, representative existing iframe content | Pending |
| Forms | Both real contact forms validate, CAPTCHA/spam protection works, configured handlers run safely, submissions retained | Pending |
| Rules | Inspect and exercise `new_comment` behavior without real external mail | Pending |
| Navigation/assets | Desktop/mobile menus, search, sidebars, footer, carousel, slideshow, keyboard/touch behavior; no console errors or missing assets | Pending |
| Actual jQuery 4 | Target core version asserted in fixture; Bootstrap and all loaded plugins function on it | Installed core asset fixtures pass; full browser integration pending |
| Themes | Zircon, Buriton5 and Claro PHP checks pass; visual comparison against the original copy accepted | PHP/render/layout checks pass; owner visual acceptance pending |
| Reports/documents | Minutes, agendas, finances, annual reports and representative attached files open correctly | Pending |
| Routing | Aliases/redirects, pager/filter controls, taxonomy, search, login and RSS work | Pending |
| Feeds/cron | Existing feed definitions/items preserved; controlled refresh adds expected results; outgoing RSS valid | Pending |
| Operations | Controlled cron, backup/restore, mail interception and logs checked; local overrides documented | Pending |
| Accessibility | Agreed keyboard, focus, zoom, contrast/form-label/mobile checks; existing versus introduced issues recorded | Pending |
| Recovery | Matching pre-stage code/database/files restored successfully on an isolated copy | Separate DB import and file samples verified; full restored-site boot/off-machine copy pending |
| Owner review | Technical POC closure; explicit owner/editor workflow acceptance before production | Owner requested POC closure on 19 October; production workflow sign-off pending |

Data counts and HTTP 200s alone do not prove correctness. The baseline hashes
cover only the fields named in `baseline.json`; nodes are fingerprinted by
identity/type, not their full body/revision history. Check representative
content and uploaded-file existence/bytes separately. Compare private exports
for stronger content-integrity evidence where needed; keep them out of Git.

Representative routes from the actual configuration include `/home`, `/news`,
`/events`, `/parish-council-minutes`, `/parish-council-agendas`,
`/parish-council-finances`, `/parish-council-financial-reports`,
`/planning-committee-minutes`, `/county-councillor-reports`,
`/district-councillor-reports`, `/aggregator/sources`, `/aggregator/rss` and
`/rss.xml`. Test the real form URLs/aliases after inspecting the active site.

## Record as implementation proceeds

Append dated entries to a new implementation record in this folder. Each entry
should state the change, reason, exact command or patch, outcome and evidence
file. Do not overwrite the pre-upgrade baseline to make results look current.

| Handover item | Required record |
| --- | --- |
| Starting source/data | Commit or archive identifier; capture date; private backup location reference |
| Prepared Drupal 10 bridge | Exact lock/source reference and completed update output |
| Final Drupal 11 release | Exact core/PHP/database/Composer/Drush versions; source commit and lock hashes |
| Package decisions | Added/upgraded/removed packages, actual usage checks and any replacement functionality |
| Theme/JavaScript work | Patches/source changes, upstream basis/licences, build commands, jQuery 4 tests and maintenance ownership |
| Configuration | Reviewed before/after changes and environment overrides; explain differences from current live site |
| Data evidence | Before/after counts/fingerprints; explanation of feed refresh or intentional content changes |
| Test evidence | Date, environment, tester, pass/fail, screenshots/logs where helpful, accepted exceptions |
| Recovery | Restore procedure actually exercised, duration and matching backup identifiers |
| Unresolved work | Risk, operational impact, owner and next action; no hidden scanner suppressions |

## Before quoting or deploying the real upgrade

The live site may have changed since the copied study data. Repeat the entire
Drupal 9 → 10 → 11 sequence on a fresh private live snapshot and time the replay.
Retain the Drupal 10 intermediate state; its completed database updates cannot
be skipped. Verify the clean build from source/locks in a separate environment.

Confirm with the host: web and CLI PHP versions/extensions, database support,
Composer/build access, writable/private paths, URL rewrites, trusted hosts,
nginx/Apache protections, cron ownership, outbound mail, backup retention and
restore access. Replace `.cpanel.yml`'s blanket copy with a reviewed deployment
artifact. Exclude docs, private backups/data, local settings and development
tools; preserve required scaffold/dotfiles and runtime dependencies. Review the
change rather than automatically copying this entire working directory.

Agree the content freeze, cutover window, rollback trigger/deadline, responsible
operator, stakeholder signoff and post-launch checks. Restore all matching
components if rollback is required; do not downgrade code against migrated data.

A fixed quote should separate completed POC investigation/compatibility work
from new dependency/patch review, fresh-data replay, host work,
functional/accessibility testing, cutover and post-launch support. Full theme
redesign, broader accessibility remediation and hosting migration need explicit
scope. Do not give a fixed production effort from a successful Composer solver
or from this appraisal alone. The previous
[estimate worksheet](../feasibility-study/04-estimating-and-acceptance.md)
remains useful once measured rehearsal results are available.

Council-facing position after technical POC closure:

> The Drupal 9-to-10-to-11 upgrade has been demonstrated on a local copy,
> retaining the existing website and recorded data. The technical findings,
> compatibility fixes and replay/rollback instructions are documented. The
> real upgrade requires a rehearsal against fresh live data, hosting and
> deployment preparation, workflow acceptance and an agreed cutover plan.
