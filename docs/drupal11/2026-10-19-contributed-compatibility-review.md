# Contributed-project compatibility review — 19 October 2026

Subsequently implemented: [the contributed-module update](2026-10-19-contributed-module-updates.md)
has installed Font Awesome 3.0.0 and Similar By Terms 2.0.0, added the TB Mega
Menu PHP 8.4 patch and validated the selected package set. The review below
records the earlier pre-update state and its initial recommendation.

Reviewed the owner's pasted Upgrade Status report, whose scan timestamps are
18 October, against the current Composer lock and the completed cleanup.
Drupal is currently 10.6.19 on PHP 8.4.18. No dependency versions were changed
by this review, and no fresh full static scan was run.

## Decision

**Font Awesome is the one remaining contributed package whose locked core
constraint excludes Drupal 11.** It is enabled at 2.26.0 but absent from the
pasted report. Upgrade to **3.0.0 / `^3.0`** on Drupal 10, run its updates and
check icons/editor integration before changing core. The official release
supports `^10.2 || ^11.0` and includes PHP 8.4 compatibility fixes.
[Font Awesome 3.0.0 release](https://www.drupal.org/project/fontawesome/releases/3.0.0).

Every contributed project actually listed in the pasted report has an installed
core constraint that includes Drupal 10 and 11. That is metadata compatibility,
not proof of correct runtime behavior; their warning counts alone do not require
upgrading all of them. Core's own coordinated package upgrade remains separate.

## Particular findings

- **TB Mega Menu 1.10:** the reported `getContentType()` call at controller line
  84 is in the `else` branch of a `method_exists(getContentTypeFormat)` check.
  Symfony on the target uses the newer method. This is not an unconditional
  failure requiring a major module upgrade. Other form/serialization warnings
  still need review, and its real PHP 8.4 nullable-parameter notices remain.
  Test menu administration/save, block rendering/caching and mobile interaction;
  use a maintained targeted patch where needed. The project lists stable 1.10
  with Drupal 11 support; moving to its 3.0 alpha solely to clear a scan is not
  justified. [TB Mega Menu project](https://www.drupal.org/project/tb_megamenu).
- **Entity Clone 2.2.0-beta1:** already declares Drupal 10.3/11 compatibility.
  No compulsory branch upgrade is identified, but it is a beta and the project
  lists no supported stable release. Retaining it needs deliberate acceptance
  and actual cloning tests. [Entity Clone project](https://www.drupal.org/project/entity_clone).
- **Aggregator, Rules, Webform and similar high-warning projects:** many hits
  concern PHPUnit assertions/attributes in test files, unavailable optional
  integrations or newer-core attributes scanned under Drupal 10. These are
  reasons for triage in the correct environment, not a count of confirmed site
  failures. Keep manual findings visible and retest real enabled functionality.
- **Admin Toolbar:** the deprecated Links Access Filter submodule in the report
  is already uninstalled. Its files remain bundled within Admin Toolbar; its
  presence in a package-wide scan does not mean it is enabled.
- **Stable/Classy:** absent libraries belonging to uninstalled optional modules
  and deprecated Twig filters need contextual review. Do not enable CKEditor 4,
  Quick Edit or other retired modules merely to make those scan messages vanish.

## Current package inventory

Taken from the current lock; requirements below are package declarations.
The report itself omits Font Awesome and Upgrade Status.

| Package | Locked version | Declared core requirement | Action |
| --- | --- | --- | --- |
| `drupal/admin_toolbar` | 3.6.3 | `^9.5 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/aggregator` | 2.2.6 | `^10.2 || ^11 || ^12` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/backup_migrate` | 5.1.5 | `^10.3 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/bootstrap5` | 4.0.8 | `^10.1 || ^11.0` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/captcha` | 2.0.10 | `^9.5 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/classy` | 2.0.3 | `^10.3 || ^11 || ^12` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/ctools` | 4.1.1 | `^9.5 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/entity_clone` | 2.2.0-beta1 | `^10.3 || ^11` | Already declares 11 support; beta dependency needs explicit acceptance and cloning tests |
| `drupal/fitvids` | 2.1.0 | `^9.2 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/fontawesome` | 2.26.0 | `^9.4 || ^10` | Upgrade to ^3.0 before core 11; current version excludes 11 |
| `drupal/honeypot` | 2.2.2 | `^10.3 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/pathauto` | 1.15.0 | `^10.2 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/recaptcha` | 3.5.0 | `^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/redirect` | 1.13.0 | `^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/rules` | 4.0.1 | `^10.3 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/similarterms` | 1.10.0 | `^8 || ^9 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/stable` | 2.1.0 | `^10.3 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/tb_megamenu` | 1.10.0 | `^8 || ^9 || ^10 || ^11` | Keep 1.10 initially; review/patch PHP 8.4 notices and test menu rendering, caching and admin save |
| `drupal/token` | 1.17.0 | `^10.3 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/typed_data` | 2.1.1 | `^10.3 | ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/upgrade_status` | 4.3.10 | `^9 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/views_slideshow` | 5.0.1 | `^9 || ^10 || ^11` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |
| `drupal/webform` | 6.3.1 | `^10.3 || ^11.0` | Keep for the rehearsal; review remaining findings and test on Drupal 11 |

## Next sequence

Upgrade and validate Font Awesome first, review TB Mega Menu's remaining real
issues, then refresh Upgrade Status results rather than relying on cached
18 October findings. Resolve the separate **Zircon Bootstrap 3 / target jQuery 4
conflict**, complete the custom-theme compatibility declarations and run the
local core 11 rehearsal. Recheck full browser/editor/form/feed behavior on that
runtime before accepting compatibility. Bootstrap5, Classy and Stable already
declare core 11 support; the installed custom themes require their own work.

The Drupal 11 dependency precheck after cleanup is retained under
`evidence/2026-10-19-cleanup/remaining-drupal11-blockers.stdout.txt`; its
contributed-module blocker is Font Awesome, alongside the expected core 10 and
locked-transitive dependency constraints.
