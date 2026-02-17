#!/bin/sh

# Default PORT to 8080 if not set (Railway provides PORT dynamically)
export PORT="${PORT:-8080}"

# Substitute only $PORT in the nginx template (leave $uri, $args, etc. intact)
envsubst '$PORT' < /etc/nginx/railway.conf.template > /etc/nginx/http.d/app.conf

# Ensure session directory exists with correct ownership
mkdir -p /data/sessions
chown -R www-data:www-data /data

# Start supervisord as PID 1 for proper signal handling
exec supervisord -c /etc/supervisord.conf
