#!/bin/bash
set -euo pipefail

# Aplikace v konfiguraci míří na 127.0.0.1:3306 a SMTP localhost:25.
# V Docker síti jsou služby `db` a `mail` — přesměrujeme je na localhost v kontejneru,
# aby nebylo nutné měnit Configuration*.php všech sites.
DB_UPSTREAM_HOST="${WEB_DB_UPSTREAM_HOST:-db}"
DB_UPSTREAM_PORT="${WEB_DB_UPSTREAM_PORT:-3306}"
MAIL_UPSTREAM_HOST="${WEB_MAIL_UPSTREAM_HOST:-mail}"
MAIL_UPSTREAM_PORT="${WEB_MAIL_UPSTREAM_PORT:-25}"

if command -v socat >/dev/null 2>&1; then
  socat TCP-LISTEN:3306,bind=127.0.0.1,fork,reuseaddr "TCP:${DB_UPSTREAM_HOST}:${DB_UPSTREAM_PORT}" &
  socat TCP-LISTEN:25,bind=127.0.0.1,fork,reuseaddr "TCP:${MAIL_UPSTREAM_HOST}:${MAIL_UPSTREAM_PORT}" &
fi

# TLS vhost zapnout jen když existují certifikáty (jinak běží jen HTTP).
if [[ -f /etc/apache2/ssl/cert.pem && -f /etc/apache2/ssl/key.pem ]]; then
  a2ensite grafia-web-ssl >/dev/null 2>&1 || true
else
  a2dissite grafia-web-ssl >/dev/null 2>&1 || true
  echo "WARN: chybí docker/certs/cert.pem nebo key.pem — HTTPS vypnuto. Spusťte docker/certs/generate-certs.sh" >&2
fi

# ServerName z env (LAN hostname / IP)
if [[ -n "${WEB_SERVER_NAME:-}" ]]; then
  echo "ServerName ${WEB_SERVER_NAME}" > /etc/apache2/conf-available/servername.conf
  a2enconf servername >/dev/null 2>&1 || true
fi

exec "$@"
