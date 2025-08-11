#!/bin/bash

# Contrast Agent Configuration Template
# Copy this file to config.local.sh and add your personal Contrast agent token

# Instructions:
# 1. Copy this file: cp config.template.sh config.local.sh
# 2. Edit config.local.sh and replace YOUR_BASE64_TOKEN_HERE with your actual token
# 3. The config.local.sh file is git-ignored for security

# Your Contrast agent token (Base64 encoded)
# Get this from your Contrast TeamServer: Organization Settings -> Agent
PERSONAL_AGENT_TOKEN="YOUR_BASE64_TOKEN_HERE"

# Export for use by run-demo.sh
export CONTRAST_AGENT_TOKEN="$PERSONAL_AGENT_TOKEN"