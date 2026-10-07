#!/bin/bash

# ==============================================================================
# Sync supportportal_dev to supportportal (without deleting existing files)
# ==============================================================================

set -e

SOURCE_DIR="/var/www/html/supportportal_dev"
DEST_DIR="/var/www/html/supportportal"

echo "=================================================="
echo "🚀 Starting sync from $SOURCE_DIR to $DEST_DIR"
echo "🕒 Timestamp: $(date)"
echo "=================================================="

# Check if source directory exists
if [ ! -d "$SOURCE_DIR" ]; then
    echo "❌ Source directory does not exist: $SOURCE_DIR"
    exit 1
fi

# Check if destination directory exists
if [ ! -d "$DEST_DIR" ]; then
    echo "❌ Destination directory does not exist: $DEST_DIR"
    exit 1
fi

# Rsync without --delete to ensure destination files are not deleted
echo "📦 Syncing files..."
rsync -av --progress \
    --exclude='.env' \
    --exclude='.git/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/' \
    --exclude='node_modules/' \
    --exclude='public/firmware/' \
    --exclude='scratch/' \
    "$SOURCE_DIR/" "$DEST_DIR/"

echo "🧹 Clearing and rebuilding cache in $DEST_DIR..."
cd "$DEST_DIR"
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan clear-compiled || true
php artisan config:cache || true

echo "🔒 Setting proper permissions for www-data..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "=================================================="
echo "✅ Sync completed successfully at $(date)"
echo "=================================================="
