# Custom extension paths — 19 October 2026

See [the relocation record](../../2026-10-19-custom-paths.md).

- `source-before.json` and `source-preservation.json`: relative file hashes and
  unchanged contents after reinstall; old contributed locations absent.
- `resolved-paths.json`: Drupal discovery and Composer metadata both select
  `modules/custom/tb_megamenu` and `themes/custom/bootstrap5`.
- `composer-lock-refresh.txt`, `composer-reinstall.txt`: path-aware reinstall
  and patch application; no package version updates.
- `composer-validate.txt`, `composer-install-dry-run.txt`: valid manifest/lock
  and no outstanding dependency installation work.
- `sass-build.txt`, `sass-output-comparison.json`: Buriton5 compiles against the
  moved parent; resulting CSS matches the existing output except source-map
  comment. Existing compiled CSS was not replaced.
- `theme-*.json`, `contrib.json`, `libraries.json`,
  `javascript-legacy-plugins.json`, `http.json`: render/asset/integration and
  public GET checks after moving the projects.
- `baseline-{before,after}.json`, `config-{before,after}.json`,
  `preservation-summary.json`: unchanged data/configuration/runtime baseline,
  package versions and patch lock; maintenance mode off.

The before-source archive is outside the web root and its path is recorded in
ignored `tmp/custom-paths-backup-location.txt`. No database dump, private file
or credential is included here. `checksums.sha256` records the current modified
source, manifests/locks, documentation and evidence, not generated dependencies.
