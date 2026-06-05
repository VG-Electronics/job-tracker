FROM alpine:3.19
RUN apk add --no-cache mysql-client dcron
COPY docker/backup/backup.sh /backup.sh
RUN chmod +x /backup.sh && \
    echo "0 3 * * * /backup.sh >> /var/log/backup.log 2>&1" | crontab -
CMD ["crond", "-f", "-d", "8"]
