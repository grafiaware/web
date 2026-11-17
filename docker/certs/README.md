# Lokální TLS certifikáty (Docker web)

Soubory `cert.pem`, `key.pem` a případně `ca.pem` se **necommitují**.

```bash
# z kořene repo (doporučeno mkcert: https://github.com/FiloSottile/mkcert)
./docker/certs/generate-certs.sh

# volitelně pevná LAN IP a další jména
WEB_LAN_IP=192.168.1.20 WEB_CERT_HOSTS=localhost,web.local,muj-notebook ./docker/certs/generate-certs.sh
```

Pak `docker compose up -d` (nebo restart služby `web`). HTTPS: `https://localhost/` nebo `https://<LAN-IP>/`.

Na telefonu nainstalujte `ca.pem` (u mkcert je to root CA) jako důvěryhodnou autoritu — jinak mobilní WebView / QR odkazy na HTTPS často selžou.
