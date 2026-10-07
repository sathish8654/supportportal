#!/bin/bash

BACKUP_DIR="/data/backup/supportportal/database"

# Create directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

DB_NAME="liveskytech"
DATESTAMP=$(date +%d-%m-%y-%H-%M)

BACKUP_FILE="${BACKUP_DIR}/${DB_NAME}-${DATESTAMP}.7z"

# Create backup with proper internal filename
mysqldump -h localhost -u root -p'Sm@rtctrl_321mail' "$DB_NAME" --single-transaction \
    | 7z a -si"${DB_NAME}.sql" "$BACKUP_FILE"

# Check if backup was created successfully
if [ $? -eq 0 ]; then
    echo "Backup successful: $BACKUP_FILE"

    # Keep only latest 7 backups
    find "$BACKUP_DIR" -name "${DB_NAME}-*.7z" -type f -printf '%T@ %p\n' \
        | sort -nr \
        | awk 'NR>7 {print substr($0, index($0,$2))}' \
        | xargs -r rm -f
else
    echo "Backup failed. Old backups not deleted."
    exit 1
fi