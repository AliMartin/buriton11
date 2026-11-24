<?php

/**
 * Read-only password-service smoke check. Run using drush php:script.
 * Uses a public core test fixture, never an existing account's password/hash.
 */
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit;
}

$password = \Drupal::service('password');
// Fixture from core/modules/phpass/tests/src/Unit/PasswordVerifyTest.php.
$legacy = '$S$5TOxWPdvJRs0P/xZBdrrPlGgzViOS0drHu3jaIjitesfttrp18bk';
$valid = 'valid password';
$checks = [
  'phpass_enabled' => \Drupal::moduleHandler()->moduleExists('phpass'),
  'legacy_password_accepted' => $password->check($valid, $legacy),
  'wrong_legacy_password_rejected' => !$password->check('wrong password', $legacy),
  'legacy_hash_needs_rehash' => $password->needsRehash($legacy),
];
$new = $password->hash($valid);
$checks['modern_password_accepted'] = $password->check($valid, $new);
$checks['wrong_modern_password_rejected'] = !$password->check('wrong password', $new);
if (in_array(FALSE, $checks, TRUE)) {
  throw new \RuntimeException('Password compatibility check failed: ' . json_encode($checks));
}
echo json_encode([
  'core' => \Drupal::VERSION,
  'password_service' => get_class($password),
  'checks' => $checks,
  'existing_accounts_modified' => FALSE,
  'limit' => 'Service fixture only; an owner must still test an existing account with its original password.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
