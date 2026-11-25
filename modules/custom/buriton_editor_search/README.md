# Editor content search

Content → Search the site (`/admin/content/search-the-site`) searches node
titles and stored text fields, including body text and summaries. It includes
all content types and searches published and unpublished content by default.
Each whitespace-separated word must match somewhere in the item's text fields.
Results show the publication status and are paginated, 25 per page, with the
most recently updated items first. It does not search inside attached files.

The `search all buriton content` permission is assigned to Website Editor in
the exported configuration. Drupal administrators also have access. Entity
access checks still apply; the Website Editor role already has permission to
view unpublished content. Public search and its index are unchanged.

Deploy the module source and import the configuration to enable the module and
role permission. Clear Drupal caches after deployment.

Run `ddev drush php:script tools/check-editor-search.php` to check role access,
menu placement, defaults, keyword matching, publication filters, and submission.
The check creates temporary content in a database transaction and rolls it back.
