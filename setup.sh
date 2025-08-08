#!/bin/bash

# Quick setup script for the Contrast Flex Agent Demo
# This ensures all scripts have proper permissions regardless of host OS

set -e

echo "🔧 Setting up Contrast Flex Agent Demo..."

# Make sure all shell scripts are executable
chmod +x run-demo.sh 2>/dev/null || true
chmod +x DEMO/demo-control.sh 2>/dev/null || true

echo "✅ Scripts are now executable"
echo "🚀 Ready to run: ./run-demo.sh"
echo ""
echo "📚 See README.md for comprehensive documentation"
echo "📋 See DEMO-SUMMARY.md for quick reference"
