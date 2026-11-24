# Tour aggregate warning investigation

See [the investigation record](../../2026-10-19-tour-asset-warning.md).

- `admin-assets-before.json`: fresh status report and eight local HTTP assets
  already pass without Tour references before cache rebuilding.
- `admin-assets-after.json`: same checks pass after `ddev drush cr`.
- `config-status.json`: no differences between active/exported configuration.
- `checksums.sha256`: source, evidence and investigation documentation hashes.

These checks establish the current server-rendered page and generated assets
are clean. They do not capture the user's original failing request or inspect
an existing browser cache. The old-request explanation remains an inference
from the supplied stack trace and current clean library lists. No credential,
session token, inline settings, raw content or SQL dump is included.
