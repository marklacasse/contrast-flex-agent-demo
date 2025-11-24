<?php

/**
 * Drupal Vulnerable Demo - Pre-configured Settings
 */

// Database configuration - SQLite
$databases['default']['default'] = array (
  'database' => '/demos/drupal-app/web/sites/default/files/.sqlite',
  'prefix' => '',
  'namespace' => 'Drupal\\sqlite\\Driver\\Database\\sqlite',
  'driver' => 'sqlite',
);

// Salt for one-time login links, cancel links, form tokens, etc.
$settings['hash_salt'] = 'contrast-demo-insecure-salt-for-testing-only';

// Configuration directories
$settings['config_sync_directory'] = '../config/sync';

// Trusted host patterns (allow all for demo)
$settings['trusted_host_patterns'] = [
  '^.+$',
];

// File paths
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '';
$settings['file_temp_path'] = '/tmp';

// Disable CSS/JS aggregation for easier debugging
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

// Display errors for demo purposes
$config['system.logging']['error_level'] = 'verbose';

// Skip file system permissions hardening
$settings['skip_permissions_hardening'] = TRUE;

// Update settings
$settings['update_free_access'] = FALSE;

// Reverse proxy settings
$settings['reverse_proxy'] = FALSE;

// Base URL (optional, can be overridden)
// $base_url = 'http://localhost:7070';

// Entity update backup
$settings['entity_update_backup'] = TRUE;

// Migration settings
$settings['migrate_node_migrate_type_classic'] = FALSE;

// Container yamls
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
