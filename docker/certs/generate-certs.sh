#!/usr/bin/env bash
# Vygeneruje lokální TLS certifikát pro Docker web (HTTPS z LAN / mobilu).
# Preferuje mkcert (důvěryhodný CA na hostu i telefonu po instalaci root CA).
# Bez mkcert použije openssl self-signed (prohlížeč / mobil musí varování přijmout
# nebo root nainstalovat ručně — pro QR/PWA často nestačí).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

HOST_NAMES="${WEB_CERT_HOSTS:-localhost,web.local,grafia.local}"
EXTRA_IP="${WEB_LAN_IP:-}"

# Detekce LAN IP (volitelně), pokud není v env
if [[ -z "$EXTRA_IP" ]]; then
  if command -v ip >/dev/null 2>&1; then
    EXTRA_IP="$(ip -4 route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="src"){print $(i+1); exit}}' || true)"
  fi
fi

echo "SAN hostnames: $HOST_NAMES"
echo "LAN IP: ${EXTRA_IP:-'(není)'}"

if command -v mkcert >/dev/null 2>&1; then
  echo "Používám mkcert…"
  mkcert -install >/dev/null 2>&1 || true
  ARGS=(-cert-file cert.pem -key-file key.pem)
  IFS=',' read -ra NAMES <<< "$HOST_NAMES"
  for n in "${NAMES[@]}"; do
    n="$(echo "$n" | xargs)"
    [[ -n "$n" ]] && ARGS+=("$n")
  done
  ARGS+=(127.0.0.1 ::1)
  [[ -n "$EXTRA_IP" ]] && ARGS+=("$EXTRA_IP")
  mkcert "${ARGS[@]}"
  # Root CA pro telefon (zkopírujte a nainstalujte)
  CAROOT="$(mkcert -CAROOT)"
  cp "${CAROOT}/rootCA.pem" ./ca.pem
  echo "Hotovo (mkcert). Root CA: ${CAROOT}/rootCA.pem (zkopírováno i do docker/certs/ca.pem)"
  echo "Na telefonu nainstalujte ca.pem jako důvěryhodnou CA, pak https://<LAN-IP>/ bude bez varování."
  exit 0
fi

echo "mkcert není v PATH — generuji self-signed openssl certifikát…"
SAN="DNS:localhost,DNS:web.local,DNS:grafia.local,IP:127.0.0.1,IP:::1"
IFS=',' read -ra NAMES <<< "$HOST_NAMES"
for n in "${NAMES[@]}"; do
  n="$(echo "$n" | xargs)"
  [[ -n "$n" && "$n" != "localhost" && "$n" != "web.local" && "$n" != "grafia.local" ]] && SAN="${SAN},DNS:${n}"
done
[[ -n "$EXTRA_IP" ]] && SAN="${SAN},IP:${EXTRA_IP}"

openssl req -x509 -nodes -newkey rsa:2048 -days 825 \
  -keyout key.pem -out cert.pem \
  -subj "/CN=grafia-web-local/O=Grafia Local Dev" \
  -addext "subjectAltName=${SAN}"

cp cert.pem ca.pem
echo "Hotovo (self-signed). Pro mobilní HTTPS bez varování nainstalujte mkcert a spusťte skript znovu."
