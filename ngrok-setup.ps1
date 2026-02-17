# Script untuk setup ngrok dengan Laravel
# Usage: .\ngrok-setup.ps1 https://your-ngrok-url.ngrok-free.app

param(
    [Parameter(Mandatory=$false)]
    [string]$NgrokUrl
)

if (-not $NgrokUrl) {
    Write-Host "❌ Masukkan URL ngrok kamu!" -ForegroundColor Red
    Write-Host "Contoh: .\ngrok-setup.ps1 https://abc123.ngrok-free.app" -ForegroundColor Yellow
    exit 1
}

# Update APP_URL di .env
Write-Host "🔧 Updating APP_URL di .env..." -ForegroundColor Cyan
$envPath = ".env"
$content = Get-Content $envPath -Raw

if ($content -match "APP_URL=.*") {
    $content = $content -replace "APP_URL=.*", "APP_URL=$NgrokUrl"
} else {
    $content += "`nAPP_URL=$NgrokUrl"
}

Set-Content -Path $envPath -Value $content

# Clear config cache
Write-Host "🧹 Clearing config cache..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear

Write-Host "✅ Setup selesai!" -ForegroundColor Green
Write-Host "📝 APP_URL sekarang: $NgrokUrl" -ForegroundColor Green
Write-Host ""
Write-Host "🚀 Langkah selanjutnya:" -ForegroundColor Yellow
Write-Host "   1. Jalankan: php artisan serve" -ForegroundColor White
Write-Host "   2. Di terminal lain: ngrok http 8000" -ForegroundColor White
Write-Host "   3. Buka URL ngrok di browser" -ForegroundColor White
