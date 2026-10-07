#!/bin/bash

# ==============================================================================
# RootIQ Web Automated Backup Script
# ==============================================================================

set -euo pipefail

# ------------------------------- Configuration --------------------------------
PROJECT_NAME="supportportal"
BASE_DIR="/var/www/html"
SRC_DIR="${BASE_DIR}/${PROJECT_NAME}"
DEST_DIR="/data/backup/supportportal/web"
KEEP_COUNT=5

# Formatted timestamp (includes time to avoid same-day collisions)
TIMESTAMP=$(date +"%d-%m-%Y_%H%M%S")
BACKUP_FILENAME="${TIMESTAMP}_${PROJECT_NAME}.tar.gz"
FINAL_BACKUP="${DEST_DIR}/${BACKUP_FILENAME}"
TEMP_BACKUP="${DEST_DIR}/.${BACKUP_FILENAME}.tmp"

# ----------------------------- Error & Trap Setup -----------------------------
cleanup() {
    local exit_code=$?
    if [[ -f "$TEMP_BACKUP" ]]; then
        echo "⚠️  Cleaning up incomplete backup file..."
        rm -f "$TEMP_BACKUP"
    fi
    if [[ $exit_code -ne 0 ]]; then
        echo "❌ Backup process failed with exit code $exit_code."
    fi
}
trap cleanup EXIT INT TERM

# --------------------------------- Validation ---------------------------------
if [[ ! -d "$SRC_DIR" ]]; then
    echo "❌ Source directory does not exist: $SRC_DIR"
    exit 1
fi

mkdir -p "$DEST_DIR"

echo "=================================================="
echo "📦 Starting Backup for: $PROJECT_NAME"
echo "🕒 Timestamp:           $(date)"
echo "📂 Source Directory:    $SRC_DIR"
echo "💾 Target Backup:       $FINAL_BACKUP"
echo "=================================================="

START_TIME=$(date +%s)

# ------------------------------ Create Backup ---------------------------------
# Note: Exclude patterns must match relative paths inside archive (relative to BASE_DIR)
tar -czf "$TEMP_BACKUP" \
    --exclude="${PROJECT_NAME}/node_modules" \
    --exclude="${PROJECT_NAME}/.expo" \
    --exclude="${PROJECT_NAME}/.expo-shared" \
    --exclude="${PROJECT_NAME}/.cache" \
    --exclude="${PROJECT_NAME}/build" \
    --exclude="${PROJECT_NAME}/dist" \
    --exclude="${PROJECT_NAME}/coverage" \
    --exclude="${PROJECT_NAME}/logs" \
    --exclude="${PROJECT_NAME}/tmp" \
    --exclude="${PROJECT_NAME}/*.log" \
    -C "$BASE_DIR" \
    "$PROJECT_NAME"

# ---------------------------- Verify Integrity --------------------------------
echo "🔍 Verifying backup integrity..."
tar -tzf "$TEMP_BACKUP" > /dev/null

# Finalize file atomically
mv "$TEMP_BACKUP" "$FINAL_BACKUP"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
BACKUP_SIZE=$(ls -lh "$FINAL_BACKUP" | awk '{print $5}')

echo "✅ Backup created successfully in ${DURATION}s!"
echo "📊 Archive Size: $BACKUP_SIZE"
echo ""

# ---------------------------- Retention Management ----------------------------
echo "🧹 Pruning old backups (Keeping latest $KEEP_COUNT)..."

# Sort by modification time (oldest first) to safely prune across date formats
TOTAL_BACKUPS=$(find "$DEST_DIR" -maxdepth 1 \( -name "*_${PROJECT_NAME}.tar.gz" -o -name "${PROJECT_NAME}_*.tar.gz" \) -type f | wc -l)

if [[ $TOTAL_BACKUPS -gt $KEEP_COUNT ]]; then
    PRUNE_COUNT=$((TOTAL_BACKUPS - KEEP_COUNT))
    echo "🗑️  Removing $PRUNE_COUNT old backup(s)..."
    find "$DEST_DIR" -maxdepth 1 \( -name "*_${PROJECT_NAME}.tar.gz" -o -name "${PROJECT_NAME}_*.tar.gz" \) -type f -printf '%T@ %p\n' \
        | sort -n \
        | head -n "$PRUNE_COUNT" \
        | cut -d' ' -f2- \
        | xargs -r rm -v
fi

# --------------------------------- Summary ------------------------------------
echo ""
echo "📁 Available backups in $DEST_DIR:"
ls -lh "$DEST_DIR"/*.tar.gz 2>/dev/null || echo "No backups found."

echo ""
echo "💾 Disk space on backup drive:"
df -h "$DEST_DIR" | awk 'NR==1 || NR==2'

echo "=================================================="
echo "🎉 Backup process finished successfully at $(date)"
echo "=================================================="

# ---------------------------- Cloud Backup Sync -------------------------------
echo "💾 Sending backup to cloud server..."

# Configuration
SERVER1="rootiqcloud2.liveskytech.com"
REMOTE_PATH="/data/backup/supportportal/web"
SSH_USER="root"
SSH_PASS="Sm@rtctrl_321mail"
SSH_PORT="3022"
SSH_OPTS="-4 -o Port=$SSH_PORT -o StrictHostKeyChecking=no -o ConnectTimeout=10"

# Resolve target IP
SLAVE_IP=$(dig +short A "$SERVER1" | head -n1)

if [[ -z "$SLAVE_IP" ]]; then
    echo "❌ DNS resolution failed for $SERVER1"
    exit 1
fi

echo "🌐 Resolved IP ($SERVER1): $SLAVE_IP"

# Ensure remote destination directory exists
sshpass -p "$SSH_PASS" ssh $SSH_OPTS "${SSH_USER}@${SLAVE_IP}" "mkdir -p '$REMOTE_PATH'"

echo "🚀 Uploading $FINAL_BACKUP to $SLAVE_IP:$REMOTE_PATH/"
if sshpass -p "$SSH_PASS" scp $SSH_OPTS "$FINAL_BACKUP" "${SSH_USER}@${SLAVE_IP}:${REMOTE_PATH}/"; then
    echo "✅ SUCCESS: Cloud backup upload complete!"

    # Remote Retention Management (Keep latest 10)
    REMOTE_KEEP_COUNT=10
    echo "🧹 Pruning old backups on remote server (Keeping latest $REMOTE_KEEP_COUNT)..."
    sshpass -p "$SSH_PASS" ssh $SSH_OPTS "${SSH_USER}@${SLAVE_IP}" "
        cd '$REMOTE_PATH' || exit 0
        TOTAL_REMOTE=\$(find . -maxdepth 1 \( -name '*_${PROJECT_NAME}.tar.gz' -o -name '${PROJECT_NAME}_*.tar.gz' \) -type f | wc -l)
        if [ \"\$TOTAL_REMOTE\" -gt $REMOTE_KEEP_COUNT ]; then
            PRUNE_REMOTE=\$((TOTAL_REMOTE - $REMOTE_KEEP_COUNT))
            echo \"🗑️  Removing \$PRUNE_REMOTE old backup(s) on remote server...\"
            find . -maxdepth 1 \( -name '*_${PROJECT_NAME}.tar.gz' -o -name '${PROJECT_NAME}_*.tar.gz' \) -type f -printf '%T@ %p\n' \
                | sort -n \
                | head -n \"\$PRUNE_REMOTE\" \
                | cut -d' ' -f2- \
                | xargs -r rm -v
        fi
    "
else
    echo "❌ FAILED: Upload to cloud server failed. Local archive retained at $FINAL_BACKUP"
    exit 1
fi
