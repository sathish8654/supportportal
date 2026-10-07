#!/bin/bash

BACKUP_HOST="support.easy2get.in"
SSH_PORT="3022"
SSH_PASS="Sm@rtctrl_321mail"

echo "Syncing to: $BACKUP_HOST"

# Create destination directories
sshpass -p "$SSH_PASS" ssh -p "$SSH_PORT" \
    -o StrictHostKeyChecking=no root@$BACKUP_HOST "
mkdir -p /data/backup/supportportal/database
mkdir -p /data/backup/supportportal/web
"

# Sync database
sshpass -p "$SSH_PASS" rsync -auv --progress \
    -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no" \
    /data/backup/supportportal/database/ \
    root@$BACKUP_HOST:/data/backup/supportportal/database/

# Sync web
sshpass -p "$SSH_PASS" rsync -auv --progress \
    -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no" \
    /data/backup/supportportal/web/ \
    root@$BACKUP_HOST:/data/backup/supportportal/web/

# Keep only 7 newest files/directories
sshpass -p "$SSH_PASS" ssh -p "$SSH_PORT" \
    -o StrictHostKeyChecking=no root@$BACKUP_HOST "
cd /data/backup/supportportal/database && ls -1dt * 2>/dev/null | tail -n +8 | xargs -r rm -rf
cd /data/backup/supportportal/web && ls -1dt * 2>/dev/null | tail -n +8 | xargs -r rm -rf
"

echo "Backup completed successfully."