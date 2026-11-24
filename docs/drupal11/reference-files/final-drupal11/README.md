# Final Drupal 11 POC manifests and locks — 19 October 2026

This checkpoint records the final `^11.4.10` policy with **Drupal 11.4.10**
locked, including the custom installation paths and all four managed patches.
No package versions changed when widening the core constraints.

These are reference files, not a standalone Composer project. Copy/review them
at the actual project root together with `patches/`, maintained theme sources,
QA tools and configuration. The Webform library merge needs its normal install
workflow; do not run Composer inside this documentation directory.

Use `composer install` with a reviewed tested lock for deployment. Recheck
supported releases/platform/package constraints and test any newer lock during
the fresh production rehearsal. Read [the final handover](../../poc-handover.md)
for source/data/backup requirements and all remaining acceptance items.
