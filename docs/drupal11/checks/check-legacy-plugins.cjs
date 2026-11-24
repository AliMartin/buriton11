const {JSDOM} = require('jsdom');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.resolve(process.argv[2] || process.cwd());
assert.ok(process.argv[3] && process.argv[4], 'Supply a jQuery asset and expected version');
const dom = new JSDOM('<!doctype html><html><body><div id="cycle"><div>First</div><div>Second</div></div><div id="block-column"><div class="mega-inner"></div></div><div id="tb-megamenu-admin-mm-tb"><span id="toolbox-loading"></span></div></body></html>', {url: 'https://buriton11.ddev.site/', runScripts: 'outside-only', pretendToBeVisual: true});
const w = dom.window;
try {
  w.eval(fs.readFileSync(path.resolve(process.argv[3]), 'utf8'));
  const $ = w.jQuery;
  assert.equal($.fn.jquery, process.argv[4]);
  $.fx.off = true;
  w.eval(fs.readFileSync(path.join(root, 'libraries/jquery.cycle/jquery.cycle.all.js'), 'utf8'));
  const cycle = $('#cycle');
  cycle.cycle({fx: 'fade', timeout: 0, speed: 0});
  assert.ok(cycle.data('cycle.opts'), 'Cycle initialises with both slides');
  assert.equal(cycle.data('cycle.opts').slideCount, 2);
  cycle.cycle('next');
  assert.equal(cycle.data('cycle.opts').currSlide, 1, 'Cycle advances');
  cycle.cycle('destroy');
  assert.equal(cycle.data('cycle.opts'), undefined, 'Cycle destroys cleanly');
  w.Drupal = {behaviors: {}, t: s => s};
  w.drupalSettings = {TBMegaMenu: {saveConfigURL: '/not-requested'}};
  // Expose the private dispatcher in this in-memory test copy only. Its real
  // completion callback receives a stub response; no site request/save occurs.
  const installed = JSON.parse(fs.readFileSync(path.join(root, 'vendor/composer/installed.json'), 'utf8'));
  const tb = installed.packages.find(p => p.name === 'drupal/tb_megamenu');
  assert.ok(tb && tb['install-path'], 'TB Mega Menu has a Composer installation path');
  const tbPath = path.resolve(root, 'vendor/composer', tb['install-path']);
  const source = fs.readFileSync(path.join(tbPath, 'js/tb-megamenu.object.js'), 'utf8');
  const end = '})(jQuery, Drupal, drupalSettings);';
  assert.equal(source.split(end).length, 2);
  w.eval(source.replace(end, 'Drupal.TBMegaMenu.testCallAjax = callAjax;\n' + end));
  let calls = 0;
  $.ajax = options => {
    calls++;
    options.complete({status: 200, responseText: JSON.stringify({id: 'block-column', content: '<p class="loaded-block">Loaded block</p><input name="unsafe-form-field">'})});
  };
  w.Drupal.TBMegaMenu.testCallAjax({action: 'load_block', id: 'block-column', block_id: 'fixture'});
  assert.equal(calls, 1);
  assert.equal($('#block-column .loaded-block').text(), 'Loaded block', 'TB chooser parses and displays JSON');
  assert.equal($('#block-column input').attr('name'), undefined, 'Preview fields are not submitted');
  assert.equal(w.Drupal.TBMegaMenu.isLockedAjax(), false, 'AJAX lock is released');
  if (process.argv[4].startsWith('4.')) {
    assert.equal($.isFunction, undefined, 'No global helper shim');
    assert.equal($.parseJSON, undefined, 'No global JSON shim');
  }
  console.log(JSON.stringify({result: 'PASS', jquery: $.fn.jquery, checks: ['Cycle initialise/advance/destroy', 'TB block chooser response parsing/rendering', 'TB AJAX unlock', 'preview field safety']}, null, 2));
} finally {
  w.close();
}
