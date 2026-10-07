#!/bin/bash

# Laravel web backup directory
BACKUP_DIR="/data/backup/supportportal/web"

# Create directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

DATESTAMP=$(date +%d-%m-%y-%H-%M)
BACKUP_FILE="${BACKUP_DIR}/laravel-${DATESTAMP}.tar.gz"

# Create backup (Laravel project)
tar --exclude=".git" \
    --exclude="vendor" \
    --exclude="node_modules" \
    --exclude="storage/logs" \
    --exclude="storage/framework/cache" \
    --exclude="storage/framework/sessions" \
    --exclude="storage/framework/views" \
    --exclude=".cache*" \
    --exclude="bootstrap/cache" \
    --exclude="/data/backup" \
    -zcvf "$BACKUP_FILE" /var/www/html/

# Keep only latest 7 backups
find "$BACKUP_DIR" -name "laravel-*.tar.gz" -type f -printf '%T@ %p\n' \
    | sort -nr \
    | awk 'NR>7 {print substr($0, index($0,$2))}' \
    | xargs -r rm -f

echo "Laravel backup completed: $BACKUP_FILE"