$ErrorActionPreference = 'Stop'
$base = 'http://127.0.0.1:8000'
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

$login = Invoke-WebRequest -Uri "$base/admin-panel/login" -WebSession $session -UseBasicParsing
$token = ([regex]::Match($login.Content, 'name="csrf-token" content="([^"]+)"')).Groups[1].Value
$body = @{ email = 'admin@salon.test'; password = 'password' } | ConvertTo-Json
Invoke-WebRequest -Uri "$base/api/login" -Method POST -Body $body -ContentType 'application/json' `
    -Headers @{ 'X-CSRF-TOKEN' = $token } -WebSession $session -UseBasicParsing | Out-Null

$r = Invoke-WebRequest -Uri "$base/admin-panel" -WebSession $session -UseBasicParsing
$html = $r.Content

Write-Output "=== KPI band (label -> value) ==="
$cells = [regex]::Matches($html, '<p class="band__label">\s*<svg.*?</svg>\s*(.*?)\s*</p>\s*<p class="band__value[^"]*">\s*(.*?)\s*</p>', 'Singleline')
foreach ($c in $cells) {
    $label = ($c.Groups[1].Value -replace '<[^>]+>', '' -replace '\s+', ' ').Trim()
    $value = ($c.Groups[2].Value -replace '<[^>]+>', '' -replace '\s+', ' ').Trim()
    Write-Output ("  {0,-22} {1}" -f $label, $value)
}

Write-Output ""
Write-Output "=== Status breakdown meters ==="
$meters = [regex]::Matches($html, '<span class="meter__name">\s*<span class="meter__dot"[^>]*></span>\s*(.*?)\s*</span>\s*<span class="meter__value">(.*?)</span>', 'Singleline')
foreach ($m in $meters) {
    Write-Output ("  {0,-14} {1}" -f $m.Groups[1].Value.Trim(), $m.Groups[2].Value.Trim())
}

Write-Output ""
Write-Output "=== Service ledger rows ==="
$rows = [regex]::Matches($html, '<tr>\s*<td class="cell-strong">(.*?)</td>.*?<td class="cell-num">(\d+)</td>\s*<td class="cell-num cell-strong">\s*(.*?)\s*</td>', 'Singleline')
foreach ($row in $rows) {
    Write-Output ("  {0,-24} {1,4} bookings  {2}" -f $row.Groups[1].Value.Trim(), $row.Groups[2].Value, $row.Groups[3].Value.Trim())
}

Write-Output ""
Write-Output "=== Chart bars (14-day series) ==="
$bars = [regex]::Matches($html, '<title>(.*?)</title>')
foreach ($b in $bars) { Write-Output ("  {0}" -f $b.Groups[1].Value) }

Write-Output ""
Write-Output "=== Today's appointments (from timeline) ==="
$times = [regex]::Matches($html, '<span class="timeline__time">\s*(.*?)\s*<small>(.*?)</small>', 'Singleline')
foreach ($t in $times) { Write-Output ("  time={0} ({1})" -f $t.Groups[1].Value.Trim(), $t.Groups[2].Value.Trim()) }

Write-Output ""
Write-Output "=== Pending queue seals ==="
$pending = [regex]::Matches($html, 'data-action="confirm-booking"', 'IgnoreCase')
Write-Output "  confirm buttons: $($pending.Count)"
