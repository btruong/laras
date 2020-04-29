<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config/sync';

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * Determine the current server enviroment. This is used to apply the desired
 * Config Split profile(s).
 */
if (defined('PANTHEON_ENVIRONMENT')) {
  switch (PANTHEON_ENVIRONMENT) {
    case 'dev':
    case 'test':
    case 'live':
      $config['server_environment'] = PANTHEON_ENVIRONMENT;
      break;

    case 'lando':
      $config['server_environment'] = 'local';
      break;

    // Multidev
    default:
      $config['server_environment'] = 'dev';
      break;
  }
}
else {
  $config['server_environment'] = 'local';
}

/**
 * Config Split settings
 * - Dev Config Split (config/split/dev)
 * - Local Config Split (config/split/local)
 *
 * @see /config/README.md
 */
// Live and Test: Disable Dev & Local Config Splits
if (in_array($config['server_environment'], ['live', 'test'])) {
  $config['config_split.config_split.dev']['status'] = FALSE;
  $config['config_split.config_split.local']['status'] = FALSE;
}
// Local: Enable Dev & Local Config Splits.
else if (in_array($config['server_environment'], ['lando', 'local'])) {
  $config['config_split.config_split.dev']['status'] = TRUE;
  $config['config_split.config_split.local']['status'] = TRUE;
}
// Dev and Multidev: Enable Dev and Disable Local Config Splits
else {
  $config['config_split.config_split.dev']['status'] = TRUE;
  $config['config_split.config_split.local']['status'] = FALSE;
}

/**
 * Always install the 'standard' profile to stop the installer from
 * modifying settings.php.
 */
$settings['install_profile'] = 'standard';

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

$config['image.settings']['allow_insecure_derivatives'] = TRUE;

/**
 * HTTP Client config.
 *
 * @see https://www.jeffgeerling.com/blog/2016/increase-guzzle-http-client-request-timeout-drupal-8
 */
$settings['http_client_config']['timeout'] = 60;