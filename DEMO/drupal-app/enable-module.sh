#!/bin/bash
# Simple script to enable contrast_vulnerable module

cd /demos/drupal-app/web

# Get current module list from database
CURRENT_MODULES=$(sqlite3 sites/default/files/.sqlite "SELECT data FROM config WHERE name='core.extension'" 2>/dev/null)

# Check if module is already enabled
if echo "$CURRENT_MODULES" | grep -q "contrast_vulnerable"; then
    echo "Module 'contrast_vulnerable' is already enabled"
    exit 0
fi

# Use PHP to update the config
php << 'PHPCODE'
<?php
$db = new PDO('sqlite:/demos/drupal-app/web/sites/default/files/.sqlite');

// Get current config
$stmt = $db->query("SELECT data FROM config WHERE name='core.extension'");
$blob = $stmt->fetchColumn();
$config = unserialize($blob);

// Add our module
$config['module']['contrast_vulnerable'] = 0;

// Sort modules
ksort($config['module']);

// Save back
$serialized = serialize($config);
$update = $db->prepare("UPDATE config SET data = :data WHERE name = 'core.extension'");
$update->execute([':data' => $serialized]);

echo "Module 'contrast_vulnerable' has been enabled!\n";
echo "Note: You may need to clear cache for routes to work.\n";
?>
PHPCODE

# Clear Drupal cache by truncating cache tables
echo "Clearing cache..."
for table in $(sqlite3 sites/default/files/.sqlite "SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 'cache_%'" 2>/dev/null); do
    sqlite3 sites/default/files/.sqlite "DELETE FROM $table" 2>/dev/null
done

echo "Done! Visit http://localhost:7070/contrast-demo/info to test"
