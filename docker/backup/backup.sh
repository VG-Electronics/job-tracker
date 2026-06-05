#!/bin/sh
BACKUP_DIR="/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
FILENAME="$BACKUP_DIR/job_tracker_$TIMESTAMP.sql.gz"

mkdir -p "$BACKUP_DIR"

mysqldump \
  -h "$MYSQL_HOST" \
  -u "$MYSQL_USER" \
  -p"$MYSQL_PASSWORD" \
  "$MYSQL_DATABASE" | gzip > "$FILENAME"

if [ $? -eq 0 ]; then
  echo "[$TIMESTAMP] Backup OK: $FILENAME"
  ls -t "$BACKUP_DIR"/job_tracker_*.sql.gz | tail -n +8 | xargs -r rm
else
  echo "[$TIMESTAMP] Backup FAILED"
  rm -f "$FILENAME"
fi
