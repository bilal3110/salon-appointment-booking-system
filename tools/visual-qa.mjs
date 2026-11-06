/**
 * Visual + console QA for the admin panel.
 *
 * Drives headless Chrome over the DevTools Protocol:
 *   - signs in through the real /api/login endpoint
 *   - injects the session cookie
 *   - visits each admin page
 *   - captures a full-page PNG
 *   - collects console errors, uncaught exceptions and failed requests
 *
 * Usage: node tools/visual-qa.mjs [baseUrl] [outDir]
 */
import { spawn } from 'node:child_process';
import { mkdirSync, writeFileSync, rmSync } from 'node:fs';
import { resolve, join } from 'node:path';

const BASE = process.argv[2] || 'http://127.0.0.1:8000';
const OUT = resolve(process.argv[3] || 'storage/app/visual-qa');
const PORT = 9222;

const CHROME =
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' ||
    'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

const PAGES = [
    { name: '01-dashboard', path: '/admin-panel', width: 1600, height: 1150 },
    { name: '02-appointments', path: '/admin-panel/bookings', width: 1600, height: 1000 },
    { name: '03-clients', path: '/admin-panel/clients', width: 1600, height: 1000 },
    { name: '04-services', path: '/admin-panel/services', width: 1600, height: 950 },
    { name: '05-service-create', path: '/admin-panel/services/create', width: 1600, height: 900 },
    { name: '06-staff', path: '/admin-panel/staff', width: 1600, height: 950 },
    { name: '07-staff-view', path: '/admin-panel/staff/1/view', width: 1600, height: 850 },
    { name: '08-accounts', path: '/admin-panel/users', width: 1600, height: 950 },
    { name: '09-reviews', path: '/admin-panel/testimonials', width: 1600, height: 850 },
    { name: '10-reports', path: '/admin-panel/reports', width: 1600, height: 1500 },
    { name: '11-settings', path: '/admin-panel/settings', width: 1600, height: 950 },
    { name: '12-login', path: '/admin-panel/login', width: 1600, height: 900, anon: true },
    { name: '13-mobile-dashboard', path: '/admin-panel', width: 414, height: 900, mobile: true },
    { name: '14-mobile-appointments', path: '/admin-panel/bookings', width: 414, height: 900, mobile: true },
    { name: '15-tablet-dashboard', path: '/admin-panel', width: 900, height: 1000, mobile: true },
];

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

/* ---------- sign in ---------- */
function cookieMap(setCookies) {
    const jar = new Map();
    for (const raw of setCookies) {
        const [pair] = raw.split(';');
        const idx = pair.indexOf('=');
        if (idx > 0) {
            jar.set(pair.slice(0, idx).trim(), pair.slice(idx + 1).trim());
        }
    }
    return jar;
}

function jarHeader(jar) {
    return Array.from(jar.entries())
        .map(([k, v]) => `${k}=${v}`)
        .join('; ');
}

