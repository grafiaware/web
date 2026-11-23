# Po pullu / merge: TLS certy (pokud chybí), build image web, recreate Apache kontejneru.
# Spouštějte z PowerShellu v libovolném adresáři — skript najde kořen repo sám
# a nemění aktuální složku terminálu.
#
# Použití (typicky po git pull / Pull v NetBeans):
#   .\docker\recreate-web.ps1
#   .\docker\recreate-web.ps1 -SkipCerts          # jen build + recreate
#   .\docker\recreate-web.ps1 -NoCache            # build --no-cache web
#
# Execution policy (jednou, pokud Windows skript blokuje):
#   Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
#
# Nepřenášejte skript ručně (USB / e-mail) — vždy přes git pull, ať sedí konce řádků.

param(
    [switch]$SkipCerts,
    [switch]$NoCache
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RepoRoot = Resolve-Path (Join-Path $ScriptDir "..")
$CertPem = Join-Path $ScriptDir "certs\cert.pem"
$KeyPem = Join-Path $ScriptDir "certs\key.pem"
$GenerateCerts = Join-Path $ScriptDir "certs\generate-certs.ps1"

Push-Location $RepoRoot
try {
    if (-not (Test-Path (Join-Path $RepoRoot "docker-compose.yml"))) {
        throw "V $RepoRoot chybí docker-compose.yml — spusťte skript z klonu grafiaware/web."
    }

    if (-not $SkipCerts) {
        if (-not (Test-Path $CertPem) -or -not (Test-Path $KeyPem)) {
            Write-Host "Chybí docker/certs/cert.pem nebo key.pem — generuji…"
            & $GenerateCerts
            if ($LASTEXITCODE -ne 0) {
                throw "generate-certs.ps1 skončil s kódem $LASTEXITCODE"
            }
        } else {
            Write-Host "TLS certy už existují (docker/certs/*.pem) — přeskakuji generování."
            Write-Host "  (nové hosty / LAN IP: spusťte .\docker\certs\generate-certs.ps1 ručně)"
        }
    }

    $buildArgs = @("compose", "build", "web")
    if ($NoCache) {
        $buildArgs = @("compose", "build", "--no-cache", "web")
    }
    Write-Host "docker $($buildArgs -join ' ')"
    & docker @buildArgs
    if ($LASTEXITCODE -ne 0) {
        throw "docker compose build web selhal (kód $LASTEXITCODE)"
    }

    Write-Host "docker compose up -d --force-recreate web"
    & docker compose up -d --force-recreate web
    if ($LASTEXITCODE -ne 0) {
        throw "docker compose up selhal (kód $LASTEXITCODE)"
    }

    Write-Host ""
    & docker compose ps
    Write-Host ""
    Write-Host "Hotovo. Log web: docker compose logs -f web"
    Write-Host "WEB_PUBLIC_HOST: docker compose exec web printenv WEB_PUBLIC_HOST"
}
finally {
    Pop-Location
}
