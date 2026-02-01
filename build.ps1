
$version = "1.2.2"
$pluginSlug = "swell-styled-map"
$zipName = "$pluginSlug-v$version.zip"
$tempDir = "temp_build"
$rootDir = "$tempDir/$pluginSlug"

# Clean up previous build
if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }
if (Test-Path $zipName) { Remove-Item $zipName -Force }

# Create directory structure
New-Item -ItemType Directory -Path $rootDir -Force | Out-Null
New-Item -ItemType Directory -Path "$rootDir/assets" -Force | Out-Null

# Copy files
Copy-Item "$pluginSlug.php" -Destination $rootDir
Copy-Item "assets/*" -Destination "$rootDir/assets" -Recurse

# Exclude unwanted files (if any were copied)
if (Test-Path "$rootDir/assets/js/admin.js") { Remove-Item "$rootDir/assets/js/admin.js" -Force }

# Create Zip using tar (bsdtar) for better compatibility
# We change location to tempDir so that the zip root starts with pluginSlug
Push-Location $tempDir
try {
    tar -a -c -f "../$zipName" "$pluginSlug"
}
finally {
    Pop-Location
}

# Cleanup
Remove-Item $tempDir -Recurse -Force

Write-Host "Created $zipName successfully using tar."
