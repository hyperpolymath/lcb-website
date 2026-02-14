#!/bin/bash
# SPDX-License-Identifier: PMPL-1.0-or-later
# SPDX-FileCopyrightText: 2025 Jonathan D.A. Jewell <jonathan.jewell@open.ac.uk>
#
# Export WordPress site for Verpex deployment

set -e

echo "=== Exporting NUJ LCB WordPress for Verpex Deployment ==="
echo ""

# Check if containers are running
if ! podman compose ps | grep -q "Up"; then
    echo "ERROR: Containers are not running. Start them with:"
    echo "  podman compose up -d"
    exit 1
fi

echo "[1/3] Exporting database..."
podman exec nuj-lcb-wordpress wp db export /tmp/wordpress-backup.sql --allow-root > /dev/null
podman cp nuj-lcb-wordpress:/tmp/wordpress-backup.sql ./nuj-lcb-backup.sql
podman exec nuj-lcb-wordpress rm /tmp/wordpress-backup.sql
echo "  ✓ Database exported: nuj-lcb-backup.sql"

echo "[2/3] Compressing database..."
gzip -f nuj-lcb-backup.sql
echo "  ✓ Database compressed: nuj-lcb-backup.sql.gz"

echo "[3/3] Exporting WordPress files..."
podman exec nuj-lcb-wordpress tar czf /tmp/wordpress-files.tar.gz -C /var/www/html .
podman cp nuj-lcb-wordpress:/tmp/wordpress-files.tar.gz ./wordpress-files.tar.gz
podman exec nuj-lcb-wordpress rm /tmp/wordpress-files.tar.gz
echo "  ✓ Files exported: wordpress-files.tar.gz"

echo ""
echo "=== Export Complete! ==="
echo ""
echo "Files ready for upload to Verpex:"
ls -lh nuj-lcb-backup.sql.gz wordpress-files.tar.gz
echo ""
echo "Next steps:"
echo "1. Follow VERPEX-DEPLOYMENT.md guide"
echo "2. Upload these files to cPanel"
echo "3. Import database and configure wp-config.php"
echo ""
echo "⚠️  SECURITY: Delete these files after uploading!"
