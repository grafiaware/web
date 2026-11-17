# Vygeneruje lokální TLS certifikát pro Docker web (HTTPS z LAN / mobilu).
# Spouštějte z PowerShellu. Preferuje mkcert; jinak openssl v jednorázovém Docker kontejneru.
#
# Použití (z kořene repo):
#   .\docker\certs\generate-certs.ps1
#   $env:WEB_LAN_IP = "192.168.1.42"; .\docker\certs\generate-certs.ps1
#
# Execution policy (jednou, pokud Windows skript blokuje):
#   Set-ExecutionPolicy -Scope CurrentUser RemoteSigned

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ScriptDir

$HostNames = if ($env:WEB_CERT_HOSTS) { $env:WEB_CERT_HOSTS } else { "localhost,web.local,grafia.local" }
$ExtraIp = if ($env:WEB_LAN_IP) { $env:WEB_LAN_IP.Trim() } else { "" }

if (-not $ExtraIp) {
    try {
        $cfg = Get-NetIPConfiguration -ErrorAction SilentlyContinue |
            Where-Object { $_.IPv4DefaultGateway -ne $null -and $_.NetAdapter.Status -eq "Up" } |
            Select-Object -First 1
        if ($cfg -and $cfg.IPv4Address) {
            $ExtraIp = $cfg.IPv4Address.IPAddress
        }
    } catch {
        # Ignorovat — IP doplní uživatel ručně
    }
}

Write-Host "SAN hostnames: $HostNames"
Write-Host "LAN IP: $(if ($ExtraIp) { $ExtraIp } else { '(není — doplňte $env:WEB_LAN_IP)' })"

$mkcert = Get-Command mkcert -ErrorAction SilentlyContinue
if ($mkcert) {
    Write-Host "Používám mkcert…"
    & mkcert -install 2>$null | Out-Null
    $argsList = @("-cert-file", "cert.pem", "-key-file", "key.pem")
    foreach ($n in ($HostNames -split ",")) {
        $n = $n.Trim()
        if ($n) { $argsList += $n }
    }
    $argsList += @("127.0.0.1", "::1")
    if ($ExtraIp) { $argsList += $ExtraIp }
    & mkcert @argsList
    $caroot = (& mkcert -CAROOT).Trim()
    Copy-Item -Force (Join-Path $caroot "rootCA.pem") (Join-Path $ScriptDir "ca.pem")
    Write-Host "Hotovo (mkcert). Root CA zkopírován do docker\certs\ca.pem"
    Write-Host "Na telefonu nainstalujte ca.pem jako důvěryhodnou CA, pak https://<LAN-IP>/ bude bez varování."
    exit 0
}

Write-Host "mkcert není v PATH — generuji self-signed přes Docker (openssl)…"

$san = "DNS:localhost,DNS:web.local,DNS:grafia.local,IP:127.0.0.1,IP:0:0:0:0:0:0:0:1"
foreach ($n in ($HostNames -split ",")) {
    $n = $n.Trim()
    if ($n -and $n -notin @("localhost", "web.local", "grafia.local")) {
        $san += ",DNS:$n"
    }
}
if ($ExtraIp) {
    $san += ",IP:$ExtraIp"
}

$docker = Get-Command docker -ErrorAction SilentlyContinue
if (-not $docker) {
    throw "Docker není v PATH. Spusťte Docker Desktop a zkuste znovu."
}

# alpine/openssl image — bez instalace openssl na Windows
$opensslCmd = @(
    "req", "-x509", "-nodes", "-newkey", "rsa:2048", "-days", "825",
    "-keyout", "/certs/key.pem", "-out", "/certs/cert.pem",
    "-subj", "/CN=grafia-web-local/O=Grafia Local Dev",
    "-addext", "subjectAltName=$san"
)

docker run --rm -v "${ScriptDir}:/certs" alpine/openssl @opensslCmd
if ($LASTEXITCODE -ne 0) {
    throw "docker run alpine/openssl selhal (exit $LASTEXITCODE)."
}

Copy-Item -Force (Join-Path $ScriptDir "cert.pem") (Join-Path $ScriptDir "ca.pem")
Write-Host "Hotovo (self-signed). Pro mobilní HTTPS bez varování nainstalujte mkcert a spusťte skript znovu."
Write-Host "  mkcert: https://github.com/FiloSottile/mkcert#windows"
