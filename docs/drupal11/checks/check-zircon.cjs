// Load the site's own scripts against a specific Drupal core jQuery asset.
const {JSDOM, VirtualConsole} = require('jsdom');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.resolve(process.argv[2] || process.cwd());
const jquery = path.resolve(process.argv[3] || path.join(root, 'core/assets/vendor/jquery/jquery.min.js'));
const expectedVersion = process.argv[4];
assert.ok(expectedVersion, 'Supply the expected jQuery version as argument 4');
const errors = [];
const virtualConsole = new VirtualConsole();
virtualConsole.on('jsdomError', error => errors.push(error.message));
const dom = new JSDOM(`<!doctype html><html><body>
<a class="btn-btt" href="#top">Top</a>
<form id="search-block-form"><input name="keys" value=""></form>
<form id="contact-message-feedback-form"><input placeholder="Name" value=""></form>
<button id="collapse-toggle" data-toggle="collapse" data-target="#nav">Menu</button><div id="nav" class="collapse">Links</div>
<div class="dropdown"><button id="dropdown-toggle" data-toggle="dropdown">Open</button><ul class="dropdown-menu"><li><a href="#one">One</a></li></ul></div>
<div id="modal" class="modal"><div class="modal-dialog"><div class="modal-content">Content</div></div></div>
<button id="tip" title="Tooltip">Tip</button>
<div class="carousel-responsive"><div>One</div><div>Two</div><div>Three</div><div>Four</div><div>Five</div></div>
<div id="slideshow_container"><div u="loading"></div><div u="slides"><div><div class="views-field-field-slideshow"><img width="600" height="300" src="one.jpg"></div><div class="views-field-title"><a href="/one">One</a></div></div><div><div class="views-field-field-slideshow"><img width="600" height="300" src="two.jpg"></div><div class="views-field-title"><a href="/two">Two</a></div></div></div></div>
</body></html>`, {url: 'https://buriton11.ddev.site/', runScripts: 'outside-only', pretendToBeVisual: true, virtualConsole});
const w = dom.window;
try {
  w.matchMedia = query => ({matches: query.includes('prefers-reduced-motion'), addListener() {}, removeListener() {}});
  w.scrollTo = () => {};
  w.Drupal = {behaviors: {}, t: s => s};
  const load = p => w.eval(fs.readFileSync(path.join(root, p), 'utf8'));
  w.eval(fs.readFileSync(jquery, 'utf8'));
  const $ = w.jQuery;
  assert.equal($.fn.jquery, expectedVersion, 'Test the declared target core jQuery version');
  load('core/assets/vendor/once/once.min.js');
  for (const p of ['includes/js/jquery.smooth-scroll.js', 'includes/slick/slick/slick.js', 'includes/bootstrap/js/bootstrap.js', 'includes/flexslider/jquery.flexslider.js', 'js/slideshow.js', 'js/carousel.js', 'js/zircon.js']) {
    load('themes/custom/zircon/' + (process.argv[5] === '--minified' ? p.replace('bootstrap.js', 'bootstrap.min.js').replace('slick.js', 'slick.min.js') : p));
  }
  if (expectedVersion.startsWith('4.')) {
    for (const removed of ['type', 'isFunction', 'isWindow', 'camelCase', 'isArray']) {
      assert.equal($[removed], undefined, 'No global jQuery shim: ' + removed);
    }
  }
  // Disable transition timers, not the component behaviour under test.
  $.support.transition = false;
  $.fx.off = true;
  $('#collapse-toggle').trigger('click');
  assert.ok($('#nav').hasClass('in'), 'Bootstrap navigation opens');
  $('#collapse-toggle').trigger('click');
  assert.ok(!$('#nav').hasClass('in'), 'Bootstrap navigation closes');
  $('#dropdown-toggle').trigger('click');
  assert.ok($('.dropdown').hasClass('open'), 'Bootstrap dropdown opens');
  $('#dropdown-toggle').trigger('click');
  assert.ok(!$('.dropdown').hasClass('open'), 'Bootstrap dropdown closes');
  $('#modal').modal('show');
  assert.ok($('#modal').hasClass('in'), 'Bootstrap modal opens');
  $('#modal').modal('hide');
  assert.ok(!$('#modal').hasClass('in'), 'Bootstrap modal closes');
  $('#tip').tooltip({animation: false, title: () => 'Dynamic title'}).tooltip('show');
  assert.equal($('.tooltip-inner').text(), 'Tooltip', 'Bootstrap tooltip renders');
  $('#tip').tooltip('hide').tooltip('destroy');
  const form = w.document.querySelector('#contact-message-feedback-form');
  let formEvents = 0;
  $(form).on('change', () => formEvents++);
  const attach = context => Object.values(w.Drupal.behaviors).forEach(b => b.attach(context, {}));
  attach(w.document);
  attach(w.document);
  assert.equal($('.carousel-responsive > .slick-list').length, 1, 'Carousel initialises once');
  assert.ok($('#slideshow_container').data('flexslider'), 'Slideshow initialises');
  assert.equal($('#slideshow_container').data('flexslider').vars.slideshow, false, 'Reduced motion disables autoplay');
  assert.equal($('#slideshow_container ul.slides > li:not(.clone)').length, 2, 'Slideshow preserves both slides');
  assert.equal($('#search-block-form input').val(), '', 'Placeholder is not submitted as a search value');
  assert.equal($('#search-block-form input').attr('placeholder'), 'Keywords');
  assert.equal(w.document.querySelector('#contact-message-feedback-form'), form, 'Form DOM is preserved');
  $(form).trigger('change');
  assert.equal(formEvents, 1, 'Attached form handlers survive');
  assert.equal($._data(w, 'events').scroll.filter(x => x.namespace === 'zircon').length, 1, 'Window handler is not duplicated');
  $('.btn-btt').trigger('click');
  const carousel = $('.carousel-responsive');
  carousel.slick('slickFilter', ':even');
  assert.equal(carousel.slick('getSlick').slideCount, 3, 'Slick filtering uses native function checks');
  carousel.slick('slickUnfilter');
  assert.equal(carousel.slick('getSlick').slideCount, 5, 'Slick filtering restores content');
  carousel.slick('slickSetOption', 'responsive', [{breakpoint: 700, settings: {slidesToShow: 2}}], true);
  carousel.slick('slickSetOption', {slidesToScroll: 1}, true);
  assert.equal(carousel.slick('slickGetOption', 'slidesToScroll'), 1, 'Slick object options update');
  const ajax = w.document.createElement('div');
  ajax.innerHTML = '<div class="carousel-responsive"><div>Ajax one</div><div>Ajax two</div></div>';
  w.document.body.appendChild(ajax);
  attach(ajax);
  attach(ajax);
  assert.equal($(ajax).find('.slick-list').length, 1, 'AJAX carousel initialises exactly once');
  $(ajax).find('.carousel-responsive').slick('unslick');
  assert.equal($(ajax).find('.slick-list').length, 0, 'Slick destroys cleanly');
  assert.deepEqual(errors, [], 'No DOM runtime errors');
  console.log(JSON.stringify({result: 'PASS', jquery: $.fn.jquery, minified: process.argv[5] === '--minified', bootstrap: $.fn.modal.Constructor.VERSION, checks: ['collapse', 'dropdown', 'modal', 'tooltip', 'smooth-scroll click', 'Slick filter/options/destroy', 'repeated/AJAX attachment', 'slideshow content', 'reduced motion', 'search placeholder', 'form handlers']}, null, 2));
} finally {
  w.close();
}
