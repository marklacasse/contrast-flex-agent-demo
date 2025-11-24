#!/bin/bash

# Drupal Demo Application Stop Script
echo "Stopping Drupal application..."

# Stop Apache
service apache2 stop

echo "✅ Drupal application stopped"
