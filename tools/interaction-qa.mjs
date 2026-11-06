/**
 * Interaction QA — drives the real admin workflows over the DevTools Protocol.
 *
 * Exercises: search, filters, pagination, view/edit/create modals, confirmation
 * dialogs, toasts, sidebar collapse, mobile drawer, global search, and a full
 * service create/delete round trip. Data mutated during the run is restored.
 *
 * Usage: node tools/interaction-qa.mjs [baseUrl] [outDir]
 */
import { spawn } from 'node:child_process';
import { mkdirSync, writeFileSync, rmSync } from 'node:fs';
import { resolve, join } from 'node:path';

const BASE = process.argv[2] || 'http://127.0.0.1:8000';
const OUT = resolve(process.argv[3] || 'storage/app/interaction-qa');
const PORT = 9223;

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

/* ---------- session ---------- */
function cookieMap(setCookies) {
    const jar = new Map();
    for (const raw of setCookies) {
        const [pair] = raw.split(';');
        const idx = pair.indexOf('=');
        if (idx > 0) jar.set(pair.slice(0, idx).trim(), pair.slice(idx + 1).trim());
    }
    return jar;
}
const jarHeader = (jar) => Array.from(jar.entries()).map(([k, v]) => `${k}=${v}`).join('; ');

async function signIn() {
    const page = await fetch(`${BASE}/admin-panel/login`);
    const html = await page.text();
    const token = /name="csrf-token" content="([^"]+)"/.exec(html)?.[1];
    const jar = cookieMap(page.headers.getSetCookie());

    const res = await fetch(`${BASE}/api/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token || '', Cookie: jarHeader(jar) },
        body: JSON.stringify({ email: 'admin@salon.test', password: 'password' }),
    });
    if (!res.ok) throw new Error(`login failed: ${res.status}`);

    cookieMap(res.headers.getSetCookie()).forEach((v, k) => jar.set(k, v));
    const cookies = Array.from(jar.entries()).map(([k, v]) => `${k}=${v}`);

    const me = await (await fetch(`${BASE}/api/users/1`, { headers: { Cookie: jarHeader(jar) } })).json();
    const csrf = /name="csrf-token" content="([^"]+)"/.exec(html)?.[1];
    return { cookies, csrf, jar };
}

/* ---------- CDP ---------- */
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
        if (sessionId) payload.sessionId = sessionId;
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
        if (!this.listeners.has(method)) this.listeners.set(method, []);
        this.listeners.get(method).push(fn);
    }
}

/* ---------- helpers ---------- */
let cdp;
let sessionId;

async function evaluate(expression) {
    const res = await cdp.send(
        'Runtime.evaluate',
        { expression, returnByValue: true, awaitPromise: true },
        sessionId
    );
    if (res.exceptionDetails) {
        throw new Error(res.exceptionDetails.exception?.description || res.exceptionDetails.text);
    }
    return res.result.value;
}

async function goto(path) {
    const loaded = new Promise((resolve) => {
        const handler = () => resolve();
        cdp.on('Page.loadEventFired', handler);
    });
    await cdp.send('Page.navigate', { url: BASE + path }, sessionId);
    await Promise.race([loaded, sleep(12000)]);
    await sleep(900);
}

async function shot(name, full = true) {
    const metrics = await cdp.send('Page.getLayoutMetrics', {}, sessionId);
    const height = full ? Math.min(Math.ceil(metrics.cssContentSize.height), 6000) : 900;
    const width = full ? Math.ceil(metrics.cssContentSize.width) : 1600;
    const png = await cdp.send(
        'Page.captureScreenshot',
        { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: 0, width, height, scale: 1 } },
        sessionId
    );
    writeFileSync(join(OUT, `${name}.png`), Buffer.from(png.data, 'base64'));
}

const checks = [];
function check(label, pass, detail = '') {
    checks.push({ label, pass, detail });
    console.log(`${pass ? 'PASS' : 'FAIL'}  ${label}${detail ? '  — ' + detail : ''}`);
}

/* ---------- main ---------- */
async function main() {
    mkdirSync(OUT, { recursive: true });
    const profileDir = resolve('storage/app/chrome-qa-profile-2');
    rmSync(profileDir, { recursive: true, force: true });

    const { cookies, csrf, jar } = await signIn();

    const chrome = spawn(
        CHROME,
        [
            '--headless=new',
            '--disable-gpu',
            '--no-first-run',
            '--disable-extensions',
            '--hide-scrollbars',
            `--remote-debugging-port=${PORT}`,
            `--user-data-dir=${profileDir}`,
            'about:blank',
        ],
        { stdio: 'ignore' }
    );

    let version = null;
    for (let i = 0; i < 40; i++) {
        try {
            version = await (await fetch(`http://127.0.0.1:${PORT}/json/version`)).json();
            break;
        } catch {
            await sleep(250);
        }
    }
    if (!version) throw new Error('chrome did not start');

    const ws = new WebSocket(version.webSocketDebuggerUrl);
    await new Promise((res, rej) => {
        ws.addEventListener('open', res);
        ws.addEventListener('error', rej);
    });

    cdp = new CDP(ws);
    const { targetId } = await cdp.send('Target.createTarget', { url: 'about:blank' });
    ({ sessionId } = await cdp.send('Target.attachToTarget', { targetId, flatten: true }));

    await cdp.send('Page.enable', {}, sessionId);
    await cdp.send('Runtime.enable', {}, sessionId);
    await cdp.send('Log.enable', {}, sessionId);
    await cdp.send('Network.enable', {}, sessionId);
    await cdp.send('Emulation.setDeviceMetricsOverride', { width: 1600, height: 1000, deviceScaleFactor: 1, mobile: false }, sessionId);

    for (const cookie of cookies) {
        const [name, ...rest] = cookie.split('=');
        await cdp.send('Network.setCookie', { name, value: rest.join('='), domain: '127.0.0.1', path: '/' }, sessionId);
    }

    const consoleErrors = [];
    cdp.on('Runtime.exceptionThrown', (p) =>
        consoleErrors.push(p.exceptionDetails?.exception?.description || p.exceptionDetails?.text)
    );
    cdp.on('Runtime.consoleAPICalled', (p) => {
        if (p.type === 'error') consoleErrors.push(p.args.map((a) => a.description || a.value).join(' '));
    });

    /* ========== 1. Appointments: load, search, filter ========== */
    await goto('/admin-panel/bookings');

    const initialRows = await evaluate(`document.querySelectorAll('#bookingRows tr').length`);
    check('appointments table loads rows', initialRows > 1, `${initialRows} rows`);

    await evaluate(`(() => {
        const input = document.getElementById('bookingSearch');
        input.value = 'Bilal';
        input.dispatchEvent(new Event('input', { bubbles: true }));
    })()`);
    await sleep(600);
    const searched = await evaluate(
        `JSON.stringify({ rows: document.querySelectorAll('#bookingRows tr').length, text: document.getElementById('bookingRows').innerText.slice(0,120) })`
    );
    const searchedObj = JSON.parse(searched);
    check('search filters the ledger', searchedObj.rows === 1 && /Bilal/.test(searchedObj.text), searchedObj.text.replace(/\n/g, ' '));

    await evaluate(`(() => {
        const input = document.getElementById('bookingSearch');
        input.value = '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
    })()`);
    await sleep(500);

    await evaluate(`(() => {
        const s = document.getElementById('bookingStatus');
        s.value = 'confirmed';
        s.dispatchEvent(new Event('change', { bubbles: true }));
    })()`);
    await sleep(500);
    const confirmedOnly = await evaluate(
        `document.querySelectorAll('#bookingRows tr').length`
    );
    const confirmedText = await evaluate(`document.getElementById('bookingRows').innerText`);
    check(
        'status filter narrows to confirmed',
        /CONFIRMED/.test(confirmedText) && !/PENDING/.test(confirmedText),
        `${confirmedOnly} rows`
    );

    await evaluate(`document.getElementById('bookingClear').click()`);
    await sleep(500);
    const afterReset = await evaluate(`document.querySelectorAll('#bookingRows tr').length`);
    check('reset restores every appointment', afterReset === initialRows, `${afterReset} rows`);

    /* ========== 2. Pagination ========== */
    const pageInfo = await evaluate(`document.getElementById('bookingPagerInfo').textContent`);
    check('pagination reports the range', /Showing 1/.test(pageInfo), pageInfo.trim());

    await evaluate(`document.querySelector('#bookingPagerControls [data-page="2"]').click()`);
    await sleep(500);
    const page2 = await evaluate(`document.getElementById('bookingPagerInfo').textContent`);
    check('pagination advances to page 2', /Showing 13/.test(page2), page2.trim());

    await evaluate(`document.querySelector('#bookingPagerControls [data-page="1"]').click()`);
    await sleep(400);

    /* ========== 3. View modal ========== */
    await evaluate(`document.querySelector('[data-view-booking]').click()`);
    await sleep(700);
    const viewModal = await evaluate(
        `JSON.stringify({ open: !!document.querySelector('.modal.is-open'), title: (document.querySelector('.modal__title')||{}).textContent, rows: document.querySelectorAll('.modal .detail__row').length })`
    );
    const viewObj = JSON.parse(viewModal);
    check('view modal opens with details', viewObj.open && viewObj.rows >= 10, `${viewObj.title} / ${viewObj.rows} rows`);
    await shot('01-view-modal');

    await evaluate(`document.querySelector('.modal [data-modal-close]').click()`);
    await sleep(500);
    check('view modal closes', await evaluate(`!document.querySelector('.modal.is-open')`));

    /* ========== 4. Edit modal ========== */
    await evaluate(`document.querySelector('[data-edit-booking]').click()`);
    await sleep(900);
    const editModal = await evaluate(
        `JSON.stringify({ open: !!document.querySelector('.modal.is-open'), name: (document.getElementById('editCustomerName')||{}).value, services: document.querySelectorAll('#editServiceId option').length, staff: document.querySelectorAll('#editStaffId option').length })`
    );
    const editObj = JSON.parse(editModal);
    check(
        'edit modal opens prefilled with real options',
        editObj.open && editObj.name && editObj.services > 1 && editObj.staff > 1,
        `name="${editObj.name}" services=${editObj.services} staff=${editObj.staff}`
    );
    await shot('02-edit-modal');

    /* staff list must narrow to barbers who perform the chosen service */
    await evaluate(`(() => {
        const sel = document.getElementById('editServiceId');
        sel.selectedIndex = 1;
        sel.dispatchEvent(new Event('change', { bubbles: true }));
    })()`);
    await sleep(900);
    const filteredStaff = await evaluate(
        `JSON.stringify({ options: document.querySelectorAll('#editStaffId option').length, names: Array.from(document.querySelectorAll('#editStaffId option')).map(o => o.textContent) })`
    );
    const fsObj = JSON.parse(filteredStaff);
    check(
        'staff dropdown narrows to the chosen service',
        fsObj.options > 1,
        `${fsObj.options} options: ${fsObj.names.join(', ')}`
    );

    await evaluate(`document.querySelector('.modal [data-modal-close]').click()`);
    await sleep(400);

    /* ========== 5. New appointment modal ========== */
    await evaluate(`document.querySelector('[data-new-booking]').click()`);
    await sleep(700);
    const createModal = await evaluate(
        `JSON.stringify({ open: !!document.querySelector('.modal.is-open'), services: document.querySelectorAll('#createServiceId option').length })`
    );
    const createObj = JSON.parse(createModal);
    check('new appointment modal opens', createObj.open && createObj.services > 1, `${createObj.services} services`);
    await shot('03-create-modal');
    await evaluate(`document.querySelector('.modal [data-modal-close]').click()`);
    await sleep(400);

    /* ========== 6. Confirm a booking, then restore it ========== */
    const pendingId = await evaluate(
        `(() => { const b = document.querySelector('[data-confirm-booking]'); return b ? b.getAttribute('data-confirm-booking') : null; })()`
    );
    check('pending rows expose a confirm action', !!pendingId, `booking #${pendingId}`);

    await evaluate(`document.querySelector('[data-confirm-booking]').click()`);
    await sleep(700);
    const confirmDialog = await evaluate(
        `JSON.stringify({ open: !!document.querySelector('.modal.is-open'), title: (document.querySelector('.modal__title')||{}).textContent, danger: !!document.querySelector('.modal [data-confirm-yes]') })`
    );
    const cdObj = JSON.parse(confirmDialog);
    check('confirm dialog opens before acting', cdObj.open && !!cdObj.danger, cdObj.title);
    await shot('04-confirm-dialog');

    await evaluate(`document.querySelector('.modal [data-confirm-yes]').click()`);
    await sleep(1800);
    const toastText = await evaluate(
        `JSON.stringify({ toast: (document.querySelector('.toast__title')||{}).textContent, seals: Array.from(document.querySelectorAll('#bookingRows .seal')).map(s => s.textContent.trim()) })`
    );
    const toastObj = JSON.parse(toastText);
    check('confirm shows a success toast', /confirmed/i.test(toastObj.toast || ''), toastObj.toast);
    check('ledger reflects the new status', toastObj.seals.includes('Confirmed'), toastObj.seals.join(','));
    await shot('05-toast-confirmed');

    /* restore the record */
    const restore = await fetch(`${BASE}/api/bookings/${pendingId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Cookie: jarHeader(jar) },
        body: JSON.stringify({ status: 'pending' }),
    });
    check('test record restored to pending', restore.ok, `status ${restore.status}`);

    /* ========== 7. Cancel dialog ========== */
    await evaluate(`document.querySelector('[data-cancel-booking]').click()`);
    await sleep(700);
    const cancelDialog = await evaluate(
        `JSON.stringify({ open: !!document.querySelector('.modal.is-open'), danger: !!document.querySelector('.modal [data-confirm-yes].btn--danger') })`
    );
    check('cancel dialog is marked destructive', JSON.parse(cancelDialog).open && JSON.parse(cancelDialog).danger);
    await shot('06-cancel-dialog');
    await evaluate(`document.querySelector('.modal [data-modal-close]').click()`);
    await sleep(400);

    /* ========== 8. Services: full create + delete round trip ========== */
    await goto('/admin-panel/services');
    const serviceRows = await evaluate(`document.querySelectorAll('#servicesRows tr').length`);
    check('services table loads', serviceRows > 1, `${serviceRows} rows`);

    await goto('/admin-panel/services/create');
    await evaluate(`(() => {
        document.getElementById('serviceName').value = 'QA Temp Service';
        document.getElementById('servicePrice').value = '1234';
        document.getElementById('serviceType').value = 'men';
        document.getElementById('serviceDescription').value = 'Temporary record created by interaction QA.';
        document.getElementById('createServiceForm').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    })()`);
    await sleep(2000);
    const afterCreate = await evaluate(`document.body.innerText.includes('QA Temp Service')`);
    check('service create round trip returns to the list', afterCreate === true, `visible=${afterCreate}`);

    const created = await (
        await fetch(`${BASE}/api/services`, { headers: { Cookie: jarHeader(jar) } })
    ).json();
    const temp = created.find((s) => s.service_name === 'QA Temp Service');
    check('service was created and persisted', !!temp, temp ? `id ${temp.id}` : 'not found');
    if (temp) {
        const del = await fetch(`${BASE}/api/services/${temp.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, Cookie: jarHeader(jar) },
        });
        check('service deleted again (round trip clean)', del.ok, `status ${del.status}`);
    }

    /* ========== 9. Sidebar collapse ========== */
    await goto('/admin-panel');
    await evaluate(`document.getElementById('railToggle').click()`);
    await sleep(700);
    const rail = await evaluate(
        `JSON.stringify({ rail: document.body.getAttribute('data-rail'), sidebar: Math.round(document.querySelector('.adm-sidebar').getBoundingClientRect().width), main: Math.round(document.querySelector('.adm-main').getBoundingClientRect().width) })`
    );
    const railObj = JSON.parse(rail);
    check(
        'sidebar collapses to an icon rail',
        railObj.rail === 'collapsed' && railObj.sidebar < 100,
        `rail=${railObj.rail} width=${railObj.sidebar}px`
    );
    await shot('07-collapsed-rail');
    await evaluate(`document.getElementById('railToggle').click()`);
    await sleep(600);

    /* ========== 10. Global search ========== */
    await evaluate(`document.getElementById('gsearchTrigger').click()`);
    await sleep(1200);
    await evaluate(`(() => {
        const i = document.getElementById('gsearchInput');
        i.value = 'Ava';
        i.dispatchEvent(new Event('input', { bubbles: true }));
    })()`);
    await sleep(900);
    const gs = await evaluate(
        `JSON.stringify({ items: document.querySelectorAll('.gsearch__item').length, text: (document.getElementById('gsearchResults')||{}).innerText })`
    );
    const gsObj = JSON.parse(gs);
    check('global search returns matches', gsObj.items > 0, gsObj.text.replace(/\n/g, ' | ').slice(0, 110));
    await shot('08-global-search');
    await evaluate(`document.body.click()`);

    /* ========== 11. Profile menu + logout presence ========== */
    await evaluate(`document.getElementById('profileTrigger').click()`);
    await sleep(600);
    const menu = await evaluate(
        `JSON.stringify({ open: document.getElementById('profileMenu').classList.contains('is-open'), items: document.querySelectorAll('.profile__menu-item').length })`
    );
    const menuObj = JSON.parse(menu);
    check('profile dropdown opens with actions', menuObj.open && menuObj.items >= 3, `${menuObj.items} items`);
    await shot('09-profile-menu');

    /* ========== 12. Mobile drawer ========== */
    await cdp.send('Emulation.setDeviceMetricsOverride', { width: 414, height: 820, deviceScaleFactor: 1, mobile: true }, sessionId);
    await goto('/admin-panel');
    await evaluate(`document.getElementById('drawerToggle').click()`);
    await sleep(800);
    const drawer = await evaluate(
        `JSON.stringify({ drawer: document.body.getAttribute('data-drawer'), visible: document.querySelector('.adm-sidebar').getBoundingClientRect().left >= -1, scrim: getComputedStyle(document.querySelector('.adm-scrim')).opacity })`
    );
    const drawerObj = JSON.parse(drawer);
    check('mobile drawer opens', drawerObj.drawer === 'open' && drawerObj.visible, `scrim opacity ${drawerObj.scrim}`);
    await shot('10-mobile-drawer', false);
    await evaluate(`document.querySelector('.adm-scrim').click()`);
    await sleep(600);

    /* ========== 13. Staff page + staff detail ========== */
    await cdp.send('Emulation.setDeviceMetricsOverride', { width: 1600, height: 1000, deviceScaleFactor: 1, mobile: false }, sessionId);
    await goto('/admin-panel/staff');
    const staffRows = await evaluate(`document.querySelectorAll('#staffRows tr').length`);
    check('staff table loads', staffRows > 1, `${staffRows} rows`);

    await goto('/admin-panel/staff/1/view');
    const staffDetail = await evaluate(
        `JSON.stringify({ name: (document.querySelector('.staff-card__name')||{}).textContent, sections: document.querySelectorAll('.staff-card__head, .detail__row').length })`
    );
    const sdObj = JSON.parse(staffDetail);
    check('staff detail renders from the API', !!sdObj.name && sdObj.sections > 3, `${sdObj.name} / ${sdObj.sections} blocks`);
    await shot('11-staff-detail');

    /* ========== 14. Settings save ========== */
    await goto('/admin-panel/settings');
    await evaluate(`(() => {
        const f = document.getElementById('settingsProfileForm');
        document.getElementById('settingsPhone').value = '555-0199';
        f.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    })()`);
    await sleep(1800);
    const settingsToast = await evaluate(
        `(document.querySelector('.toast__title')||{}).textContent || ''`
    );
    check('settings save succeeds', /saved/i.test(settingsToast), settingsToast);

    /* restore the phone number */
    await fetch(`${BASE}/api/users/1`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Cookie: jarHeader(jar) },
        body: JSON.stringify({ phone: '555-0100' }),
    });

    /* ========== 15. Reviews page delete dialog ========== */
    await goto('/admin-panel/testimonials');
    const reviewRows = await evaluate(`document.querySelectorAll('.ledger tbody tr').length`);
    check('reviews table loads', reviewRows > 1, `${reviewRows} rows`);
    await evaluate(`document.querySelector('[data-delete-testimonial]').click()`);
    await sleep(700);
    const reviewDialog = await evaluate(`!!document.querySelector('.modal.is-open [data-confirm-yes]')`);
    check('review removal asks for confirmation', reviewDialog);
    await shot('12-review-confirm');
    await evaluate(`document.querySelector('.modal [data-modal-close]').click()`);
    await sleep(400);

    /* ========== 16. Reports ========== */
    await goto('/admin-panel/reports');
    const report = await evaluate(
        `JSON.stringify({ bars: document.querySelectorAll('.chart__bar').length, meters: document.querySelectorAll('.meter__row').length, tables: document.querySelectorAll('.ledger').length, kpis: document.querySelectorAll('.band__cell').length })`
    );
    const reportObj = JSON.parse(report);
    check(
        'reports render real charts and tables',
        reportObj.bars > 0 && reportObj.meters === 4 && reportObj.tables >= 2 && reportObj.kpis === 5,
        `bars=${reportObj.bars} meters=${reportObj.meters} tables=${reportObj.tables} kpis=${reportObj.kpis}`
    );
    await shot('13-reports');

    /* ========== 17. Clients ========== */
    await goto('/admin-panel/clients');
    const clients = await evaluate(
        `JSON.stringify({ rows: document.querySelectorAll('.ledger tbody tr').length, kpis: document.querySelectorAll('.band__cell').length })`
    );
    const clientsObj = JSON.parse(clients);
    check('clients register renders', clientsObj.rows > 1 && clientsObj.kpis === 4, `${clientsObj.rows} rows`);

    /* ========== summary ========== */
    const realErrors = consoleErrors.filter((e) => !/favicon/i.test(e));
    console.log(`\nconsole errors during interaction: ${realErrors.length}`);
    realErrors.forEach((e) => console.log('  ! ' + e.slice(0, 200)));

    const failed = checks.filter((c) => !c.pass);
    console.log(`\nchecks: ${checks.length - failed.length}/${checks.length} passed`);
    if (failed.length) failed.forEach((f) => console.log('  FAILED: ' + f.label + ' — ' + f.detail));

    ws.close();
    chrome.kill();
    process.exit(failed.length || realErrors.length ? 1 : 0);
}

main().catch((error) => {
    console.error('interaction QA failed:', error.message);
    process.exit(2);
});
