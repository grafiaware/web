# Lokální TLS certifikáty (Docker web)

Soubory `cert.pem`, `key.pem` a případně `ca.pem` se **necommitují**.

## Windows (PowerShell) — doporučeno

Z kořene repo:

```powershell
$env:WEB_LAN_IP = "192.168.1.42"   # vaše LAN IP
.\docker\certs\generate-certs.ps1
```

Podrobný návod včetně mobilu: [docs/vp/DOCKER-WINDOWS.md](../../docs/vp/DOCKER-WINDOWS.md).

## macOS / Linux (bash)

```bash
WEB_LAN_IP=192.168.1.42 ./docker/certs/generate-certs.sh
```

Ideálně s [mkcert](https://github.com/FiloSottile/mkcert).

Pak `docker compose up -d` (nebo restart služby `web`).  
HTTPS: `https://localhost/` nebo `https://<LAN-IP>/`.

Na telefonu nainstalujte `ca.pem` jako důvěryhodnou certifikační autoritu (ne jen zkopírovat soubor).
