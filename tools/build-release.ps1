param([string]$ShopwareCli = 'shopware-cli')
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
if (-not (Get-Command $ShopwareCli -ErrorAction SilentlyContinue)) {
    throw 'Shopware CLI fehlt. Unter Linux/WSL, macOS oder Docker: bash tools/build-release.sh. Es wird keine Ersatz-ZIP erstellt.'
}
$env:DO_NOT_TRACK = '1'
& $ShopwareCli extension validate $root --only builtin
if ($LASTEXITCODE -ne 0) { throw 'Shopware validation failed' }
& $ShopwareCli extension package $root --disable-git --release
if ($LASTEXITCODE -ne 0) { throw 'Shopware build/package failed' }
