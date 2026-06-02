#!/bin/sh
set -e

if [ -z "$AUTH_LOGIN" ] || [ -z "$AUTH_PASSWORD" ]; then
    echo "ERROR: AUTH_LOGIN and AUTH_PASSWORD must be set"
    exit 1
fi

printf "%s:%s\n" "$AUTH_LOGIN" "$(openssl passwd -apr1 "$AUTH_PASSWORD")" > /etc/nginx/.htpasswd

exec nginx -g "daemon off;"
