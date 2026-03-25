#!/bin/bash
# ============================================================
# startup.sh — Azure App Service Linux custom startup script
#
# Configure in Azure Portal:
#   App Service > Configuration > General Settings
#   Startup Command: /home/site/wwwroot/startup.sh
#
# This runs as root before PHP-FPM starts.
# ============================================================

set -e

# Create OPcache file-cache directory (defined in .user.ini).
# /tmp is a local tmpfs on Azure — fast, not SMB.
mkdir -p /tmp/opcache
chmod 755 /tmp/opcache

# Create PHP session directory (defined in .user.ini).
mkdir -p /tmp/php_sessions
chmod 700 /tmp/php_sessions

# Optional: warm the OPcache by touching every PHP file so
# PHP-FPM workers compile them on the first request wave
# rather than on each user's first hit.
# find /home/site/wwwroot -name '*.php' | xargs -P4 -I{} php -l {} > /dev/null 2>&1 || true

echo "startup.sh: directories ready, starting PHP-FPM"
