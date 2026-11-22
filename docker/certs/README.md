# Lokální TLS certifikáty (Docker web)

Soubory `cert.pem`, `key.pem` a `ca.pem` se **necommitují**.

## Windows (PowerShell) — doporučeno

1. V `.env` (viz `.env.docker.example`) nastavte své `WEB_PUBLIC_HOST` a případně `WEB_LAN_IP`.
2. Z kořene repo (`C:\Apacheroot\web`):

```powershell
.\docker\certs\generate-certs.ps1
```

Skript načte `.env` a do certifikátu dá `localhost` + `WEB_DEV_HOSTS` (výchozí `ps.dev.local`, `vs.dev.local`, `ks.dev.local`).

Podrobný návod: [docs/vp/DOCKER-WINDOWS.md](../../docs/vp/DOCKER-WINDOWS.md) (krok 4 a §11).

## macOS / Linux (bash)

```bash
./docker/certs/generate-certs.sh
```

Ideálně s [mkcert](https://github.com/FiloSottile/mkcert).

Pak `docker compose up -d` (nebo restart služby `web`).  
Na telefonu nainstalujte `ca.pem` jako důvěryhodnou certifikační autoritu.
