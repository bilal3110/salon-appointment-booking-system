
(function () {
    'use strict';
    const $ = (selector, root) => (root || document).querySelector(selector);
    const $$ = (selector, root) => Array.from((root || document).querySelectorAll(selector));

    const csrfToken = () => ($('meta[name="csrf-token"]') || {}).content || '';

    async function api(url, options) {
        const opts = Object.assign(
            { headers: {}, credentials: 'same-origin' },
            options || {}
        );

        opts.headers = Object.assign(
            { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            opts.headers || {}
        );

        if (opts.body && !(opts.body instanceof FormData)) {
            opts.headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, opts);
        const contentType = response.headers.get('content-type') || '';
        let data = null;

        if (contentType.indexOf('application/json') !== -1) {
            try {
                data = await response.json();
            } catch (error) {
                data = null;
            }
        }

        if (!response.ok) {
            const failure = new Error(
                (data && (data.message || data.error)) || 'Request failed (' + response.status + ')'
            );
            failure.status = response.status;
            failure.data = data;
            if (data && data.errors) {
                failure.fields = data.errors;
            }
            throw failure;
        }

        return data;
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatMoney(value) {
        const amount = Number(value || 0);
        return amount.toLocaleString('en-US', { maximumFractionDigits: 0 }) + ' PKR';
    }

    function formatDate(value) {
        if (!value) {
            return '—';
        }
        const parsed = new Date(value);
        if (isNaN(parsed.getTime())) {
            return String(value);
        }
        return parsed.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatTime(value) {
        if (!value) {
            return '—';
        }
        const trimmed = String(value).trim();
        if (/^\d{1,2}:\d{2}$/.test(trimmed)) {
            return trimmed;
        }
        if (/^\d{1,2}:\d{2}\s*(am|pm)$/i.test(trimmed)) {
            return trimmed.replace(/\s+/g, ' ').replace(/am|pm/i, function (m) {
                return m.toUpperCase();
            });
        }
        return trimmed;
    }

    function toTimeInput(value) {
        if (!value) {
            return '';
        }
        const match = String(value).trim().match(/^(\d{1,2}):(\d{2})\s*(am|pm)?$/i);
        if (!match) {
            return '';
        }
        let hours = parseInt(match[1], 10);
        const meridiem = match[3] ? match[3].toLowerCase() : null;
        if (meridiem === 'pm' && hours < 12) {
            hours += 12;
        }
        if (meridiem === 'am' && hours === 12) {
            hours = 0;
        }
        return String(hours).padStart(2, '0') + ':' + match[2];
    }

    function initials(name) {
        return String(name || '?')
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map(function (part) {
                return part.charAt(0).toUpperCase();
            })
            .join('');
    }

    function debounce(fn, wait) {
        let timer = null;
        return function () {
            const args = arguments;
            const context = this;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(context, args);
            }, wait || 220);
        };
    }

    function todayISO() {
        const now = new Date();
        return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 10);
    }


    const TOAST_ICONS = {
        success: '#i-circle-check',
        error: '#i-circle-alert',
        warning: '#i-triangle-alert',
        info: '#i-circle-help'
    };

    function toast(title, text, type, timeout) {
        const stack = $('#toastStack');
        if (!stack) {
            return;
        }

        const kind = TOAST_ICONS[type] ? type : 'info';
        const element = document.createElement('div');
        element.className = 'toast toast--' + kind;
        element.setAttribute('role', kind === 'error' ? 'alert' : 'status');

        element.innerHTML =
            '<svg class="icon toast__icon" aria-hidden="true"><use href="' + TOAST_ICONS[kind] + '"></use></svg>' +
            '<div class="toast__body">' +
            '<p class="toast__title">' + escapeHtml(title) + '</p>' +
            (text ? '<p class="toast__text">' + escapeHtml(text) + '</p>' : '') +
            '</div>' +
            '<button type="button" class="toast__close" aria-label="Dismiss">' +
            '<svg class="icon icon--xs" aria-hidden="true"><use href="#i-x"></use></svg>' +
            '</button>';

        const dismiss = function () {
            if (!element.parentNode) {
                return;
            }
            element.classList.add('is-leaving');
            setTimeout(function () {
                if (element.parentNode) {
                    element.parentNode.removeChild(element);
                }
            }, 220);
        };

        $('button', element).addEventListener('click', dismiss);
        stack.appendChild(element);

        const life = timeout === undefined ? 4600 : timeout;
        if (life > 0) {
            setTimeout(dismiss, life);
        }

        return dismiss;
    }

    const FOCUSABLE =
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    let openModalCount = 0;

    function openModal(options) {
        const config = options || {};
        const id = config.id || 'admModal';

        const modal = document.createElement('div');
        modal.className = 'modal is-open';
        modal.id = id;
        modal.setAttribute('aria-hidden', 'false');

        const sizeClass = config.size ? ' modal__panel--' + config.size : '';

        modal.innerHTML =
            '<div class="modal__backdrop" data-modal-close></div>' +
            '<div class="modal__panel' + sizeClass + '" role="dialog" aria-modal="true" aria-labelledby="' + id + '-title">' +
            '<div class="modal__head">' +
            '<div>' +
            '<h2 class="modal__title" id="' + id + '-title">' + (config.title || '') + '</h2>' +
            (config.subtitle ? '<p class="modal__sub">' + config.subtitle + '</p>' : '') +
            '</div>' +
            '<button type="button" class="modal__close" data-modal-close aria-label="Close">' +
            '<svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>' +
            '</button>' +
            '</div>' +
            '<div class="modal__body">' + (config.body || '') + '</div>' +
            (config.footer ? '<div class="modal__foot">' + config.footer + '</div>' : '') +
            '</div>';

        document.body.appendChild(modal);
        openModalCount += 1;
        document.body.style.overflow = 'hidden';

        const previousFocus = document.activeElement;

        function close() {
            if (!modal.parentNode) {
                return;
            }
            modal.parentNode.removeChild(modal);
            openModalCount = Math.max(0, openModalCount - 1);
            if (openModalCount === 0) {
                document.body.style.overflow = '';
            }
            document.removeEventListener('keydown', onKeydown);
            if (previousFocus && previousFocus.focus) {
                previousFocus.focus();
            }
            if (typeof config.onClose === 'function') {
                config.onClose();
            }
        }

        function onKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                return;
            }
            if (event.key !== 'Tab') {
                return;
            }
            const focusable = $$(FOCUSABLE, modal).filter(function (node) {
                return node.offsetParent !== null;
            });
            if (!focusable.length) {
                return;
            }
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        $$('[data-modal-close]', modal).forEach(function (node) {
            node.addEventListener('click', close);
        });

        document.addEventListener('keydown', onKeydown);

        const focusTarget = $('[data-autofocus]', modal) || $$(FOCUSABLE, modal)[0];
        if (focusTarget) {
            setTimeout(function () {
                focusTarget.focus();
            }, 40);
        }

        if (typeof config.onMount === 'function') {
            config.onMount(modal, close);
        }

        modal.close = close;
        return modal;
    }

    function confirmDialog(options) {
        const config = options || {};
        const tone = config.tone === 'danger' ? 'btn--danger' : 'btn--primary';

        return new Promise(function (resolve) {
            let settled = false;

            const modal = openModal({
                id: 'admConfirm',
                size: 'narrow',
                title: config.title || 'Are you certain?',
                subtitle: config.subtitle || '',
                body:
                    '<p style="margin:0;color:var(--ivory-dim);font-size:14px;line-height:1.65">' +
                    (config.message || '') +
                    '</p>',
                footer:
                    '<button type="button" class="btn btn--ghost" data-modal-close>' +
                    escapeHtml(config.cancelLabel || 'Cancel') +
                    '</button>' +
                    '<button type="button" class="btn ' + tone + '" data-confirm-yes>' +
                    escapeHtml(config.confirmLabel || 'Confirm') +
                    '</button>',
                onClose: function () {
                    if (!settled) {
                        settled = true;
                        resolve(false);
                    }
                }
            });

            $('[data-confirm-yes]', modal).addEventListener('click', function () {
                settled = true;
                resolve(true);
                modal.close();
            });
        });
    }

    function formValues(form) {
        const values = {};
        $$('input, select, textarea', form).forEach(function (field) {
            if (!field.name || field.disabled) {
                return;
            }
            if (field.type === 'checkbox') {
                if (field.dataset.multi === 'true') {
                    if (!Array.isArray(values[field.name])) {
                        values[field.name] = [];
                    }
                    if (field.checked) {
                        values[field.name].push(field.value);
                    }
                } else {
                    values[field.name] = field.checked ? field.value || '1' : '';
                }
                return;
            }
            values[field.name] = field.value;
        });
        return values;
    }

    function clearFormErrors(form) {
        $$('.field.has-error', form).forEach(function (field) {
            field.classList.remove('has-error');
        });
        $$('.field__error', form).forEach(function (node) {
            node.innerHTML = '';
        });
    }

    function setFormError(form, name, message) {
        const field = form.querySelector('[name="' + name + '"]');
        if (!field) {
            return;
        }
        const wrapper = field.closest('.field');
        if (!wrapper) {
            return;
        }
        wrapper.classList.add('has-error');
        const slot = wrapper.querySelector('.field__error');
        if (slot) {
            slot.innerHTML =
                '<svg class="icon" aria-hidden="true"><use href="#i-circle-alert"></use></svg>' +
                '<span>' + escapeHtml(message) + '</span>';
        }
    }

    function applyServerErrors(form, errors) {
        if (!errors) {
            return;
        }
        Object.keys(errors).forEach(function (name) {
            const list = errors[name];
            setFormError(form, name, Array.isArray(list) ? list[0] : list);
        });
    }

    function setBusy(button, busy) {
        if (!button) {
            return;
        }
        button.classList.toggle('is-busy', !!busy);
        button.disabled = !!busy;
    }

    function initShell() {
        const body = document.body;
        $$('input[type="file"]').forEach(function (input) {
            input.addEventListener('change', function () {
                const label = document.getElementById(input.id + 'Name');
                if (!label) {
                    return;
                }
                const file = input.files && input.files[0];
                label.textContent = file ? file.name : 'No file chosen';
            });
        });

        /* Collapsible rail ---------------------------------------------------- */
        const railToggle = $('#railToggle');
        const stored = window.localStorage ? window.localStorage.getItem('garrison.rail') : null;

        if (stored === 'collapsed' && window.innerWidth > 1024) {
            body.setAttribute('data-rail', 'collapsed');
        }

        if (railToggle) {
            railToggle.addEventListener('click', function () {
                const collapsed = body.getAttribute('data-rail') === 'collapsed';
                body.setAttribute('data-rail', collapsed ? 'expanded' : 'collapsed');
                try {
                    window.localStorage.setItem('garrison.rail', collapsed ? 'expanded' : 'collapsed');
                } catch (error) {
                }
            });
        }

        const drawerToggle = $('#drawerToggle');
        const scrim = $('[data-drawer-close]');

        function setDrawer(open) {
            body.setAttribute('data-drawer', open ? 'open' : 'closed');
            if (drawerToggle) {
                drawerToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
        }

        if (drawerToggle) {
            drawerToggle.addEventListener('click', function () {
                setDrawer(body.getAttribute('data-drawer') !== 'open');
            });
        }

        if (scrim) {
            scrim.addEventListener('click', function () {
                setDrawer(false);
            });
        }

        const profile = $('#profileMenu');
        const profileTrigger = $('#profileTrigger');

        if (profile && profileTrigger) {
            profileTrigger.addEventListener('click', function (event) {
                event.stopPropagation();
                const open = profile.classList.toggle('is-open');
                profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                if (!profile.contains(event.target)) {
                    profile.classList.remove('is-open');
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    profile.classList.remove('is-open');
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }

        const logout = function (event) {
            if (event) {
                event.preventDefault();
            }
            api('/api/logout', { method: 'POST' })
                .then(function () {
                    window.location.href = '/admin-panel/login';
                })
                .catch(function (error) {
                    toast('Could not sign out', error.message, 'error');
                });
        };

        ['#logoutButton', '#logoutButtonProfile'].forEach(function (selector) {
            const button = $(selector);
            if (button) {
                button.addEventListener('click', logout);
            }
        });

        initGlobalSearch();
    }

    function initGlobalSearch() {
        const wrap = $('#gsearch');
        if (!wrap) {
            return;
        }

        const trigger = $('#gsearchTrigger');
        const input = $('#gsearchInput');
        const results = $('#gsearchResults');
        let index = { bookings: [], services: [], staff: [], users: [] };
        let loaded = false;

        function setOpen(open) {
            wrap.classList.toggle('is-open', open);
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                setTimeout(function () {
                    input.focus();
                }, 60);
            }
        }

        async function loadIndex() {
            if (loaded) {
                return;
            }
            try {
                const [bookings, services, staff, users] = await Promise.all([
                    api('/api/bookings').catch(function () {
                        return [];
                    }),
                    api('/api/services').catch(function () {
                        return [];
                    }),
                    api('/api/staff').catch(function () {
                        return [];
                    }),
                    api('/api/users').catch(function () {
                        return [];
                    })
                ]);
                index = { bookings: bookings, services: services, staff: staff, users: users };
                loaded = true;
            } catch (error) {
                index = { bookings: [], services: [], staff: [], users: [] };
            }
        }

        function group(label, items) {
            if (!items.length) {
                return '';
            }
            return (
                '<div class="gsearch__group">' +
                '<p class="gsearch__group-label">' + label + '</p>' +
                items.join('') +
                '</div>'
            );
        }

        function resultItem(icon, label, meta, href) {
            return (
                '<a class="gsearch__item" href="' + href + '">' +
                '<svg class="icon" aria-hidden="true"><use href="' + icon + '"></use></svg>' +
                '<span>' + escapeHtml(label) + '</span>' +
                (meta ? '<small>' + escapeHtml(meta) + '</small>' : '') +
                '</a>'
            );
        }

        function render(term) {
            const query = term.trim().toLowerCase();

            if (!query) {
                results.innerHTML =
                    '<p class="gsearch__empty">Type to search clients, appointments, services and staff.</p>';
                return;
            }

            const matches = {
                bookings: index.bookings.filter(function (booking) {
                    return (
                        String(booking.customer_name || '').toLowerCase().indexOf(query) !== -1 ||
                        String(booking.customer_email || '').toLowerCase().indexOf(query) !== -1 ||
                        String(booking.service && booking.service.service_name ? booking.service.service_name : '')
                            .toLowerCase()
                            .indexOf(query) !== -1
                    );
                }).slice(0, 5),
                services: index.services.filter(function (service) {
                    return String(service.service_name || '').toLowerCase().indexOf(query) !== -1;
                }).slice(0, 5),
                staff: index.staff.filter(function (staff) {
                    return String(staff.user && staff.user.name ? staff.user.name : '')
                        .toLowerCase()
                        .indexOf(query) !== -1;
                }).slice(0, 5),
                users: index.users.filter(function (user) {
                    return (
                        String(user.name || '').toLowerCase().indexOf(query) !== -1 ||
                        String(user.email || '').toLowerCase().indexOf(query) !== -1
                    );
                }).slice(0, 5)
            };

            const blocks = [];

            if (matches.bookings.length) {
                blocks.push(
                    group(
                        'Appointments',
                        matches.bookings.map(function (booking) {
                            return resultItem(
                                '#i-calendar-days',
                                booking.customer_name || 'Unnamed client',
                                formatDate(booking.booking_date) + ' · ' + formatTime(booking.booking_time),
                                '/admin-panel/bookings'
                            );
                        })
                    )
                );
            }

            if (matches.services.length) {
                blocks.push(
                    group(
                        'Services',
                        matches.services.map(function (service) {
                            return resultItem(
                                '#i-scissors',
                                service.service_name,
                                formatMoney(service.price),
                                '/admin-panel/services/' + service.id + '/edit'
                            );
                        })
                    )
                );
            }

            if (matches.staff.length) {
                blocks.push(
                    group(
                        'Staff',
                        matches.staff.map(function (staff) {
                            return resultItem(
                                '#i-contact',
                                staff.user ? staff.user.name : 'Unknown',
                                'Barber',
                                '/admin-panel/staff/' + staff.id + '/view'
                            );
                        })
                    )
                );
            }

            if (matches.users.length) {
                blocks.push(
                    group(
                        'Accounts',
                        matches.users.map(function (user) {
                            return resultItem(
                                '#i-id-card',
                                user.name,
                                user.type,
                                '/admin-panel/users/' + user.id + '/edit'
                            );
                        })
                    )
                );
            }

            results.innerHTML = blocks.length
                ? blocks.join('')
                : '<p class="gsearch__empty">Nothing in the house matches that.</p>';
        }

        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            const open = !wrap.classList.contains('is-open');
            setOpen(open);
            if (open) {
                loadIndex().then(function () {
                    render(input.value);
                });
            }
        });

        document.addEventListener('click', function (event) {
            if (!wrap.contains(event.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && wrap.classList.contains('is-open')) {
                setOpen(false);
                trigger.focus();
            }
            if (event.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName)) {
                event.preventDefault();
                setOpen(true);
                loadIndex().then(function () {
                    render(input.value);
                });
            }
        });

        input.addEventListener(
            'input',
            debounce(function () {
                render(input.value);
            }, 180)
        );
    }

    async function confirmBooking(id, onDone) {
        const accepted = await confirmDialog({
            title: 'Confirm this appointment?',
            subtitle: 'The client will see it as confirmed.',
            message:
                'Confirming holds the chair and moves the appointment out of the pending queue. You can still cancel it later.',
            confirmLabel: 'Confirm appointment',
            cancelLabel: 'Leave pending'
        });

        if (!accepted) {
            return;
        }

        try {
            await api('/api/bookings/' + id + '/confirm', { method: 'GET' });
            toast('Appointment confirmed', 'The booking has moved to confirmed.', 'success');
            if (typeof onDone === 'function') {
                onDone();
            }
        } catch (error) {
            toast('Could not confirm', error.message, 'error');
        }
    }

    async function cancelBooking(id, onDone) {
        const accepted = await confirmDialog({
            title: 'Cancel this appointment?',
            subtitle: 'This cannot be undone from the ledger.',
            message:
                'Cancelling releases the chair and marks the appointment as cancelled. The client record stays intact.',
            confirmLabel: 'Cancel appointment',
            cancelLabel: 'Keep booking',
            tone: 'danger'
        });

        if (!accepted) {
            return;
        }

        try {
            await api('/api/bookings/' + id + '/cancel', { method: 'GET' });
            toast('Appointment cancelled', 'The chair has been released.', 'warning');
            if (typeof onDone === 'function') {
                onDone();
            }
        } catch (error) {
            toast('Could not cancel', error.message, 'error');
        }
    }

    async function deleteBooking(id, onDone) {
        const accepted = await confirmDialog({
            title: 'Remove this appointment?',
            subtitle: 'Permanent — the record is deleted.',
            message:
                'This permanently removes the appointment from the ledger. Prefer cancelling if you only need to free the chair.',
            confirmLabel: 'Delete permanently',
            cancelLabel: 'Keep it',
            tone: 'danger'
        });

        if (!accepted) {
            return;
        }

        try {
            await api('/api/bookings/' + id, { method: 'DELETE' });
            toast('Appointment deleted', 'The record has been removed.', 'success');
            if (typeof onDone === 'function') {
                onDone();
            }
        } catch (error) {
            toast('Could not delete', error.message, 'error');
        }
    }

    function initBookingShortcuts() {
        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-action]');
            if (!trigger) {
                return;
            }

            const action = trigger.getAttribute('data-action');
            const id = trigger.getAttribute('data-id');

            if (action === 'confirm-booking') {
                event.preventDefault();
                confirmBooking(id, function () {
                    window.location.reload();
                });
            } else if (action === 'cancel-booking') {
                event.preventDefault();
                cancelBooking(id, function () {
                    window.location.reload();
                });
            } else if (action === 'delete-booking') {
                event.preventDefault();
                deleteBooking(id, function () {
                    window.location.reload();
                });
            }
        });
    }

    function initAppointments() {
        const tableBody = $('#bookingRows');
        if (!tableBody) {
            return;
        }

        const state = {
            rows: [],
            filtered: [],
            page: 1,
            perPage: 12,
            services: [],
            staff: []
        };

        const searchInput = $('#bookingSearch');
        const statusFilter = $('#bookingStatus');
        const staffFilter = $('#bookingStaff');
        const dateFrom = $('#bookingFrom');
        const dateTo = $('#bookingTo');
        const clearButton = $('#bookingClear');
        const countLabel = $('#bookingCount');
        const pagerInfo = $('#bookingPagerInfo');
        const pagerControls = $('#bookingPagerControls');
        const pageSizeSelect = $('#bookingPageSize');

        function statusSeal(status) {
            const known = ['pending', 'confirmed', 'completed', 'canceled'];
            const key = known.indexOf(status) !== -1 ? status : 'pending';
            const label = status === 'canceled' ? 'Cancelled' : status.charAt(0).toUpperCase() + status.slice(1);
            return '<span class="seal seal--' + key + '">' + escapeHtml(label) + '</span>';
        }

        function renderRows() {
            const start = (state.page - 1) * state.perPage;
            const slice = state.filtered.slice(start, start + state.perPage);

            if (!slice.length) {
                const emptyTitle = state.rows.length ? 'Nothing matches those filters' : 'No appointments yet';
                const emptyText = state.rows.length
                    ? 'Loosen the search or clear the filters to see the rest of the ledger.'
                    : 'When clients book through the public site, their appointments will land here.';

                tableBody.innerHTML =
                    '<tr><td colspan="8">' +
                    '<div class="empty">' +
                    '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-inbox"></use></svg></span>' +
                    '<h3 class="empty__title">' + emptyTitle + '</h3>' +
                    '<p class="empty__text">' + emptyText + '</p>' +
                    (state.rows.length
                        ? '<div class="empty__action"><button type="button" class="btn btn--ghost" id="emptyClearFilters">Clear filters</button></div>'
                        : '') +
                    '</div>' +
                    '</td></tr>';

                const emptyClear = $('#emptyClearFilters');
                if (emptyClear) {
                    emptyClear.addEventListener('click', clearFilters);
                }
                return;
            }

            tableBody.innerHTML = slice
                .map(function (booking) {
                    const service = booking.service || {};
                    const staffName = booking.staff && booking.staff.user ? booking.staff.user.name : 'Unassigned';
                    const canConfirm = booking.status === 'pending';
                    const canCancel = booking.status === 'pending' || booking.status === 'confirmed';

                    return (
                        '<tr>' +
                        '<td class="row-mark cell-ref">#' + escapeHtml(booking.id) + '</td>' +
                        '<td>' +
                        '<div class="person">' +
                        '<span class="initials">' + escapeHtml(initials(booking.customer_name)) + '</span>' +
                        '<span><span class="person__name">' + escapeHtml(booking.customer_name) + '</span>' +
                        '<span class="person__meta">' + escapeHtml(booking.customer_email) + '</span>' +
                        '<span class="person__meta">' + escapeHtml(booking.customer_contact || 'No contact number') + '</span></span>' +
                        '</div></td>' +
                        '<td><span class="cell-strong">' + escapeHtml(service.service_name || '—') + '</span>' +
                        '<span class="cell-sub">' + escapeHtml(service.service_type ? service.service_type : '') + '</span></td>' +
                        '<td class="cell-nowrap">' + escapeHtml(staffName) + '</td>' +
                        '<td class="cell-num cell-nowrap">' + escapeHtml(formatDate(booking.booking_date)) +
                        '<span class="cell-sub">' + escapeHtml(formatTime(booking.booking_time)) + '</span></td>' +
                        '<td class="cell-num cell-strong">' + escapeHtml(formatMoney(service.price)) + '</td>' +
                        '<td>' + statusSeal(booking.status) + '</td>' +
                        '<td><div class="ledger__actions">' +
                        '<button type="button" class="icon-btn" data-view-booking="' + booking.id + '" title="View details" aria-label="View appointment ' + escapeHtml(booking.customer_name) + '">' +
                        '<svg class="icon" aria-hidden="true"><use href="#i-eye"></use></svg></button>' +
                        '<button type="button" class="icon-btn" data-edit-booking="' + booking.id + '" title="Edit appointment" aria-label="Edit appointment ' + escapeHtml(booking.customer_name) + '">' +
                        '<svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg></button>' +
                        (canConfirm
                            ? '<button type="button" class="icon-btn icon-btn--gold" data-confirm-booking="' + booking.id + '" title="Confirm appointment" aria-label="Confirm appointment ' + escapeHtml(booking.customer_name) + '">' +
                              '<svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg></button>'
                            : '') +
                        (canCancel
                            ? '<button type="button" class="icon-btn icon-btn--danger" data-cancel-booking="' + booking.id + '" title="Cancel appointment" aria-label="Cancel appointment ' + escapeHtml(booking.customer_name) + '">' +
                              '<svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg></button>'
                            : '') +
                        '<button type="button" class="icon-btn icon-btn--danger" data-delete-booking="' + booking.id + '" title="Delete appointment" aria-label="Delete appointment ' + escapeHtml(booking.customer_name) + '">' +
                        '<svg class="icon" aria-hidden="true"><use href="#i-trash-2"></use></svg></button>' +
                        '</div></td>' +
                        '</tr>'
                    );
                })
                .join('');
        }

        function renderPager() {
            const pages = Math.max(1, Math.ceil(state.filtered.length / state.perPage));

            if (state.page > pages) {
                state.page = pages;
            }

            const from = state.filtered.length ? (state.page - 1) * state.perPage + 1 : 0;
            const to = Math.min(state.page * state.perPage, state.filtered.length);

            if (pagerInfo) {
                pagerInfo.textContent =
                    state.filtered.length === 0
                        ? 'No records'
                        : 'Showing ' + from + '–' + to + ' of ' + state.filtered.length;
            }

            if (!pagerControls) {
                return;
            }

            if (pages <= 1) {
                pagerControls.innerHTML = '';
                return;
            }

            let html =
                '<button type="button" class="pager__btn" data-page="' + (state.page - 1) + '"' +
                (state.page === 1 ? ' disabled' : '') +
                ' aria-label="Previous page"><svg class="icon" aria-hidden="true"><use href="#i-chevron-left"></use></svg></button>';

            const window_ = [];
            const push = function (n) {
                if (window_.indexOf(n) === -1 && n >= 1 && n <= pages) {
                    window_.push(n);
                }
            };

            push(1);
            for (let n = state.page - 1; n <= state.page + 1; n += 1) {
                push(n);
            }
            push(pages);
            window_.sort(function (a, b) {
                return a - b;
            });

            let previous = 0;
            window_.forEach(function (n) {
                if (previous && n - previous > 1) {
                    html += '<span class="pager__gap">…</span>';
                }
                html +=
                    '<button type="button" class="pager__btn' + (n === state.page ? ' is-active' : '') +
                    '" data-page="' + n + '"' + (n === state.page ? ' aria-current="page"' : '') + '>' + n + '</button>';
                previous = n;
            });

            html +=
                '<button type="button" class="pager__btn" data-page="' + (state.page + 1) + '"' +
                (state.page === pages ? ' disabled' : '') +
                ' aria-label="Next page"><svg class="icon" aria-hidden="true"><use href="#i-chevron-right"></use></svg></button>';

            pagerControls.innerHTML = html;
        }

        function applyFilters() {
            const term = (searchInput ? searchInput.value : '').trim().toLowerCase();
            const status = statusFilter ? statusFilter.value : '';
            const staffId = staffFilter ? staffFilter.value : '';
            const from = dateFrom ? dateFrom.value : '';
            const to = dateTo ? dateTo.value : '';

            state.filtered = state.rows.filter(function (booking) {
                const service = booking.service || {};
                const staffName = booking.staff && booking.staff.user ? booking.staff.user.name : '';
                const haystack = [
                    booking.customer_name,
                    booking.customer_email,
                    booking.customer_contact,
                    service.service_name,
                    staffName
                ]
                    .join(' ')
                    .toLowerCase();

                if (term && haystack.indexOf(term) === -1) {
                    return false;
                }
                if (status && booking.status !== status) {
                    return false;
                }
                if (staffId && String(booking.staff_id) !== staffId) {
                    return false;
                }
                if (from && String(booking.booking_date) < from) {
                    return false;
                }
                if (to && String(booking.booking_date) > to) {
                    return false;
                }
                return true;
            });

            state.page = 1;

            if (countLabel) {
                countLabel.textContent =
                    state.filtered.length === state.rows.length
                        ? state.rows.length + ' appointment' + (state.rows.length === 1 ? '' : 's')
                        : state.filtered.length + ' of ' + state.rows.length + ' appointments';
            }

            renderRows();
            renderPager();
        }

        function clearFilters() {
            if (searchInput) {
                searchInput.value = '';
                searchInput.closest('.search').classList.remove('has-value');
            }
            [statusFilter, staffFilter, dateFrom, dateTo].forEach(function (field) {
                if (field) {
                    field.value = '';
                }
            });
            applyFilters();
        }

        function findBooking(id) {
            for (let i = 0; i < state.rows.length; i += 1) {
                if (String(state.rows[i].id) === String(id)) {
                    return state.rows[i];
                }
            }
            return null;
        }

        function openDetail(id) {
            const booking = findBooking(id);
            if (!booking) {
                return;
            }

            const service = booking.service || {};
            const staff = booking.staff && booking.staff.user ? booking.staff.user : null;
            const rows = [
                ['Client', booking.customer_name],
                ['Email', booking.customer_email],
                ['Contact', booking.customer_contact],
                ['Service', service.service_name],
                ['Service type', service.service_type],
                ['Barber', staff ? staff.name : 'Unassigned'],
                ['Date', formatDate(booking.booking_date)],
                ['Time', formatTime(booking.booking_time)],
                ['Value', formatMoney(service.price)],
                ['Status', null],
                ['Notes', booking.notes]
            ];

            const body = rows
                .map(function (row) {
                    let value;
                    if (row[0] === 'Status') {
                        value = statusSeal(booking.status);
                    } else if (!row[1]) {
                        value = '<span class="text-smoke">—</span>';
                    } else {
                        value = escapeHtml(row[1]);
                    }
                    return (
                        '<div class="detail__row">' +
                        '<span class="detail__key">' + escapeHtml(row[0]) + '</span>' +
                        '<span class="detail__val' + (row[1] ? '' : ' detail__val--muted') + '">' + value + '</span>' +
                        '</div>'
                    );
                })
                .join('');

            openModal({
                id: 'admBookingDetail',
                title: 'Appointment #' + booking.id,
                subtitle: 'Recorded ' + formatDate(booking.booking_date) + ' at ' + formatTime(booking.booking_time),
                body: '<div class="detail">' + body + '</div>',
                footer:
                    '<button type="button" class="btn btn--ghost" data-modal-close>Close</button>' +
                    '<button type="button" class="btn btn--primary" data-edit-from-detail="' + booking.id + '">' +
                    '<svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg> Edit</button>'
            });
        }

        function openEditor(id) {
            const booking = findBooking(id);
            if (!booking) {
                return;
            }

            const serviceOptions = state.services
                .map(function (service) {
                    return (
                        '<option value="' + service.id + '"' +
                        (String(service.id) === String(booking.service_id) ? ' selected' : '') +
                        '>' + escapeHtml(service.service_name) + ' — ' + escapeHtml(formatMoney(service.price)) + '</option>'
                    );
                })
                .join('');

            const staffOptions = state.staff
                .map(function (member) {
                    const name = member.user ? member.user.name : 'Unknown';
                    return (
                        '<option value="' + member.id + '"' +
                        (String(member.id) === String(booking.staff_id) ? ' selected' : '') +
                        '>' + escapeHtml(name) + '</option>'
                    );
                })
                .join('');

            const modal = openModal({
                id: 'admBookingEdit',
                size: 'wide',
                title: 'Edit appointment #' + booking.id,
                subtitle: 'Changes take effect immediately in the ledger.',
                body:
                    '<form id="bookingEditForm" novalidate>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="editCustomerName">Client name <span class="req">*</span></label>' +
                    '<input class="input" type="text" id="editCustomerName" name="customer_name" value="' + escapeHtml(booking.customer_name) + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="editCustomerContact">Contact <span class="req">*</span></label>' +
                    '<input class="input" type="text" id="editCustomerContact" name="customer_contact" value="' + escapeHtml(booking.customer_contact) + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field"><label class="field__label" for="editCustomerEmail">Email <span class="req">*</span></label>' +
                    '<input class="input" type="email" id="editCustomerEmail" name="customer_email" value="' + escapeHtml(booking.customer_email) + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="editServiceId">Service <span class="req">*</span></label>' +
                    '<select class="select" id="editServiceId" name="service_id" required><option value="">Select a service</option>' + serviceOptions + '</select>' +
                    '<p class="field__hint">Only barbers who perform this service can be assigned.</p>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="editStaffId">Barber <span class="req">*</span></label>' +
                    '<select class="select" id="editStaffId" name="staff_id" required><option value="">Select a barber</option>' + staffOptions + '</select>' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="editBookingDate">Date <span class="req">*</span></label>' +
                    '<input class="input" type="date" id="editBookingDate" name="booking_date" value="' + escapeHtml(String(booking.booking_date || '').slice(0, 10)) + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="editBookingTime">Time <span class="req">*</span></label>' +
                    '<input class="input" type="time" id="editBookingTime" name="booking_time" value="' + escapeHtml(toTimeInput(booking.booking_time)) + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="editStatus">Status <span class="req">*</span></label>' +
                    '<select class="select" id="editStatus" name="status" required>' +
                    ['pending', 'confirmed', 'completed', 'canceled']
                        .map(function (option) {
                            return (
                                '<option value="' + option + '"' +
                                (booking.status === option ? ' selected' : '') +
                                '>' + (option === 'canceled' ? 'Cancelled' : option.charAt(0).toUpperCase() + option.slice(1)) + '</option>'
                            );
                        })
                        .join('') +
                    '</select><p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="editNotes">Notes</label>' +
                    '<input class="input" type="text" id="editNotes" name="notes" value="' + escapeHtml(booking.notes || '') + '" placeholder="Anything the barber should know">' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '</form>',
                footer:
                    '<button type="button" class="btn btn--ghost" data-modal-close>Cancel</button>' +
                    '<button type="submit" form="bookingEditForm" class="btn btn--primary" data-save-booking>' +
                    '<svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg> Save changes</button>'
            });

            const form = $('#bookingEditForm', modal);
            const serviceSelect = $('#editServiceId', modal);
            const staffSelect = $('#editStaffId', modal);

            async function refreshStaff(serviceId, preferred) {
                if (!serviceId) {
                    return;
                }
                try {
                    const list = await api('/api/staff-by-service/' + serviceId);
                    staffSelect.innerHTML =
                        '<option value="">Select a barber</option>' +
                        list
                            .map(function (member) {
                                return (
                                    '<option value="' + member.id + '"' +
                                    (String(member.id) === String(preferred) ? ' selected' : '') +
                                    '>' + escapeHtml(member.name) + '</option>'
                                );
                            })
                            .join('');
                } catch (error) {
                    /* keep whatever options we already have */
                }
            }

            serviceSelect.addEventListener('change', function () {
                refreshStaff(serviceSelect.value, booking.staff_id);
            });

            if (booking.service_id) {
                refreshStaff(booking.service_id, booking.staff_id);
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearFormErrors(form);

                const submit = $('[data-save-booking]', modal);
                const values = formValues(form);

                if (!values.customer_name || !values.customer_contact || !values.customer_email) {
                    if (!values.customer_name) {
                        setFormError(form, 'customer_name', 'Tell us who the client is.');
                    }
                    if (!values.customer_contact) {
                        setFormError(form, 'customer_contact', 'A contact number is required.');
                    }
                    if (!values.customer_email) {
                        setFormError(form, 'customer_email', 'An email address is required.');
                    }
                    return;
                }

                setBusy(submit, true);

                try {
                    await api('/api/bookings/' + booking.id, {
                        method: 'POST',
                        body: JSON.stringify(values)
                    });
                    toast('Appointment updated', 'The ledger has been updated.', 'success');
                    modal.close();
                    load();
                } catch (error) {
                    applyServerErrors(form, error.fields);
                    toast('Could not save', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }

        function openCreator() {
            const serviceOptions = state.services
                .map(function (service) {
                    return (
                        '<option value="' + service.id + '">' +
                        escapeHtml(service.service_name) + ' — ' + escapeHtml(formatMoney(service.price)) +
                        '</option>'
                    );
                })
                .join('');

            const modal = openModal({
                id: 'admBookingCreate',
                size: 'wide',
                title: 'New Appointment',
                subtitle: 'For clients who book in person rather than online.',
                body:
                    '<form id="bookingCreateForm" novalidate>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="createCustomerName">Client name <span class="req">*</span></label>' +
                    '<input class="input" type="text" id="createCustomerName" name="customer_name" required autocomplete="off" data-autofocus>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="createCustomerContact">Contact <span class="req">*</span></label>' +
                    '<input class="input" type="text" id="createCustomerContact" name="customer_contact" required autocomplete="off">' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field"><label class="field__label" for="createCustomerEmail">Email <span class="req">*</span></label>' +
                    '<input class="input" type="email" id="createCustomerEmail" name="customer_email" required autocomplete="off">' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="createServiceId">Service <span class="req">*</span></label>' +
                    '<select class="select" id="createServiceId" name="service_id" required>' +
                    (serviceOptions ? '<option value="">Select a service</option>' + serviceOptions : '<option value="">No services defined</option>') +
                    '</select><p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="createStaffId">Barber <span class="req">*</span></label>' +
                    '<select class="select" id="createStaffId" name="staff_id" required>' +
                    '<option value="">Select a barber</option></select>' +
                    '<p class="field__hint">Only barbers who perform the chosen service can be assigned.</p>' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="createBookingDate">Date <span class="req">*</span></label>' +
                    '<input class="input" type="date" id="createBookingDate" name="booking_date" value="' + todayISO() + '" required>' +
                    '<p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="createBookingTime">Time <span class="req">*</span></label>' +
                    '<input class="input" type="time" id="createBookingTime" name="booking_time" required>' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '<div class="field-row">' +
                    '<div class="field"><label class="field__label" for="createStatus">Status <span class="req">*</span></label>' +
                    '<select class="select" id="createStatus" name="status" required>' +
                    '<option value="pending">Pending</option>' +
                    '<option value="confirmed">Confirmed</option>' +
                    '<option value="completed">Completed</option>' +
                    '<option value="canceled">Cancelled</option>' +
                    '</select><p class="field__error"></p></div>' +
                    '<div class="field"><label class="field__label" for="createNotes">Notes</label>' +
                    '<input class="input" type="text" id="createNotes" name="notes" placeholder="Anything the barber should know">' +
                    '<p class="field__error"></p></div>' +
                    '</div>' +
                    '</form>',
                footer:
                    '<button type="button" class="btn btn--ghost" data-modal-close>Cancel</button>' +
                    '<button type="submit" form="bookingCreateForm" class="btn btn--primary" data-save-booking>' +
                    '<svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg> Create appointment</button>'
            });

            const form = $('#bookingCreateForm', modal);
            const serviceSelect = $('#createServiceId', modal);
            const staffSelect = $('#createStaffId', modal);

            serviceSelect.addEventListener('change', function () {
                const serviceId = serviceSelect.value;
                staffSelect.innerHTML = '<option value="">Select a barber</option>';

                if (!serviceId) {
                    return;
                }

                api('/api/staff-by-service/' + serviceId)
                    .then(function (list) {
                        staffSelect.innerHTML =
                            '<option value="">Select a barber</option>' +
                            list
                                .map(function (member) {
                                    return '<option value="' + member.id + '">' + escapeHtml(member.name) + '</option>';
                                })
                                .join('');
                    })
                    .catch(function () {
                    });
            });

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearFormErrors(form);

                const values = formValues(form);
                const submit = $('[data-save-booking]', modal);
                let valid = true;

                if (!values.customer_name) {
                    setFormError(form, 'customer_name', 'Tell us who the client is.');
                    valid = false;
                }
                if (!values.customer_contact) {
                    setFormError(form, 'customer_contact', 'A contact number is required.');
                    valid = false;
                }
                if (!values.customer_email) {
                    setFormError(form, 'customer_email', 'An email address is required.');
                    valid = false;
                }
                if (!values.service_id) {
                    setFormError(form, 'service_id', 'Choose the service being performed.');
                    valid = false;
                }
                if (!values.staff_id) {
                    setFormError(form, 'staff_id', 'Assign a barber to this chair.');
                    valid = false;
                }
                if (!values.booking_date) {
                    setFormError(form, 'booking_date', 'Choose a date.');
                    valid = false;
                }
                if (!values.booking_time) {
                    setFormError(form, 'booking_time', 'Choose a time.');
                    valid = false;
                }

                if (!valid) {
                    return;
                }

                setBusy(submit, true);

                try {
                    await api('/api/bookings', { method: 'POST', body: JSON.stringify(values) });
                    toast('Appointment created', 'It has been added to the ledger.', 'success');
                    modal.close();
                    load();
                } catch (error) {
                    applyServerErrors(form, error.fields);
                    toast('Could not create appointment', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }

        async function load() {
            try {
                const bookings = await api('/api/bookings');
                state.rows = Array.isArray(bookings) ? bookings : bookings.data || [];
                applyFilters();
            } catch (error) {
                tableBody.innerHTML =
                    '<tr><td colspan="8"><div class="empty">' +
                    '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-triangle-alert"></use></svg></span>' +
                    '<h3 class="empty__title">The ledger could not be reached</h3>' +
                    '<p class="empty__text">' + escapeHtml(error.message) + '</p>' +
                    '<div class="empty__action"><button type="button" class="btn btn--ghost" data-retry>Try again</button></div>' +
                    '</div></td></tr>';
                const retry = $('[data-retry]');
                if (retry) {
                    retry.addEventListener('click', load);
                }
            }
        }

        async function loadLookups() {
            try {
                const [services, staff] = await Promise.all([
                    api('/api/services').catch(function () {
                        return [];
                    }),
                    api('/api/staff').catch(function () {
                        return [];
                    })
                ]);
                state.services = Array.isArray(services) ? services : [];
                state.staff = Array.isArray(staff) ? staff : [];

                if (staffFilter) {
                    const options = state.staff
                        .map(function (member) {
                            const name = member.user ? member.user.name : 'Unknown';
                            return '<option value="' + member.id + '">' + escapeHtml(name) + '</option>';
                        })
                        .join('');
                    staffFilter.insertAdjacentHTML('beforeend', options);
                }
            } catch (error) {
            }
        }

        if (searchInput) {
            searchInput.addEventListener(
                'input',
                debounce(function () {
                    searchInput.closest('.search').classList.toggle('has-value', !!searchInput.value);
                    applyFilters();
                }, 180)
            );
        }

        $$('.search__clear').forEach(function (button) {
            button.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.closest('.search').classList.remove('has-value');
                    applyFilters();
                    searchInput.focus();
                }
            });
        });

        [statusFilter, staffFilter, dateFrom, dateTo].forEach(function (field) {
            if (field) {
                field.addEventListener('change', applyFilters);
            }
        });

        if (clearButton) {
            clearButton.addEventListener('click', clearFilters);
        }

        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', function () {
                state.perPage = parseInt(pageSizeSelect.value, 10) || 12;
                state.page = 1;
                renderRows();
                renderPager();
            });
        }

        if (pagerControls) {
            pagerControls.addEventListener('click', function (event) {
                const button = event.target.closest('[data-page]');
                if (!button || button.disabled) {
                    return;
                }
                state.page = parseInt(button.getAttribute('data-page'), 10) || 1;
                renderRows();
                renderPager();
            });
        }

        document.addEventListener('click', function (event) {
            const createBtn = event.target.closest('[data-new-booking]');
            if (createBtn) {
                event.preventDefault();
                openCreator();
                return;
            }

            const view = event.target.closest('[data-view-booking]');
            if (view) {
                event.preventDefault();
                openDetail(view.getAttribute('data-view-booking'));
                return;
            }

            const edit = event.target.closest('[data-edit-booking]');
            if (edit) {
                event.preventDefault();
                openEditor(edit.getAttribute('data-edit-booking'));
                return;
            }

            const fromDetail = event.target.closest('[data-edit-from-detail]');
            if (fromDetail) {
                event.preventDefault();
                const id = fromDetail.getAttribute('data-edit-from-detail');
                const modal = fromDetail.closest('.modal');
                if (modal && modal.close) {
                    modal.close();
                }
                setTimeout(function () {
                    openEditor(id);
                }, 120);
                return;
            }

            const confirmBtn = event.target.closest('[data-confirm-booking]');
            if (confirmBtn) {
                event.preventDefault();
                confirmBooking(confirmBtn.getAttribute('data-confirm-booking'), load);
                return;
            }

            const cancelBtn = event.target.closest('[data-cancel-booking]');
            if (cancelBtn) {
                event.preventDefault();
                cancelBooking(cancelBtn.getAttribute('data-cancel-booking'), load);
                return;
            }

            const deleteBtn = event.target.closest('[data-delete-booking]');
            if (deleteBtn) {
                event.preventDefault();
                deleteBooking(deleteBtn.getAttribute('data-delete-booking'), load);
            }
        });

        loadLookups();
        load();
    }

    function initServices() {
        const tableBody = $('#servicesRows');
        const listPage = tableBody ? 'list' : null;

        if (listPage) {
            const state = { rows: [], filtered: [] };
            const searchInput = $('#serviceSearch');
            const typeFilter = $('#serviceTypeFilter');
            const countLabel = $('#serviceCount');

            function render() {
                if (!state.filtered.length) {
                    tableBody.innerHTML =
                        '<tr><td colspan="7"><div class="empty">' +
                        '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-scissors"></use></svg></span>' +
                        '<h3 class="empty__title">' +
                        (state.rows.length ? 'Nothing matches that search' : 'No services on the books') +
                        '</h3>' +
                        '<p class="empty__text">' +
                        (state.rows.length
                            ? 'Try a different name, or clear the search.'
                            : 'Add the treatments the house performs so clients can book them.') +
                        '</p>' +
                        (state.rows.length
                            ? ''
                            : '<div class="empty__action"><a class="btn btn--primary" href="/admin-panel/services/create">' +
                              '<svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg> Add a service</a></div>') +
                        '</div></td></tr>';
                    return;
                }

                tableBody.innerHTML = state.filtered
                    .map(function (service) {
                        const staffCount = Array.isArray(service.staff) ? service.staff.length : 0;
                        const bookingsCount = service.bookings_count !== undefined ? service.bookings_count : null;

                        return (
                            '<tr>' +
                            '<td class="row-mark cell-ref">#' + escapeHtml(service.id) + '</td>' +
                            '<td><span class="cell-strong">' + escapeHtml(service.service_name) + '</span>' +
                            '<span class="cell-sub">' + escapeHtml((service.description || 'No description').slice(0, 90)) + '</span></td>' +
                            '<td><span class="seal seal--plain">' + escapeHtml(service.service_type) + '</span></td>' +
                            '<td class="cell-num cell-strong">' + escapeHtml(formatMoney(service.price)) + '</td>' +
                            '<td class="cell-num">' + staffCount + '</td>' +
                            '<td class="cell-num">' + (bookingsCount === null ? '—' : bookingsCount) + '</td>' +
                            '<td><div class="ledger__actions">' +
                            '<a class="icon-btn" href="/admin-panel/services/' + service.id + '/edit" title="Edit service" aria-label="Edit ' + escapeHtml(service.service_name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg></a>' +
                            '<button type="button" class="icon-btn icon-btn--danger" data-delete-service="' + service.id + '" title="Delete service" aria-label="Delete ' + escapeHtml(service.service_name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-trash-2"></use></svg></button>' +
                            '</div></td>' +
                            '</tr>'
                        );
                    })
                    .join('');
            }

            function apply() {
                const term = (searchInput ? searchInput.value : '').trim().toLowerCase();
                const type = typeFilter ? typeFilter.value : '';

                state.filtered = state.rows.filter(function (service) {
                    if (term && String(service.service_name || '').toLowerCase().indexOf(term) === -1) {
                        return false;
                    }
                    if (type && service.service_type !== type) {
                        return false;
                    }
                    return true;
                });

                if (countLabel) {
                    countLabel.textContent =
                        state.filtered.length === state.rows.length
                            ? state.rows.length + ' service' + (state.rows.length === 1 ? '' : 's')
                            : state.filtered.length + ' of ' + state.rows.length + ' services';
                }

                render();
            }

            async function load() {
                try {
                    const services = await api('/api/services');
                    state.rows = Array.isArray(services) ? services : [];
                    apply();
                } catch (error) {
                    tableBody.innerHTML =
                        '<tr><td colspan="7"><div class="empty"><h3 class="empty__title">Could not load services</h3>' +
                        '<p class="empty__text">' + escapeHtml(error.message) + '</p></div></td></tr>';
                }
            }

            if (searchInput) {
                searchInput.addEventListener(
                    'input',
                    debounce(function () {
                        searchInput.closest('.search').classList.toggle('has-value', !!searchInput.value);
                        apply();
                    }, 180)
                );
            }

            $$('.search__clear').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.closest('.search').classList.remove('has-value');
                        apply();
                    }
                });
            });

            if (typeFilter) {
                typeFilter.addEventListener('change', apply);
            }

            document.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-delete-service]');
                if (!button) {
                    return;
                }
                event.preventDefault();

                const id = button.getAttribute('data-delete-service');
                const accepted = await confirmDialog({
                    title: 'Delete this service?',
                    subtitle: 'It will disappear from the public booking page.',
                    message:
                        'Existing appointments are not deleted, but this service can no longer be booked. Consider editing it instead.',
                    confirmLabel: 'Delete service',
                    cancelLabel: 'Keep it',
                    tone: 'danger'
                });

                if (!accepted) {
                    return;
                }

                try {
                    await api('/api/services/' + id, { method: 'DELETE' });
                    toast('Service deleted', 'The service has been removed from the house.', 'success');
                    load();
                } catch (error) {
                    toast('Could not delete', error.message, 'error');
                }
            });

            load();
        }

        const createForm = $('#createServiceForm');
        const editForm = $('#editServiceForm');

        function readServiceForm(form) {
            return {
                service_name: form.querySelector('[name="service_name"]').value.trim(),
                description: form.querySelector('[name="description"]').value.trim(),
                price: form.querySelector('[name="price"]').value,
                service_type: form.querySelector('[name="service_type"]').value
            };
        }

        function validateServiceForm(form, values) {
            clearFormErrors(form);
            let valid = true;

            if (!values.service_name) {
                setFormError(form, 'service_name', 'Give the service a name.');
                valid = false;
            }
            if (!values.service_type) {
                setFormError(form, 'service_type', 'Choose who this service is for.');
                valid = false;
            }
            if (!values.price || isNaN(Number(values.price)) || Number(values.price) < 0) {
                setFormError(form, 'price', 'Enter a price of zero or more.');
                valid = false;
            }

            return valid;
        }

        if (createForm) {
            createForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const values = readServiceForm(createForm);
                if (!validateServiceForm(createForm, values)) {
                    return;
                }

                const submit = $('[type="submit"]', createForm);
                setBusy(submit, true);

                try {
                    await api('/api/services', { method: 'POST', body: JSON.stringify(values) });
                    toast('Service created', 'It is now bookable on the public site.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/services';
                    }, 500);
                } catch (error) {
                    applyServerErrors(createForm, error.fields);
                    toast('Could not create service', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }

        if (editForm) {
            const serviceId = editForm.getAttribute('data-service-id');

            api('/api/services/' + serviceId)
                .then(function (service) {
                    editForm.querySelector('[name="service_name"]').value = service.service_name || '';
                    editForm.querySelector('[name="description"]').value = service.description || '';
                    editForm.querySelector('[name="price"]').value = service.price;
                    editForm.querySelector('[name="service_type"]').value = service.service_type;
                })
                .catch(function (error) {
                    toast('Could not load the service', error.message, 'error');
                });

            editForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const values = readServiceForm(editForm);
                if (!validateServiceForm(editForm, values)) {
                    return;
                }

                const submit = $('[type="submit"]', editForm);
                setBusy(submit, true);

                try {
                    await api('/api/services/' + serviceId, { method: 'PUT', body: JSON.stringify(values) });
                    toast('Service updated', 'The changes have been saved.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/services';
                    }, 500);
                } catch (error) {
                    applyServerErrors(editForm, error.fields);
                    toast('Could not save', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }
    }

    function initAccounts() {
        const tableBody = $('#usersRows');

        if (tableBody) {
            const state = { rows: [], filtered: [] };
            const searchInput = $('#userSearch');
            const roleFilter = $('#userRoleFilter');
            const countLabel = $('#userCount');

            function render() {
                if (!state.filtered.length) {
                    tableBody.innerHTML =
                        '<tr><td colspan="6"><div class="empty">' +
                        '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-id-card"></use></svg></span>' +
                        '<h3 class="empty__title">' +
                        (state.rows.length ? 'No accounts match' : 'No accounts yet') +
                        '</h3>' +
                        '<p class="empty__text">' +
                        (state.rows.length
                            ? 'Try another name, email or role.'
                            : 'Accounts let barbers and proprietors sign in to the management office.') +
                        '</p>' +
                        (state.rows.length
                            ? ''
                            : '<div class="empty__action"><a class="btn btn--primary" href="/admin-panel/users/create">' +
                              '<svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg> Create an account</a></div>') +
                        '</div></td></tr>';
                    return;
                }

                tableBody.innerHTML = state.filtered
                    .map(function (user) {
                        const roleClass = user.type === 'admin' ? 'seal--admin' : 'seal--staff';
                        const joined = user.created_at ? formatDate(user.created_at) : '—';

                        return (
                            '<tr>' +
                            '<td class="row-mark cell-ref">#' + escapeHtml(user.id) + '</td>' +
                            '<td><div class="person">' +
                            '<span class="initials">' + escapeHtml(initials(user.name)) + '</span>' +
                            '<span><span class="person__name">' + escapeHtml(user.name) + '</span>' +
                            '<span class="person__meta">' + escapeHtml(user.email) + '</span></span>' +
                            '</div></td>' +
                            '<td>' + escapeHtml(user.phone || '—') + '</td>' +
                            '<td><span class="seal ' + roleClass + '">' + escapeHtml(user.type) + '</span></td>' +
                            '<td class="cell-num cell-ref">' + escapeHtml(joined) + '</td>' +
                            '<td><div class="ledger__actions">' +
                            '<a class="icon-btn" href="/admin-panel/users/' + user.id + '/edit" title="Edit account" aria-label="Edit ' + escapeHtml(user.name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg></a>' +
                            '<button type="button" class="icon-btn icon-btn--danger" data-delete-user="' + user.id + '" title="Delete account" aria-label="Delete ' + escapeHtml(user.name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-trash-2"></use></svg></button>' +
                            '</div></td>' +
                            '</tr>'
                        );
                    })
                    .join('');
            }

            function apply() {
                const term = (searchInput ? searchInput.value : '').trim().toLowerCase();
                const role = roleFilter ? roleFilter.value : '';

                state.filtered = state.rows.filter(function (user) {
                    const haystack = [user.name, user.email, user.phone].join(' ').toLowerCase();
                    if (term && haystack.indexOf(term) === -1) {
                        return false;
                    }
                    if (role && user.type !== role) {
                        return false;
                    }
                    return true;
                });

                if (countLabel) {
                    countLabel.textContent =
                        state.filtered.length === state.rows.length
                            ? state.rows.length + ' account' + (state.rows.length === 1 ? '' : 's')
                            : state.filtered.length + ' of ' + state.rows.length + ' accounts';
                }

                render();
            }

            async function load() {
                try {
                    const users = await api('/api/users');
                    state.rows = Array.isArray(users) ? users : [];
                    apply();
                } catch (error) {
                    tableBody.innerHTML =
                        '<tr><td colspan="6"><div class="empty"><h3 class="empty__title">Could not load accounts</h3>' +
                        '<p class="empty__text">' + escapeHtml(error.message) + '</p></div></td></tr>';
                }
            }

            if (searchInput) {
                searchInput.addEventListener(
                    'input',
                    debounce(function () {
                        searchInput.closest('.search').classList.toggle('has-value', !!searchInput.value);
                        apply();
                    }, 180)
                );
            }

            $$('.search__clear').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.closest('.search').classList.remove('has-value');
                        apply();
                    }
                });
            });

            if (roleFilter) {
                roleFilter.addEventListener('change', apply);
            }

            document.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-delete-user]');
                if (!button) {
                    return;
                }
                event.preventDefault();

                const id = button.getAttribute('data-delete-user');
                const accepted = await confirmDialog({
                    title: 'Delete this account?',
                    subtitle: 'The sign-in will stop working immediately.',
                    message:
                        'Deleting an account removes its access to the management office. Any barber record tied to it goes with it.',
                    confirmLabel: 'Delete account',
                    cancelLabel: 'Keep it',
                    tone: 'danger'
                });

                if (!accepted) {
                    return;
                }

                try {
                    await api('/api/users/' + id, { method: 'DELETE' });
                    toast('Account deleted', 'The sign-in has been removed.', 'success');
                    load();
                } catch (error) {
                    toast('Could not delete', error.message, 'error');
                }
            });

            load();
        }

        const createForm = $('#createUserForm');
        const editForm = $('#editUserForm');

        function readUserForm(form) {
            const values = formValues(form);
            delete values.password_confirmation;
            if (!values.password) {
                delete values.password;
            }
            return values;
        }

        function validateUserForm(form, values, requirePassword) {
            clearFormErrors(form);
            let valid = true;

            if (!values.name) {
                setFormError(form, 'name', 'A name is required.');
                valid = false;
            }
            if (!values.email) {
                setFormError(form, 'email', 'An email address is required.');
                valid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(values.email)) {
                setFormError(form, 'email', 'That does not look like an email address.');
                valid = false;
            }
            if (!values.type) {
                setFormError(form, 'type', 'Choose a role for this account.');
                valid = false;
            }

            const password = form.querySelector('[name="password"]');
            const confirmation = form.querySelector('[name="password_confirmation"]');

            if (requirePassword) {
                if (!password || password.value.length < 8) {
                    setFormError(form, 'password', 'Use at least 8 characters.');
                    valid = false;
                }
            }

            if (password && confirmation && password.value !== confirmation.value) {
                setFormError(form, 'password_confirmation', 'The two passwords do not match.');
                valid = false;
            }

            return valid;
        }

        if (createForm) {
            createForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const values = readUserForm(createForm);
                if (!validateUserForm(createForm, values, true)) {
                    return;
                }

                const submit = $('[type="submit"]', createForm);
                setBusy(submit, true);

                try {
                    await api('/api/users', {
                        method: 'POST',
                        body: JSON.stringify(
                            Object.assign({}, values, {
                                password_confirmation: createForm.querySelector('[name="password_confirmation"]').value
                            })
                        )
                    });
                    toast('Account created', 'The sign-in is ready to use.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/users';
                    }, 500);
                } catch (error) {
                    applyServerErrors(createForm, error.fields);
                    toast('Could not create account', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }

        if (editForm) {
            const userId = editForm.getAttribute('data-user-id');

            api('/api/users/' + userId)
                .then(function (user) {
                    editForm.querySelector('[name="name"]').value = user.name || '';
                    editForm.querySelector('[name="email"]').value = user.email || '';
                    editForm.querySelector('[name="phone"]').value = user.phone || '';
                    editForm.querySelector('[name="type"]').value = user.type || '';
                })
                .catch(function (error) {
                    toast('Could not load the account', error.message, 'error');
                });

            editForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const values = readUserForm(editForm);
                if (!validateUserForm(editForm, values, false)) {
                    return;
                }

                const submit = $('[type="submit"]', editForm);
                setBusy(submit, true);

                try {
                    await api('/api/users/' + userId, {
                        method: 'PUT',
                        body: JSON.stringify(
                            Object.assign({}, values, {
                                password_confirmation: editForm.querySelector('[name="password_confirmation"]').value
                            })
                        )
                    });
                    toast('Account updated', 'The changes have been saved.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/users';
                    }, 500);
                } catch (error) {
                    applyServerErrors(editForm, error.fields);
                    toast('Could not save', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }
    }


    function initStaff() {
        const tableBody = $('#staffRows');

        if (tableBody) {
            const state = { rows: [], filtered: [] };
            const searchInput = $('#staffSearch');
            const countLabel = $('#staffCount');

            function render() {
                if (!state.filtered.length) {
                    tableBody.innerHTML =
                        '<tr><td colspan="6"><div class="empty">' +
                        '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-contact"></use></svg></span>' +
                        '<h3 class="empty__title">' +
                        (state.rows.length ? 'No barber matches' : 'No barbers on the floor') +
                        '</h3>' +
                        '<p class="empty__text">' +
                        (state.rows.length
                            ? 'Try another name or email.'
                            : 'Create an account for a barber, then add their barber record so they can take bookings.') +
                        '</p>' +
                        (state.rows.length
                            ? ''
                            : '<div class="empty__action"><a class="btn btn--primary" href="/admin-panel/staff/create">' +
                              '<svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg> Add a barber</a></div>') +
                        '</div></td></tr>';
                    return;
                }

                tableBody.innerHTML = state.filtered
                    .map(function (member) {
                        const user = member.user || {};
                        const services = Array.isArray(member.services) ? member.services : [];
                        const bookingsCount = member.bookings_count !== undefined ? member.bookings_count : null;

                        return (
                            '<tr>' +
                            '<td class="row-mark cell-ref">#' + escapeHtml(member.id) + '</td>' +
                            '<td><div class="person">' +
                            '<span class="initials">' + escapeHtml(initials(user.name)) + '</span>' +
                            '<span><span class="person__name">' + escapeHtml(user.name || 'Unnamed') + '</span>' +
                            '<span class="person__meta">' + escapeHtml(user.email || '—') + '</span></span>' +
                            '</div></td>' +
                            '<td>' + escapeHtml(user.phone || '—') + '</td>' +
                            '<td>' + (services.length
                                ? '<span class="tag-list">' + services.slice(0, 2).map(function (service) {
                                    return '<span class="tag">' + escapeHtml(service.service_name) + '</span>';
                                }).join('') + (services.length > 2 ? '<span class="tag">+' + (services.length - 2) + '</span>' : '') + '</span>'
                                : '<span class="text-smoke">None assigned</span>') + '</td>' +
                            '<td class="cell-num">' + (bookingsCount === null ? '—' : bookingsCount) + '</td>' +
                            '<td><div class="ledger__actions">' +
                            '<a class="icon-btn" href="/admin-panel/staff/' + member.id + '/view" title="View barber" aria-label="View ' + escapeHtml(user.name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-eye"></use></svg></a>' +
                            '<a class="icon-btn" href="/admin-panel/staff/' + member.id + '/edit" title="Edit barber" aria-label="Edit ' + escapeHtml(user.name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg></a>' +
                            '<button type="button" class="icon-btn icon-btn--danger" data-delete-staff="' + member.id + '" title="Remove barber" aria-label="Remove ' + escapeHtml(user.name) + '">' +
                            '<svg class="icon" aria-hidden="true"><use href="#i-trash-2"></use></svg></button>' +
                            '</div></td>' +
                            '</tr>'
                        );
                    })
                    .join('');
            }

            function apply() {
                const term = (searchInput ? searchInput.value : '').trim().toLowerCase();

                state.filtered = state.rows.filter(function (member) {
                    const user = member.user || {};
                    const haystack = [user.name, user.email, user.phone].join(' ').toLowerCase();
                    return !term || haystack.indexOf(term) !== -1;
                });

                if (countLabel) {
                    countLabel.textContent =
                        state.filtered.length === state.rows.length
                            ? state.rows.length + ' barber' + (state.rows.length === 1 ? '' : 's')
                            : state.filtered.length + ' of ' + state.rows.length + ' barbers';
                }

                render();
            }

            async function load() {
                try {
                    const staff = await api('/api/staff');
                    state.rows = Array.isArray(staff) ? staff : [];
                    apply();
                } catch (error) {
                    tableBody.innerHTML =
                        '<tr><td colspan="6"><div class="empty"><h3 class="empty__title">Could not load barbers</h3>' +
                        '<p class="empty__text">' + escapeHtml(error.message) + '</p></div></td></tr>';
                }
            }

            if (searchInput) {
                searchInput.addEventListener(
                    'input',
                    debounce(function () {
                        searchInput.closest('.search').classList.toggle('has-value', !!searchInput.value);
                        apply();
                    }, 180)
                );
            }

            $$('.search__clear').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.closest('.search').classList.remove('has-value');
                        apply();
                    }
                });
            });

            document.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-delete-staff]');
                if (!button) {
                    return;
                }
                event.preventDefault();

                const id = button.getAttribute('data-delete-staff');
                const accepted = await confirmDialog({
                    title: 'Remove this barber?',
                    subtitle: 'The account itself is kept.',
                    message:
                        'Removing a barber deletes their barber record and the services they were assigned. Their sign-in account stays in place.',
                    confirmLabel: 'Remove barber',
                    cancelLabel: 'Keep it',
                    tone: 'danger'
                });

                if (!accepted) {
                    return;
                }

                try {
                    await api('/api/staff/' + id, { method: 'DELETE' });
                    toast('Barber removed', 'They can no longer take bookings.', 'success');
                    load();
                } catch (error) {
                    toast('Could not remove', error.message, 'error');
                }
            });

            load();
        }

        const detail = $('#staffDetail');
        if (detail) {
            const staffId = detail.getAttribute('data-staff-id');

            api('/api/staff/' + staffId)
                .then(function (member) {
                    const user = member.user || {};
                    const services = Array.isArray(member.services) ? member.services : [];
                    const bookingsCount = member.bookings_count !== undefined ? member.bookings_count : null;

                    detail.innerHTML =
                        '<div class="staff-card__head">' +
                        (member.photo
                            ? '<img class="avatar avatar--lg" src="/storage/' + escapeHtml(member.photo) + '" alt="">'
                            : '<span class="avatar avatar--lg">' + escapeHtml(initials(user.name)) + '</span>') +
                        '<div>' +
                        '<h2 class="staff-card__name">' + escapeHtml(user.name || 'Unnamed') + '</h2>' +
                        '<p class="staff-card__role">Barber · ' + escapeHtml(user.email || 'No email') + '</p>' +
                        '</div></div>' +
                        (member.bio ? '<p class="staff-card__bio">' + escapeHtml(member.bio) + '</p>' : '') +
                        '<div class="grid-2--even grid-2" style="margin-top:26px">' +
                        '<section class="panel"><div class="panel__head"><h3 class="panel__title">Contact</h3></div>' +
                        '<div class="panel__body"><div class="detail">' +
                        '<div class="detail__row"><span class="detail__key">Email</span><span class="detail__val">' + escapeHtml(user.email || '—') + '</span></div>' +
                        '<div class="detail__row"><span class="detail__key">Contact</span><span class="detail__val">' + escapeHtml(user.phone || '—') + '</span></div>' +
                        '<div class="detail__row"><span class="detail__key">Bookings</span><span class="detail__val">' + (bookingsCount === null ? '—' : bookingsCount) + '</span></div>' +
                        '</div></div></section>' +
                        '<section class="panel"><div class="panel__head"><h3 class="panel__title">Services Performed</h3></div>' +
                        '<div class="panel__body">' +
                        (services.length
                            ? '<div class="tag-list">' + services.map(function (service) {
                                return '<span class="tag">' + escapeHtml(service.service_name) + ' <small>' + escapeHtml(service.service_type) + '</small></span>';
                            }).join('') + '</div>'
                            : '<p class="panel__sub">No services assigned yet.</p>') +
                        '</div></section>' +
                        '</div>';

                    const photo = $('.staff-photo', detail);
                    if (photo && member.photo) {
                        photo.src = '/storage/' + member.photo;
                    }
                })
                .catch(function (error) {
                    detail.innerHTML =
                        '<div class="empty">' +
                        '<span class="empty__mark"><svg class="icon" aria-hidden="true"><use href="#i-triangle-alert"></use></svg></span>' +
                        '<h3 class="empty__title">Barber not found</h3>' +
                        '<p class="empty__text">' + escapeHtml(error.message) + '</p>' +
                        '</div>';
                });
        }

        /* Create / edit forms ------------------------------------------------ */
        const createForm = $('#createStaffForm');
        const editForm = $('#editStaffForm');

        function readStaffForm(form) {
            const data = new FormData();
            data.append('bio', form.querySelector('[name="bio"]').value);

            const userSelect = form.querySelector('[name="user_id"]');
            if (userSelect && userSelect.value) {
                data.append('user_id', userSelect.value);
            }

            $$('input[name="service_id[]"]:checked', form).forEach(function (box) {
                data.append('service_id[]', box.value);
            });

            const photo = form.querySelector('[name="photo"]');
            if (photo && photo.files && photo.files[0]) {
                data.append('photo', photo.files[0]);
            }

            return data;
        }

        function validateStaffForm(form) {
            clearFormErrors(form);
            let valid = true;

            const userSelect = form.querySelector('[name="user_id"]');
            if (!userSelect || !userSelect.value) {
                setFormError(form, 'user_id', 'Choose which account this barber signs in with.');
                valid = false;
            }

            if (!$$('input[name="service_id[]"]:checked', form).length) {
                setFormError(form, 'service_id', 'Assign at least one service.');
                valid = false;
            }

            return valid;
        }

        if (createForm) {
            createForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                if (!validateStaffForm(createForm)) {
                    return;
                }

                const submit = $('[type="submit"]', createForm);
                setBusy(submit, true);

                try {
                    await api('/api/staff', { method: 'POST', body: readStaffForm(createForm) });
                    toast('Barber added', 'They can now take appointments.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/staff';
                    }, 500);
                } catch (error) {
                    applyServerErrors(createForm, error.fields);
                    toast('Could not add barber', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }

        if (editForm) {
            const staffId = editForm.getAttribute('data-staff-id');

            api('/api/staff/' + staffId)
                .then(function (member) {
                    const userSelect = editForm.querySelector('[name="user_id"]');
                    if (userSelect && member.user) {
                        userSelect.value = member.user.id;
                    }
                    editForm.querySelector('[name="bio"]').value = member.bio || '';

                    const assigned = (member.services || []).map(function (service) {
                        return String(service.id);
                    });
                    $$('input[name="service_id[]"]', editForm).forEach(function (box) {
                        box.checked = assigned.indexOf(box.value) !== -1;
                    });
                })
                .catch(function (error) {
                    toast('Could not load the barber', error.message, 'error');
                });

            editForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                if (!validateStaffForm(editForm)) {
                    return;
                }

                const submit = $('[type="submit"]', editForm);
                setBusy(submit, true);

                try {
                    await api('/api/staff/' + staffId, { method: 'POST', body: readStaffForm(editForm) });
                    toast('Barber updated', 'The changes have been saved.', 'success');
                    setTimeout(function () {
                        window.location.href = '/admin-panel/staff';
                    }, 500);
                } catch (error) {
                    applyServerErrors(editForm, error.fields);
                    toast('Could not save', error.message, 'error');
                } finally {
                    setBusy(submit, false);
                }
            });
        }
    }


    function initTestimonials() {
        document.addEventListener('click', async function (event) {
            const button = event.target.closest('[data-delete-testimonial]');
            if (!button) {
                return;
            }
            event.preventDefault();

            const id = button.getAttribute('data-delete-testimonial');

            const accepted = await confirmDialog({
                title: 'Remove this review?',
                subtitle: 'It disappears from the public site.',
                message:
                    'Removing a review withdraws it from the testimonials on the public site. This cannot be undone.',
                confirmLabel: 'Remove review',
                cancelLabel: 'Keep it',
                tone: 'danger'
            });

            if (!accepted) {
                return;
            }

            try {
                await api('/api/testimonials/' + id, { method: 'DELETE' });
                toast('Review removed', 'It has been withdrawn from the public site.', 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 600);
            } catch (error) {
                toast('Could not remove', error.message, 'error');
            }
        });
    }

    function initSettings() {
        const form = $('#settingsProfileForm');
        if (!form) {
            return;
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearFormErrors(form);

            const values = formValues(form);
            const password = form.querySelector('[name="password"]');
            const confirmation = form.querySelector('[name="password_confirmation"]');
            let valid = true;

            if (!values.name) {
                setFormError(form, 'name', 'A name is required.');
                valid = false;
            }
            if (!values.email) {
                setFormError(form, 'email', 'An email address is required.');
                valid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(values.email)) {
                setFormError(form, 'email', 'That does not look like an email address.');
                valid = false;
            }

            if (password && password.value) {
                if (password.value.length < 8) {
                    setFormError(form, 'password', 'Use at least 8 characters.');
                    valid = false;
                }
                if (!confirmation || confirmation.value !== password.value) {
                    setFormError(form, 'password_confirmation', 'The two passwords do not match.');
                    valid = false;
                }
                values.password = password.value;
                values.password_confirmation = confirmation ? confirmation.value : '';
            }

            if (!valid) {
                return;
            }

            const submit = $('[type="submit"]', form);
            setBusy(submit, true);

            try {
                await api('/api/users/' + form.getAttribute('data-user-id'), {
                    method: 'PUT',
                    body: JSON.stringify(values)
                });
                toast('Settings saved', 'Your account has been updated.', 'success');
                if (values.password) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 900);
                }
            } catch (error) {
                applyServerErrors(form, error.fields);
                toast('Could not save settings', error.message, 'error');
            } finally {
                setBusy(submit, false);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initShell();
        initBookingShortcuts();
        initAppointments();
        initServices();
        initAccounts();
        initStaff();
        initTestimonials();
        initSettings();
    });
})();
