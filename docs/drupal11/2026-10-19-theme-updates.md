# Custom-theme preparation for Drupal 11

**Later path update:** the patched TB Mega Menu and Bootstrap5 packages now
install under `custom`; see [the relocation record](2026-10-19-custom-paths.md).
The evidence and directory references below retain their original checkpoint.

Completed locally on **19 October 2026**, with Drupal **10.6.19**, PHP **8.4.18**
and Drush **13.8.0**. Zircon remains the default theme, Buriton5 the installed
alternative and Claro the administration theme. Drupal 11 has **not** been
installed. These checks prepare the themes for the core rehearsal; they do not
replace that rehearsal or the owner's visual/accessibility acceptance.

## Changes

- Zircon declares `^9.5 || ^10 || ^11`; Buriton5 declares
  `^9.5 || ^10.1 || ^11`. Their explicit base-theme dependencies remain intact.
  Drupal 9 declarations are retained from the previous project; this step did
  not rerun Drupal 9 tests.
- Zircon's Bootstrap JavaScript is now **Entreprise7pro Bootstrap 3.4.9**,
  copied unchanged from commit `f4de277e03cd7dd5753d40b127d0c6de2d06789e`.
  Both normal/minified files and its MIT licence are included. This maintained
  fork is also used by Drupal's Bootstrap theme and supports jQuery 3 and 4.
  The bundle's component constructors still report `3.4.6`; that is upstream's
  identifier, not evidence that the wrong archive was installed.
- Bootstrap **CSS/fonts remain 3.4.1**. Zircon's styles, templates and 3/6/3
  layout were not rebuilt. No second copy of jQuery, global compatibility shim
  or jQuery Migrate was introduced.
- Slick **1.8.1** uses native array/string/object checks in its normal and
  minified scripts in place of the removed `$.type()`. The local patch is kept
  in [checks/slick-jquery4.patch](checks/slick-jquery4.patch); the theme sources
  are already patched, so do not apply it again to this checkout.
- Zircon's library version advances from `1.1.0` to `1.2.0` for cache invalidation.
- Buriton5's parent remains Bootstrap5 **4.0.8**, which already declares Drupal
  11 support. Its parent-library override and compiled CSS are unchanged.

The wider JavaScript check also identified two contributed frontend failures:
Cycle **3.0.3** uses removed `$.isFunction()` calls, and TB Mega Menu **1.10.0**
uses removed `$.parseJSON()` in its administration block chooser. These now have
small **Composer-managed patches**, using native `typeof` and `JSON.parse()`:

| Package | Patch |
| --- | --- |
| `malsup/jquery.cycle` | `patches/jquery-cycle-jquery4.patch` (full and lite distributions) |
| `drupal/tb_megamenu` | `patches/tb-megamenu-jquery4-json.patch` |

These are additional to the existing Bootstrap5 base-theme-preservation and
TB Mega Menu PHP 8.4 patches. All four are locked by checksum in
`patches.lock.json`. `composer patches-repatch` successfully reinstalled the
three affected packages and applied all four patches. The Composer lock was
refreshed without changing package versions. Retire these patches only after
an upstream release provides the equivalent fixes and the checks still pass.

## Evidence and limits

Results are in [evidence/2026-10-19-theme-updates](evidence/2026-10-19-theme-updates/).

- Before the changes, the expanded Zircon check passed with jQuery **3.7.1**
  but threw Bootstrap's version-guard error with **4.0.0**. The Cycle check
  independently reproduced `$.isFunction is not a function` with jQuery 4.
- Afterwards the same Zircon assertions pass with the installed Drupal 10
  jQuery and the actual **Drupal 11.4.10 jQuery 4.0.0 asset**, loaded separately
  into the fixture. The site's core assets were not replaced. The minified
  Bootstrap/Slick variants also pass with jQuery 4.
- Coverage includes Bootstrap collapse/dropdown/modal/tooltip, a Smooth Scroll
  click, Slick filter/options/destroy, repeated/AJAX attachment, slideshow
  content, reduced-motion autoplay, search placeholders and form-handler
  preservation. The checks assert no global replacements of removed helpers.
- Webform library samples initialise Inputmask, Select2, telephone input,
  timepicker and Choices on both versions. Textcounter, CodeMirror and
  SignaturePad exports are checked; their full interactions are not exercised.
- Cycle initialisation/advance/destroy and TB's real block-response completion
  callback pass on both versions. The latter uses a test-only exposure of its
  private dispatcher and a stub response: it sends no HTTP request or save.
