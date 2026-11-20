# Vygeneruje lokální TLS certifikát pro Docker web (HTTPS z LAN / mobilu).
# Spouštějte z PowerShellu. Preferuje mkcert; jinak openssl v jednorázovém Docker kontejneru.
#
# Použití (z kořene repo):
#   .\docker\certs\generate-certs.ps1
#   $env:WEB_LAN_IP = "192.168.1.42"; .\docker\certs\generate-certs.ps1
#
# Execution policy (jednou, pokud Windows skript blokuje):
#   Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
#
# Skript nemění aktuální adresář PowerShellu (zůstáváte v C:\Apacheroot\web).

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$CertPem = Join-Path $ScriptDir "cert.pem"
$KeyPem = Join-Path $ScriptDir "key.pem"
$CaPem = Join-Path $ScriptDir "ca.pem"

# mkcert píše běžné hlášky na stderr („Created a new local CA“). S ErrorActionPreference=Stop
# to PowerShell vyhodí jako NativeCommandError — proto voláme přes tuto funkci a hlídáme jen exit code.
function Invoke-Mkcert {
    param(
        [Parameter(Mandatory = $true)]
        [string[]] $MkcertArgs
    )
    $prevEap = $ErrorActionPreference
    $ErrorActionPreference = "Continue"
    $lines = & mkcert @MkcertArgs 2>&1
    $exitCode = $LASTEXITCODE
    $ErrorActionPreference = $prevEap
    foreach ($line in $lines) {
        if ($null -eq $line) { continue }
        Write-Host ($line.ToString())
    }
    if ($exitCode -ne 0) {
        throw "mkcert selhal (exit $exitCode): $($MkcertArgs -join ' ')"
    }
    return $lines
}

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
    Invoke-Mkcert -MkcertArgs @("-install") | Out-Null
    $argsList = @("-cert-file", $CertPem, "-key-file", $KeyPem)
    foreach ($n in ($HostNames -split ",")) {
        $n = $n.Trim()
        if ($n) { $argsList += $n }
    }
    $argsList += @("127.0.0.1", "::1")
    if ($ExtraIp) { $argsList += $ExtraIp }
    Invoke-Mkcert -MkcertArgs $argsList | Out-Null
    $carootLines = Invoke-Mkcert -MkcertArgs @("-CAROOT")
    $caroot = ($carootLines | ForEach-Object { $_.ToString() } | Where-Object { $_.Trim() -ne "" } | Select-Object -Last 1).Trim()
    if (-not $caroot -or -not (Test-Path (Join-Path $caroot "rootCA.pem"))) {
        throw "mkcert -CAROOT nevrátil platnou cestu (dostali jsme: '$caroot')."
    }
    Copy-Item -Force (Join-Path $caroot "rootCA.pem") $CaPem
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

Copy-Item -Force $CertPem $CaPem
Write-Host "Hotovo (self-signed). Pro mobilní HTTPS bez varování nainstalujte mkcert a spusťte skript znovu."
Write-Host "  mkcert: https://github.com/FiloSottile/mkcert#windows"
