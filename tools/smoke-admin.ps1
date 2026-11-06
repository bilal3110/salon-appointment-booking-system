$ErrorActionPreference = 'Stop'
$base = 'http://127.0.0.1:8000'
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# --- login -------------------------------------------------------------
$login = Invoke-WebRequest -Uri "$base/admin-panel/login" -WebSession $session -UseBasicParsing
Write-Output "LOGIN PAGE  : $($login.StatusCode)  bytes=$($login.Content.Length)"

$token = ([regex]::Match($login.Content, 'name="csrf-token" content="([^"]+)"')).Groups[1].Value
$body = @{ email = 'admin@salon.test'; password = 'password' } | ConvertTo-Json
$auth = Invoke-WebRequest -Uri "$base/api/login" -Method POST -Body $body -ContentType 'application/json' `
    -Headers @{ 'X-CSRF-TOKEN' = $token } -WebSession $session -UseBasicParsing
Write-Output "AUTH        : $($auth.StatusCode)  $($auth.Content)"

# --- admin pages -------------------------------------------------------
$pages = @(
    @{ n = 'Dashboard';   p = '/admin-panel' },
    @{ n = 'Appointments';p = '/admin-panel/bookings' },
    @{ n = 'Clients';     p = '/admin-panel/clients' },
    @{ n = 'Services';    p = '/admin-panel/services' },
    @{ n = 'Service new'; p = '/admin-panel/services/create' },
    @{ n = 'Service edit';p = '/admin-panel/services/1/edit' },
    @{ n = 'Staff';       p = '/admin-panel/staff' },
    @{ n = 'Staff new';   p = '/admin-panel/staff/create' },
    @{ n = 'Staff view';  p = '/admin-panel/staff/1/view' },
    @{ n = 'Staff edit';  p = '/admin-panel/staff/1/edit' },
    @{ n = 'Accounts';    p = '/admin-panel/users' },
    @{ n = 'Account new'; p = '/admin-panel/users/create' },
    @{ n = 'Account edit';p = '/admin-panel/users/2/edit' },
    @{ n = 'Reviews';     p = '/admin-panel/testimonials' },
    @{ n = 'Reports';     p = '/admin-panel/reports' },
    @{ n = 'Settings';    p = '/admin-panel/settings' }
)

$fail = 0
foreach ($page in $pages) {
    try {
        $r = Invoke-WebRequest -Uri ($base + $page.p) -WebSession $session -UseBasicParsing
        $c = $r.Content
        $problems = @()
        if ($c -match 'Whoops|Symfony\\\\Component|ErrorException|Stack trace|Undefined variable|Fatal error') { $problems += 'PHP-ERROR' }
        if ($c -notmatch 'adm-sidebar') { $problems += 'NO-SHELL' }
        if ($c -notmatch 'i-layout-dashboard') { $problems += 'NO-ICONS' }
        if ($c -notmatch 'admin\.css') { $problems += 'NO-CSS' }
        if ($problems.Count -eq 0) {
            Write-Output ("OK   {0,-14} {1}  bytes={2}" -f $page.n, $r.StatusCode, $c.Length)
        } else {
            $fail++
            Write-Output ("FAIL {0,-14} {1}  [{2}]" -f $page.n, $r.StatusCode, ($problems -join ','))
            ($c -split "`n" | Select-String -Pattern 'Whoops|Symfony|Undefined variable|Fatal error' | Select-Object -First 3) | ForEach-Object { Write-Output "      $_" }
        }
    } catch {
        $fail++
        Write-Output ("FAIL {0,-14} {1}" -f $page.n, $_.Exception.Message)
    }
}

# --- api pages ---------------------------------------------------------
$apis = @('/api/bookings', '/api/services', '/api/staff', '/api/users', '/api/testimonials', '/api/staff-by-service/1')
foreach ($a in $apis) {
    try {
        $r = Invoke-WebRequest -Uri ($base + $a) -WebSession $session -UseBasicParsing
        Write-Output ("API  {0,-26} {1}  bytes={2}" -f $a, $r.StatusCode, $r.Content.Length)
    } catch {
        $fail++
        Write-Output ("API  {0,-26} FAIL {1}" -f $a, $_.Exception.Message)
    }
}

Write-Output ""
Write-Output "FAILURES: $fail"