- Fresh Upgrade Status scans of **both custom themes return `[]`**.
- Before/after baseline sections are identical except the two intended theme
  requirements. Recorded data counts/fingerprints, enabled modules, default/admin
  themes and the 536 active/exported configurations are unchanged. Package
  inventories are identical; only Composer's lock content hash changed.
- Zircon, Buriton5 and Claro pass Drupal 10 Twig/render/layout/asset checks;
  managed-library checks and contributed-module integration checks pass.

The JavaScript checks run in **jsdom**, not a graphical browser. They do not
prove pixel appearance, touch/keyboard accessibility, every plugin option or
the complete Drupal 11 AJAX/application lifecycle. Repeat the checks using
the installed core asset after the core upgrade, then complete desktop/mobile
browser and editor testing in [the acceptance sheet](acceptance-and-handover.md).
In particular, exercise TB's full administrative UI (including external Chosen),
actual Views Slideshow integration and both real contact forms. The Bootstrap
fork remains a dependency to track; this is not a Bootstrap 5 redesign.

## Reproduce or replay

Run from the repository root. Use Node 18+ (tested with Node 24).

```sh
ddev exec npm --prefix docs/drupal11/checks ci --ignore-scripts --no-audit --no-fund
ddev exec node docs/drupal11/checks/check-zircon.cjs . core/assets/vendor/jquery/jquery.min.js 3.7.1
ddev exec node docs/drupal11/checks/check-libraries.cjs . core/assets/vendor/jquery/jquery.min.js 3.7.1
ddev exec node docs/drupal11/checks/check-legacy-plugins.cjs . core/assets/vendor/jquery/jquery.min.js 3.7.1
```

For the target probe before installing core 11:

```sh
mkdir -p tmp/theme11
ddev exec curl -fsSL https://git.drupalcode.org/project/drupal/-/raw/11.4.10/core/assets/vendor/jquery/jquery.min.js -o tmp/theme11/jquery-drupal-11.4.10.min.js
ddev exec node docs/drupal11/checks/check-zircon.cjs . tmp/theme11/jquery-drupal-11.4.10.min.js 4.0.0
ddev exec node docs/drupal11/checks/check-zircon.cjs . tmp/theme11/jquery-drupal-11.4.10.min.js 4.0.0 --minified
ddev exec node docs/drupal11/checks/check-libraries.cjs . tmp/theme11/jquery-drupal-11.4.10.min.js 4.0.0
ddev exec node docs/drupal11/checks/check-legacy-plugins.cjs . tmp/theme11/jquery-drupal-11.4.10.min.js 4.0.0
ddev drush upgrade_status:analyze zircon buriton5
```

After the core switch, use `core/assets/vendor/jquery/jquery.min.js` and its
actual expected version (4.0.0 for the recorded target) for all three checks.

Production replay takes the maintained custom themes **from this checkout**,
plus Composer manifests/lock, all patch files and `patches.lock.json`. Ordinary
`composer install` builds the contributed code and applies the managed patches.
It does not download or rebuild the committed custom-theme assets.

To reconstruct the vendored Bootstrap JS, download the pinned archive URL in
[asset-provenance.json](evidence/2026-10-19-theme-updates/asset-provenance.json),
verify its SHA-256, and copy only `dist/js/bootstrap.js`,
`dist/js/bootstrap.min.js` and `LICENSE` into Zircon's existing Bootstrap folder.
Leave CSS/fonts unchanged. For a clean upstream Slick 1.8.1 tree, apply
`patch -p1 < docs/drupal11/checks/slick-jquery4.patch` from the repository root.
Verify the final source checksums recorded with the evidence.

Before theme editing, the custom themes, tools and affected handover documents
were archived outside the web root; its private path is recorded in ignored
`tmp/theme11-backup-location.txt`. This step changed code and analysis/cache
records only; it required no database updates or configuration export. For the
real core upgrade, retain a matching code/database/upload backup as directed by
the runbook. No theme switch, content edit, deployment or Git commit was made.

Sources: [Drupal Bootstrap's maintained-fork guidance](https://www.drupal.org/project/bootstrap),
[pinned fork](https://github.com/entreprise7pro/bootstrap/tree/f4de277e03cd7dd5753d40b127d0c6de2d06789e),
[jQuery 4 upgrade guide](https://jquery.com/upgrade-guide/4.0/).
