param([string]$NodePath = 'node')
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$utf8 = [System.Text.UTF8Encoding]::new($false)
$version = (Get-Content -LiteralPath "$root/composer.json" -Raw | ConvertFrom-Json).version
$admin = Join-Path $root 'src/Resources/public/administration'
$parts = foreach ($name in @('faq-icon', 'core', 'advanced', 'cms-block')) {
    $source = [IO.File]::ReadAllText("$root/src/Resources/app/administration/src/$name.js").Replace("`r", '')
    "(function(){`n$source`n})();"
}
$bundle = ($parts -join "`n") + "`n"
$sha = [Security.Cryptography.SHA256]::Create()
$hash = ([BitConverter]::ToString($sha.ComputeHash($utf8.GetBytes($bundle)))).Replace('-', '').ToLowerInvariant().Substring(0,8)
$sha.Dispose()
$filename = "tuami-faq-pro-$hash.js"
[IO.File]::WriteAllText("$admin/assets/$filename", $bundle, $utf8)
& $NodePath --check "$admin/assets/$filename"
if ($LASTEXITCODE -ne 0) { throw 'Administration JavaScript syntax check failed' }
$entrypoints = Get-Content -LiteralPath "$admin/.vite/entrypoints.json" -Raw | ConvertFrom-Json
$entrypoints.entryPoints.'tuami-faq-pro'.js = @("/bundles/tuamifaqpro/administration/assets/$filename")
$manifest = Get-Content -LiteralPath "$admin/.vite/manifest.json" -Raw | ConvertFrom-Json
$manifest.'src/main.js'.file = "assets/$filename"
[IO.File]::WriteAllText("$admin/.vite/entrypoints.json", ($entrypoints | ConvertTo-Json -Depth 6), $utf8)
[IO.File]::WriteAllText("$admin/.vite/manifest.json", ($manifest | ConvertTo-Json -Depth 6), $utf8)

Add-Type -AssemblyName System.IO.Compression.FileSystem
$zipPath = Join-Path $root "TuamiFaqPro-$version.zip"
if (Test-Path -LiteralPath $zipPath) { throw "Release archive already exists: $zipPath" }
$archive = [IO.Compression.ZipFile]::Open($zipPath, [IO.Compression.ZipArchiveMode]::Create)
try {
    $files = @(Get-ChildItem -LiteralPath "$root/src" -Recurse -Force -File)
    $files += @('composer.json', 'README.md', 'LICENSE', 'CHANGELOG.md') | ForEach-Object { Get-Item -LiteralPath "$root/$_" }
    foreach ($file in $files) {
        if ($file.Name -match '^tuami-faq-pro-.*\.js$' -and $file.Name -ne $filename) { continue }
        $relative = $file.FullName.Substring($root.Length + 1).Replace('\', '/')
        [void][IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $file.FullName, "TuamiFaqPro/$relative", [IO.Compression.CompressionLevel]::Optimal)
    }
} finally { $archive.Dispose() }
$check = [IO.Compression.ZipFile]::OpenRead($zipPath)
try {
    foreach ($required in @('composer.json', 'src/Resources/config/plugin.png', 'src/Resources/config/services.php', 'src/Resources/config/routes.php', 'src/Resources/public/administration/.vite/entrypoints.json', "src/Resources/public/administration/assets/$filename")) {
        if ($null -eq $check.GetEntry("TuamiFaqPro/$required")) { throw "Missing ZIP entry: $required" }
    }
    if ($check.Entries.FullName -match '(services|routes)\.xml$') { throw 'Obsolete XML configuration in archive' }
    Write-Output "Verified ZIP: $($check.Entries.Count) entries"
} finally { $check.Dispose() }
Get-FileHash -LiteralPath $zipPath -Algorithm SHA256
