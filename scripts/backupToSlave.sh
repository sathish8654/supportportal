#!/bin/bash

MASTER="gsm.liveskytech.com"
SSH_PORT="3022"
SSH_PASS="Sm@rtctrl_321mail"

MASTER_IP=$(dig +short "$MASTER" | head -n1)

for HOST in gsmoffice.liveskytech.com gsmhome.liveskytech.com
do
    HOST_IP=$(dig +short "$HOST" | head -n1)

    if [ "$HOST_IP" != "$MASTER_IP" ]; then
        SLAVE="$HOST"
        break
    fi
done

[ -z "$SLAVE" ] && { echo "No slave server found"; exit 1; }

echo "Syncing to: $SLAVE"

# Create destination directories
sshpass -p "$SSH_PASS" ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no root@$SLAVE "
mkdir -p /data/backup/supportportal/database
mkdir -p /data/backup/supportportal/web
"

# Sync
sshpass -p "$SSH_PASS" rsync -auv --progress \
    -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no" \
    /data/backup/supportportal/database/ \
    root@$SLAVE:/data/backup/supportportal/database/

sshpass -p "$SSH_PASS" rsync -auv --progress \
    -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no" \
    /data/backup/supportportal/web/ \
    root@$SLAVE:/data/backup/supportportal/web/

# Keep only 7 newest files/directories
sshpass -p "$SSH_PASS" ssh -p "$SSH_PORT" -o StrictHostKeyChecking=no root@$SLAVE "
cd /data/backup/supportportal/database && ls -1dt * 2>/dev/null | tail -n +8 | xargs -r rm -rf
cd /data/backup/supportportal/web && ls -1dt * 2>/dev/null | tail -n +8 | xargs -r rm -rf
"

echo "Done."