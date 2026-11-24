# Buriton Zircon

This is Buriton's maintained custom copy of Zircon, declaring Drupal 9.5, 10 and 11 compatibility.
The current preparation was checked on Drupal 10.6.19 and against the jQuery
4.0.0 asset from Drupal 11.4.10; full Drupal 11 site acceptance is still pending.
The duplicate `drupal/zircon` Composer package is intentionally removed: it had
the same machine name and did not supply the custom theme actually used here.

Classy remains the base theme to preserve existing markup and styling. Composer
explicitly supplies contributed Classy and Stable for Drupal 10 and 11.
Drupal 9 continues to use its core copies of those base themes.

The custom scripts use Drupal behaviors and `core/once`. They support AJAX
attachment without duplicate listeners or slider instances. The old form DOM
replacement and placeholder polyfill have been removed. FlexSlider supplies its
own touch support and pause/play controls; reduced-motion preferences disable
automatic playback.

Bundled assets loaded by `zircon.libraries.yml`:

| Library | Version | Upstream package |
| --- | --- | --- |
| Bootstrap JavaScript | Entreprise7pro 3.4.9 | https://github.com/entreprise7pro/bootstrap/tree/f4de277e03cd7dd5753d40b127d0c6de2d06789e |
| Bootstrap CSS/fonts | 3.4.1 | https://registry.npmjs.org/bootstrap/-/bootstrap-3.4.1.tgz |
| FlexSlider | 2.7.2 | https://registry.npmjs.org/flexslider/-/flexslider-2.7.2.tgz |
| Slick | 1.8.1 + local jQuery 4 patch | https://registry.npmjs.org/slick-carousel/-/slick-carousel-1.8.1.tgz |

Their licence headers and font resources are preserved. The Bootstrap JavaScript comes byte for byte from the maintained Entreprise7pro
fork; its component constructors still identify themselves as 3.4.6. The existing
Bootstrap 3 CSS/fonts retain the layout. Slick uses native type checks instead
of the removed `$.type()` API in both normal and minified files. See the
[Drupal 11 theme record](../../../docs/drupal11/2026-10-19-theme-updates.md)
for pinned sources, patch replay and regression commands. A Bootstrap 5 redesign
is a separate transition.
The two Bootstrap major versions must not be loaded together on the same page.

Run the rendering and asset check without changing the site's selected theme:

```sh
ddev exec env THEME_QA_THEME=zircon THEME_QA_BASE_URL=https://buriton11.ddev.site vendor/bin/drush php:script tools/check-theme.php
```
