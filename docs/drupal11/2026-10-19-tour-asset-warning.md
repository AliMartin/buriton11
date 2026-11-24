# Status report: obsolete Tour asset warning

Investigated locally on **19 October 2026**, on Drupal **11.4.10**.

The reported warning names a missing **theme** `tour`, followed by PHP's
`dirname(NULL)` deprecation. Its trace comes through `JsAssetController` while
building a JavaScript aggregate, rather than through installation requirements.

Tour was an unused **module**, uninstalled during the Drupal 10 preparation.
It is absent from both active/exported `core.extension` and the theme selection.
The public/admin themes remain Zircon/Claro. No current Tour library attachment
was found in the checked custom themes, TB Mega Menu, admin toolbar or Claro.

Drupal's `LibraryDiscoveryParser::buildByExtension()` checks whether the library
namespace belongs to an enabled module; otherwise it tries it as a theme. An
obsolete `tour/...` library request therefore produces the misleading theme
warning. The missing pathname then causes the secondary `dirname(NULL)` notice.

## Findings and action

- A fresh administrator render of `/admin/reports/status` returned HTTP 200
  **before cache clearing**, with no PHP warnings/deprecations.
- Decoded library lists in its aggregate URLs and AJAX settings contained no
  `tour/...` library. All **eight local CSS/JavaScript requests** returned 200
  with nonempty responses and no reported Tour/path warning text.
- Ran `ddev drush cr` to rebuild server caches and aggregated-asset discovery,
  then repeated the render and real HTTP asset requests. All checks passed
  again. Active/exported configuration still matches.
- No module/theme installation, configuration edit or core patch was required.

The original request URL was not included in the pasted warning, and Database
Logging is currently disabled. The remaining watchdog table contained no
records, so there was no original aggregate request to decode. The trace and
clean current page are **consistent with an older cached page/asset request**
that still listed Tour; this origin is an inference, not a captured replay of
the user's failing browser request.

Close administration tabs left open during the upgrade and reload the current
`https://buriton11.ddev.site/admin/reports/status` page with **Cmd+Shift+R** on
macOS (or Ctrl+Shift+R). A browser's old page/back-forward state cannot be
replaced solely by rebuilding server caches. If a new warning recurs after
that, retain the exact page URL and failing JavaScript request URL/query from
the browser Network panel for comparison with the clean current library list.

## Repeat verification

The new CLI-only check exercises the status report's **actual generated asset
URLs**, extending the earlier theme check that resolved asset definitions but
did not fetch the report's aggregates:

```sh
ddev exec env THEME_QA_BASE_URL=https://buriton11.ddev.site vendor/bin/drush php:script tools/check-admin-assets.php
```

It switches account context only in the Drush process, renders Claro's status
report, rejects Tour attachments, fetches local CSS/JS URLs and captures PHP
warnings/deprecations. It exports neither inline settings nor browser/session
tokens, sends no form submissions and does not log into or modify an account.
It does not inspect an existing browser tab/cache. Results are saved in
[evidence/2026-10-19-tour-warning](evidence/2026-10-19-tour-warning/).
