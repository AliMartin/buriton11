# Theme/frontend preparation evidence — 19 October 2026

The site remains Drupal 10.6.19 / PHP 8.4.18 / Drush 13.8.0. Results apply to
the sources recorded in `checksums.sha256`, not an installed Drupal 11 site.
See [the implementation record](../../2026-10-19-theme-updates.md).

| Files | What they show |
| --- | --- |
| `baseline-{before,after}.json`, `preservation-summary.json` | Only the two custom-theme core requirements changed in the baseline; recorded data fingerprints/counts and application selections are unchanged |
| `config-{before,after}.json` | No differences between active and exported configuration |
| `custom-theme-changes.json` | Before/after hashes for modified existing custom-theme files; new Bootstrap fork licence is also included in the source manifest |
| `asset-provenance.json`, `bootstrap-package-metadata.json` | Pinned Bootstrap fork release/commit, byte-identical JS files, target jQuery source and download checksums |
| `javascript-before-jquery*.txt` | Original Zircon passes with jQuery 3 but rejects actual jQuery 4 |
| `javascript-after-jquery*.json`, `javascript-minified-jquery4.json` | Updated theme regression checks pass on both versions, including minified Bootstrap/Slick on jQuery 4 |
| `widgets-jquery*.json` | Selected Webform library/API tests pass on both versions |
| `legacy-plugins-before-jquery4.txt`, `legacy-plugins-jquery*.json` | Cycle failure before patch; Cycle and stubbed TB block chooser completion pass after patch |
| `upgrade-status.json` | Fresh scans of Zircon and Buriton5 report no findings (`[]`) |
| `theme-*.json` | Twig, routes, layout and resolved assets pass for Zircon, Buriton5 and Claro on Drupal 10 |
| `libraries.json`, `contrib.json` | Managed assets and contributed integration checks pass after patch reinstall |
| `patches-repatch.txt` | Four managed patches apply to three reinstalled packages; its initial stale-lock warning was resolved by the subsequent lock refresh |
| `composer-lock-refresh.txt`, `composer-validate.txt`, `composer-install-dry-run.txt` | Lock hash updated without version changes; strict validation and install dry run pass |
| `maintenance-state.json`, `homepage-status.txt` | Maintenance remains scalar zero and local homepage returns HTTP 200 |

`database-updates.json` records the successful no-pending-updates result. Drush
prints that message on stderr rather than emitting a JSON body when none exist.

The DOM fixtures use jsdom 26.1.0 with locked dependencies and Node 24. They
load the actual supplied jQuery file and assert its version. They do not replace
full Drupal 11 runtime, browser, accessibility, actual form submission or
external Chosen/TB administration testing. No content, credentials or database
dump is included. Original theme source backup lives outside the web root.
