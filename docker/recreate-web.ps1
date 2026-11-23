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

# $MyInvocation.MyCommand.Path je někdy prázdné → Join-Path ".." od CWD dá C:\Apacheroot
# místo C:\Apacheroot\web. Preferujte $PSScriptRoot a hledejte docker-compose.yml nahoru.
$StartDir = $PSScriptRoot
if (-not $StartDir) {
    if ($MyInvocation.MyCommand.Path) {
        $StartDir = Split-Path -Parent $MyInvocation.MyCommand.Path
    } else {
        $StartDir = (Get-Location).Path
    }
}

$RepoRoot = $null
$candidate = $StartDir
while ($candidate) {
    if (Test-Path -LiteralPath (Join-Path $candidate "docker-compose.yml")) {
        $RepoRoot = (Resolve-Path -LiteralPath $candidate).Path
        break
    }
    $parent = Split-Path -Parent $candidate
    if (-not $parent -or $parent -eq $candidate) {
        break
    }
    $candidate = $parent
}

if (-not $RepoRoot) {
    throw @"
Nenašel jsem docker-compose.yml nad složkou skriptu.
  PSScriptRoot / StartDir: $StartDir
  CWD: $((Get-Location).Path)
Spusťte: cd C:\Apacheroot\web ; .\docker\recreate-web.ps1
"@
}

$DockerDir = Join-Path $RepoRoot "docker"
$CertPem = Join-Path $DockerDir "certs\cert.pem"
$KeyPem = Join-Path $DockerDir "certs\key.pem"
$GenerateCerts = Join-Path $DockerDir "certs\generate-certs.ps1"

Push-Location $RepoRoot
try {
    Write-Host "Repo: $RepoRoot"

    if (-not $SkipCerts) {
        if (-not (Test-Path -LiteralPath $CertPem) -or -not (Test-Path -LiteralPath $KeyPem)) {
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
