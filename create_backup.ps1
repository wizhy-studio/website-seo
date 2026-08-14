param (
    [Parameter(Mandatory=$false)]
    [ValidateSet("pre", "post")]
    [string]$Stage = "pre",

    [Parameter(Mandatory=$false)]
    [string]$Description = "task_update",

    [Parameter(Mandatory=$false)]
    [int]$Version = 0
)

$rootDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $rootDir

# If version is not provided, calculate next version number based on existing backups
if ($Version -le 0) {
    $existingZips = Get-ChildItem -Path $rootDir -Filter "wizhy_studio_*_v*.zip"
    $maxVer = 0

    foreach ($zip in $existingZips) {
        if ($zip.Name -match '_v(\d+)\.zip$') {
            $verNum = [int]$matches[1]
            if ($verNum -gt $maxVer) {
                $maxVer = $verNum
            }
        }
    }

    # If it's a pre backup, increment version; if post, use current highest version
    if ($Stage -eq "pre") {
        $Version = $maxVer + 1
    } else {
        $Version = if ($maxVer -gt 0) { $maxVer } else { 1 }
    }
}

$zipName = "wizhy_studio_${Stage}_${Description}_v${Version}.zip"
$zipPath = Join-Path $rootDir $zipName

Write-Host "Creating $Stage backup: $zipName..."

# Temporary staging directory for clean packaging
$tempStage = Join-Path $env:TEMP ("wizhy_stage_" + [Guid]::NewGuid().ToString())
New-Item -ItemType Directory -Path $tempStage -Force | Out-Null

Get-ChildItem -Path $rootDir | ForEach-Object {
    if ($_.Name -notin @("0. Don't Touch", "Don't Touch", ".git", "node_modules", "Archive", "Archive_zip", "can be deleted", "can_be_deleted") -and $_.Extension -ne ".zip") {
        Copy-Item -Path $_.FullName -Destination (Join-Path $tempStage $_.Name) -Recurse -Force
    }
}

# Compress
Compress-Archive -Path "$tempStage\*" -DestinationPath $zipPath -Force
Remove-Item -Path $tempStage -Recurse -Force

Write-Host "✅ $Stage backup created successfully: $zipName"