async function signIn() {
    const page = await fetch(`${BASE}/admin-panel/login`);
    const html = await page.text();
    const token = /name="csrf-token" content="([^"]+)"/.exec(html)?.[1];

    // The CSRF token lives in the session, so the session cookie must travel
    // with the POST — otherwise Laravel answers 419.
    const jar = cookieMap(page.headers.getSetCookie());

    const res = await fetch(`${BASE}/api/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token || '',
            Cookie: jarHeader(jar),
        },
        body: JSON.stringify({ email: 'admin@salon.test', password: 'password' }),
    });

    if (!res.ok) {
        throw new Error(`login failed: ${res.status}`);
    }

    cookieMap(res.headers.getSetCookie()).forEach((v, k) => jar.set(k, v));

    const cookies = Array.from(jar.entries()).map(([k, v]) => `${k}=${v}`);
    if (!cookies.length) {
        throw new Error('no session cookie returned');
    }
    return cookies;
}

/* ---------- chrome ---------- */
function launchChrome(profileDir) {
    const args = [
        '--headless=new',
        '--disable-gpu',
        '--no-first-run',
        '--no-default-browser-check',
        '--disable-extensions',
        '--hide-scrollbars',
        '--force-device-scale-factor=1',
        `--remote-debugging-port=${PORT}`,
        `--user-data-dir=${profileDir}`,
        'about:blank',
    ];
    return spawn(CHROME, args, { stdio: 'ignore', detached: false });
}

/* ---------- minimal CDP client ---------- */
class CDP {
    constructor(ws) {
        this.ws = ws;
        this.id = 0;
        this.pending = new Map();
        this.listeners = new Map();
        ws.addEventListener('message', (event) => {
            const msg = JSON.parse(event.data);
            if (msg.id && this.pending.has(msg.id)) {
                const { resolve, reject } = this.pending.get(msg.id);
                this.pending.delete(msg.id);
                msg.error ? reject(new Error(msg.error.message)) : resolve(msg.result);
            } else if (msg.method) {
                (this.listeners.get(msg.method) || []).forEach((fn) => fn(msg.params, msg.sessionId));
            }
        });
    }

    send(method, params = {}, sessionId) {
        const id = ++this.id;
        const payload = { id, method, params };
        if (sessionId) {
            payload.sessionId = sessionId;
        }
        this.ws.send(JSON.stringify(payload));
        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            setTimeout(() => {
                if (this.pending.has(id)) {
                    this.pending.delete(id);
                    reject(new Error(`timeout: ${method}`));
                }
            }, 30000);
        });
    }

    on(method, fn) {
        if (!this.listeners.has(method)) {
            this.listeners.set(method, []);
        }
        this.listeners.get(method).push(fn);
    }
}

/* ---------- main ---------- */
async function main() {
    mkdirSync(OUT, { recursive: true });
    const profileDir = resolve('storage/app/chrome-qa-profile');
    rmSync(profileDir, { recursive: true, force: true });

    const cookies = await signIn();
    console.log(`signed in with ${cookies.length} cookie(s)\n`);

    const chrome = launchChrome(profileDir);

    let version = null;
    for (let i = 0; i < 40; i++) {
        try {
            version = await (await fetch(`http://127.0.0.1:${PORT}/json/version`)).json();
            break;
        } catch (e) {
            await sleep(250);
        }
    }
    if (!version) {
        throw new Error('chrome did not start');
    }

    const ws = new WebSocket(version.webSocketDebuggerUrl);
    await new Promise((res, rej) => {
        ws.addEventListener('open', res);
        ws.addEventListener('error', rej);
    });

    const cdp = new CDP(ws);
    const { targetId } = await cdp.send('Target.createTarget', { url: 'about:blank' });
    const { sessionId } = await cdp.send('Target.attachToTarget', { targetId, flatten: true });

    await cdp.send('Page.enable', {}, sessionId);
    await cdp.send('Runtime.enable', {}, sessionId);
    await cdp.send('Log.enable', {}, sessionId);
    await cdp.send('Network.enable', {}, sessionId);

    const consoleErrors = [];
    cdp.on('Runtime.exceptionThrown', (p) => {
        consoleErrors.push(`uncaught: ${p.exceptionDetails?.exception?.description || p.exceptionDetails?.text}`);
    });
    cdp.on('Runtime.consoleAPICalled', (p) => {
        if (p.type === 'error' || p.type === 'warning') {
            consoleErrors.push(
                `console.${p.type}: ` + p.args.map((a) => a.description || a.value || a.type).join(' ')
            );
        }
    });
    cdp.on('Log.entryAdded', (p) => {
        if (p.entry.level === 'error') {
            consoleErrors.push(`log: ${p.entry.text} ${p.entry.url || ''}`);
        }
    });
    cdp.on('Network.loadingFailed', (p) => {
        consoleErrors.push(`request failed: ${p.errorText}`);
    });

    const results = [];

    for (const spec of PAGES) {
        consoleErrors.length = 0;

        await cdp.send(
            'Emulation.setDeviceMetricsOverride',
            {
                width: spec.width,
                height: spec.height,
                deviceScaleFactor: 1,
                mobile: !!spec.mobile,
            },
            sessionId
        );

        await cdp.send('Network.clearBrowserCookies', {}, sessionId);
        if (!spec.anon) {
            for (const cookie of cookies) {
                const [name, ...rest] = cookie.split('=');
                await cdp.send(
                    'Network.setCookie',
                    { name, value: rest.join('='), domain: '127.0.0.1', path: '/' },
                    sessionId
                );
            }
        }

        const loaded = new Promise((resolve) => {
            const handler = () => resolve();
            cdp.on('Page.loadEventFired', handler);
        });

        await cdp.send('Page.navigate', { url: BASE + spec.path }, sessionId);
        await Promise.race([loaded, sleep(12000)]);
        await sleep(1400);

        const metrics = await cdp.send('Page.getLayoutMetrics', {}, sessionId);
        const fullHeight = Math.min(Math.ceil(metrics.cssContentSize.height), 6000);

        const shot = await cdp.send(
            'Page.captureScreenshot',
            {
                format: 'png',
                captureBeyondViewport: true,
                clip: { x: 0, y: 0, width: spec.width, height: fullHeight, scale: 1 },
            },
            sessionId
        );

        const file = join(OUT, `${spec.name}.png`);
        writeFileSync(file, Buffer.from(shot.data, 'base64'));

        const probe = await cdp.send(
            'Runtime.evaluate',
            {
                expression: `JSON.stringify({
                    title: document.title,
                    bodyClass: document.body.className,
                    rail: document.body.getAttribute('data-rail'),
                    hOverflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 2,
                    scrollW: document.documentElement.scrollWidth,
                    clientW: document.documentElement.clientWidth,
                    icons: document.querySelectorAll('svg use').length,
                    brokenIcons: Array.from(document.querySelectorAll('svg use')).filter(u => {
                        const id = (u.getAttribute('href')||'').slice(1);
                        return id && !document.getElementById(id);
                    }).length,
                    navLinks: document.querySelectorAll('.nav-link').length,
                    activeNav: (document.querySelector('.nav-link.is-active .nav-link__text')||{}).textContent || null
                })`,
                returnByValue: true,
            },
            sessionId
        );

        const info = JSON.parse(probe.result.value);
        const errors = consoleErrors.filter((e) => !/favicon/i.test(e));

        results.push({ ...spec, info, errors });

        console.log(
            `${spec.name.padEnd(24)} ${String(spec.width).padStart(4)}px  h=${String(fullHeight).padEnd(5)}` +
                ` icons=${String(info.icons).padStart(3)} broken=${info.brokenIcons}` +
                ` nav=${info.navLinks} active=${(info.activeNav || '-').trim()}` +
                ` overflow=${info.hOverflow ? 'YES ' + info.scrollW + '>' + info.clientW : 'no'}` +
                ` errors=${errors.length}`
        );

        errors.forEach((e) => console.log(`      ! ${e.slice(0, 220)}`));
    }

    const totalErrors = results.reduce((sum, r) => sum + r.errors.length, 0);
    const broken = results.reduce((sum, r) => sum + r.info.brokenIcons, 0);
    const overflow = results.filter((r) => r.info.hOverflow).map((r) => r.name);

    console.log(`\nscreenshots -> ${OUT}`);
    console.log(`console errors: ${totalErrors}   broken icon refs: ${broken}`);
    console.log(`horizontal overflow: ${overflow.length ? overflow.join(', ') : 'none'}`);

    ws.close();
    chrome.kill();
    process.exit(totalErrors || broken || overflow.length ? 1 : 0);
}

main().catch((error) => {
    console.error('QA failed:', error.message);
    process.exit(2);
});
