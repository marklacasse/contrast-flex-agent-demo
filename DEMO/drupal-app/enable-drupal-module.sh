#!/bin/bash
# Enable contrast_vulnerable module by directly updating Drupal config

cd /demos/drupal-app/web

DB_FILE="sites/default/files/.sqlite"

echo "Enabling contrast_vulnerable module..."

# Use PHP to properly handle Drupal's serialized config
php << 'PHPCODE'
<?php
$db = new PDO('sqlite:/demos/drupal-app/web/sites/default/files/.sqlite');

// Get current config
$stmt = $db->query("SELECT data FROM config WHERE name='core.extension'");
$blob = $stmt->fetchColumn();
$config = unserialize($blob);

// Check if already enabled
if (isset($config['module']['contrast_vulnerable'])) {
    echo "Module 'contrast_vulnerable' is already enabled\n";
    exit(0);
}

// Add our module with proper weight (0 for most modules)
$config['module']['contrast_vulnerable'] = 0;

// Sort modules alphabetically
ksort($config['module']);

// Update module list in database
$serialized = serialize($config);
$update = $db->prepare("UPDATE config SET data = :data WHERE name = 'core.extension'");
$update->execute([':data' => $serialized]);

// Clear router cache by deleting from router table
$db->exec("DELETE FROM router");
$db->exec("DELETE FROM cache_bootstrap");
$db->exec("DELETE FROM cache_config");
$db->exec("DELETE FROM cache_container");
$db->exec("DELETE FROM cache_discovery");
$db->exec("DELETE FROM cache_entity");
$db->exec("DELETE FROM cache_menu");

echo "✓ Module 'contrast_vulnerable' has been enabled!\n";
echo "✓ Router cache cleared\n";
?>
PHPCODE

echo ""
echo "Module enabled successfully!"
echo "Visit these endpoints:"
echo "  http://localhost:7070/contrast-demo/info"
echo "  http://localhost:7070/contrast-demo/sql"
echo "  http://localhost:7070/contrast-demo/xss"
echo "  http://localhost:7070/contrast-demo/cmd"
echo "  http://localhost:7070/contrast-demo/path"
echo "  http://localhost:7070/contrast-demo/upload"
