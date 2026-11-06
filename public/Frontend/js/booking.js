(function () {
    'use strict';

    var $ = function (selector, scope) {
        return (scope || document).querySelector(selector);
    };

    var $$ = function (selector, scope) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    };

    var on = function (element, type, handler, options) {
        if (element) {
            element.addEventListener(type, handler, options);
        }
    };

    var escapeHtml = function (value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    var pad = function (value) {
        return value < 10 ? '0' + value : String(value);
    };

    var MONTHS = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    var WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var WEEKDAYS_SHORT = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

    var HOURS = {
        0: null,
        1: { open: 9, close: 20 },
        2: { open: 9, close: 20 },
        3: { open: 9, close: 20 },
        4: { open: 9, close: 20 },
        5: { open: 9, close: 20 },
        6: { open: 9, close: 18 }
    };

    var SERVICE_DURATION = {
        'classic haircut': 45,
        'beard trim': 30,
        "men's facial": 60,
        'hair coloring': 120,
        'blowout styling': 45,
        manicure: 45,
        'classic pedicure': 60,
        'makeup session': 90
    };

    var DEFAULT_DURATION = 60;

    var BOOKING_WINDOW_DAYS = 90;

    var state = {
        serviceId: '',
        serviceName: '',
        servicePrice: 0,
        serviceDuration: DEFAULT_DURATION,
        staffId: '',
        staffName: '',
        date: '',
        time: '',
        notes: '',
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear()
    };

    var els = {};

    var durationFor = function (name) {
        var key = String(name || '')
            .trim()
            .toLowerCase();
        return SERVICE_DURATION[key] || DEFAULT_DURATION;
    };

    var toDateKey = function (date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
    };

    var parseDateKey = function (key) {
        var parts = String(key).split('-');
        return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    };

    var startOfToday = function () {
        var now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    };

    var addDays = function (date, days) {
        var copy = new Date(date.getTime());
        copy.setDate(copy.getDate() + days);
        return copy;
    };

    var formatHour = function (hour) {
        var suffix = hour >= 12 ? 'PM' : 'AM';
        var value = hour % 12;

        if (value === 0) {
            value = 12;
        }

        return pad(value) + ':00 ' + suffix;
    };

    var formatDateLong = function (key) {
        var date = parseDateKey(key);
        return WEEKDAYS[date.getDay()] + ' ' + date.getDate() + ' ' + MONTHS[date.getMonth()] + ' ' + date.getFullYear();
    };

    var formatMoney = function (value) {
        var num = Number(value) || 0;
        return 'PKR ' + (Number.isInteger(num) ? String(num) : num.toFixed(2));
    };

    var isSunday = function (date) {
        return date.getDay() === 0;
    };

    var lastBookableDate = function () {
        return addDays(startOfToday(), BOOKING_WINDOW_DAYS);
    };


    var say = function (text, isError) {
        if (!els.message) {
            return;
        }

        els.message.innerHTML = text
            ? '<div class="dossier__message' +
              (isError ? ' dossier__message--error' : '') +
              '">' +
              escapeHtml(text) +
              '</div>'
            : '';
    };

    var setNote = function (element, text) {
        if (element) {
            element.textContent = text;
        }
    };


    var loadServices = function () {
        fetch('/api/services', { headers: { Accept: 'application/json' } })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('The price list could not be reached.');
                }
                return response.json();
            })
            .then(function (services) {
                if (!els.serviceSelect) {
                    return;
                }

                els.serviceSelect.innerHTML = '<option value="">Choose a treatment…</option>';

                services.forEach(function (service) {
                    var option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = service.service_name;
                    option.dataset.price = service.price;
                    option.dataset.duration = durationFor(service.service_name);
                    els.serviceSelect.appendChild(option);
                });

                els.serviceSelect.disabled = false;

                var requested = new URLSearchParams(window.location.search).get('service');

                if (requested) {
                    var match = services.filter(function (service) {
                        return String(service.id) === String(requested);
                    })[0];

                    if (match) {
                        els.serviceSelect.value = String(match.id);
                        onServiceChange();
                    }
                }
            })
            .catch(function (error) {
                if (els.serviceSelect) {
                    els.serviceSelect.innerHTML = '<option value="">Price list unavailable</option>';
                    els.serviceSelect.disabled = true;
                }

                say(error.message || 'The price list could not be reached.', true);
            });
    };

    var onServiceChange = function () {
        var option = els.serviceSelect && els.serviceSelect.selectedOptions[0];

        if (!option || !option.value) {
            state.serviceId = '';
            state.serviceName = '';
            state.servicePrice = 0;
            state.serviceDuration = DEFAULT_DURATION;
        } else {
            state.serviceId = option.value;
            state.serviceName = option.textContent;
            state.servicePrice = parseFloat(option.dataset.price) || 0;
            state.serviceDuration = parseInt(option.dataset.duration, 10) || DEFAULT_DURATION;
        }

        state.staffId = '';
        state.staffName = '';

        loadStaff();
        updateSummary();
    };

    var loadStaff = function () {
        if (!els.staffSelect) {
            return;
        }

        els.staffSelect.innerHTML = '<option value="">Choose a treatment first…</option>';
        els.staffSelect.disabled = true;
        setNote(els.staffNote, 'Choose a treatment first');

        if (!state.serviceId) {
            return;
        }

        els.staffSelect.innerHTML = '<option value="">Calling the artisans…</option>';
        setNote(els.staffNote, 'Looking for an artisan');

        fetch('/api/staff-by-service/' + encodeURIComponent(state.serviceId), {
            headers: { Accept: 'application/json' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('The artisans could not be reached.');
                }
                return response.json();
            })
            .then(function (staff) {
                els.staffSelect.innerHTML = '<option value="">Choose an artisan…</option>';

                if (!staff.length) {
                    els.staffSelect.innerHTML = '<option value="">No artisan holds this treatment</option>';
                    els.staffSelect.disabled = true;
                    setNote(els.staffNote, 'None available for this treatment');
                    return;
                }

                staff.forEach(function (member) {
                    var option = document.createElement('option');
                    option.value = member.id;
                    option.textContent = member.name;
                    els.staffSelect.appendChild(option);
                });

                els.staffSelect.disabled = false;
                setNote(els.staffNote, staff.length + (staff.length === 1 ? ' artisan' : ' artisans') + ' available');
            })
            .catch(function (error) {
                els.staffSelect.innerHTML = '<option value="">Artisans unavailable</option>';
                els.staffSelect.disabled = true;
                setNote(els.staffNote, 'Unavailable');
                say(error.message || 'The artisans could not be reached.', true);
            });
    };

    var renderCalendar = function () {
        if (!els.calendarGrid || !els.currentMonth) {
            return;
        }

        els.currentMonth.textContent = MONTHS[state.currentMonth] + ' ' + state.currentYear;
        els.calendarGrid.innerHTML = '';

        WEEKDAYS_SHORT.forEach(function (label) {
            var cell = document.createElement('span');
            cell.className = 'calendar__dow';
            cell.textContent = label;
            els.calendarGrid.appendChild(cell);
        });

        var firstDay = new Date(state.currentYear, state.currentMonth, 1).getDay();
        var daysInMonth = new Date(state.currentYear, state.currentMonth + 1, 0).getDate();
        var today = startOfToday();
        var last = lastBookableDate();
        var index;

        for (index = 0; index < firstDay; index += 1) {
            var blank = document.createElement('span');
            blank.className = 'calendar__day calendar__day--empty';
            els.calendarGrid.appendChild(blank);
        }

        for (index = 1; index <= daysInMonth; index += 1) {
            var date = new Date(state.currentYear, state.currentMonth, index);
            var key = toDateKey(date);
            var button = document.createElement('button');
            var closed = isSunday(date);
            var past = date < today;
            var beyond = date > last;

            button.type = 'button';
            button.className = 'calendar__day';
            button.textContent = String(index);
            button.dataset.date = key;
            button.setAttribute('aria-label', WEEKDAYS[date.getDay()] + ' ' + index + ' ' + MONTHS[date.getMonth()] + ' ' + state.currentYear);

            if (date.getTime() === today.getTime()) {
                button.classList.add('is-today');
            }

            if (state.date === key) {
                button.classList.add('is-selected');
                button.setAttribute('aria-pressed', 'true');
            }

            if (closed || past || beyond) {
                button.disabled = true;
                button.setAttribute(
                    'aria-label',
                    button.getAttribute('aria-label') +
                        (closed ? ' — the house is closed on Sundays' : past ? ' — a past date' : ' — beyond the booking window')
                );
            }

            on(button, 'click', function (event) {
                selectDate(event.currentTarget.dataset.date);
            });

            els.calendarGrid.appendChild(button);
        }

        updateMonthBounds();
    };

    var updateMonthBounds = function () {
        if (!els.prevMonth || !els.nextMonth) {
            return;
        }

        var today = startOfToday();
        var current = new Date(state.currentYear, state.currentMonth, 1);
        var last = lastBookableDate();
        var lastMonth = new Date(state.currentYear, state.currentMonth + 1, 1);

        els.prevMonth.disabled = current <= new Date(today.getFullYear(), today.getMonth(), 1);
        els.nextMonth.disabled = lastMonth > new Date(last.getFullYear(), last.getMonth(), 1);
    };

    var selectDate = function (key) {
        state.date = key;
        state.time = '';
        renderCalendar();
        renderSlots();
        updateSummary();
    };

    var moveMonth = function (delta) {
        var month = state.currentMonth + delta;
        var year = state.currentYear;

        if (month < 0) {
            month = 11;
            year -= 1;
        } else if (month > 11) {
            month = 0;
            year += 1;
        }

        state.currentMonth = month;
        state.currentYear = year;
        renderCalendar();
    };


    var slotIsPast = function (date, hour) {
        var now = new Date();
        var start = new Date(date.getFullYear(), date.getMonth(), date.getDate(), hour, 0, 0);
        return start <= now;
    };

    var renderSlots = function () {
        if (!els.timeSlots) {
            return;
        }

        els.timeSlots.innerHTML = '';

        if (!state.date) {
            setNote(els.slotNote, 'Choose a date to see the hours');

            var placeholder = document.createElement('p');
            placeholder.className = 'field__hint';
            placeholder.textContent = 'The house is open from nine in the morning. Pick a date and the free hours will appear here.';
            els.timeSlots.appendChild(placeholder);
            return;
        }

        var date = parseDateKey(state.date);
        var hours = HOURS[date.getDay()];

        if (!hours) {
            setNote(els.slotNote, 'The house is closed on Sundays');
            var closedNote = document.createElement('p');
            closedNote.className = 'field__hint';
            closedNote.textContent = 'The Garrison does not open on Sundays. Please choose another day.';
            els.timeSlots.appendChild(closedNote);
            return;
        }

        var durationHours = state.serviceDuration / 60;
        var count = 0;

        setNote(
            els.slotNote,
            state.serviceId
                ? 'Each treatment holds the chair for ' + state.serviceDuration + ' minutes'
                : 'Choose a treatment to allow for its full chair time'
        );

        for (var hour = hours.open; hour < hours.close; hour += 1) {
            var fits = hour + durationHours <= hours.close;
            var label = formatHour(hour);
            var button = document.createElement('button');
            var unavailable = !fits || slotIsPast(date, hour);

            button.type = 'button';
            button.className = 'slot';
            button.textContent = label;
            button.dataset.time = label;

            if (state.time === label) {
                button.classList.add('is-selected');
                button.setAttribute('aria-pressed', 'true');
            }

            if (unavailable) {
                button.disabled = true;
                button.setAttribute(
                    'aria-label',
                    label + (fits ? ' — passed' : ' — not enough time before closing')
                );
            } else {
                count += 1;
                on(button, 'click', function (event) {
                    selectTime(event.currentTarget.dataset.time);
                });
            }

            els.timeSlots.appendChild(button);
        }

        if (!count) {
            var empty = document.createElement('p');
            empty.className = 'field__hint';
            empty.textContent = 'No hours remain on this date for that treatment. Please choose another day.';
            els.timeSlots.appendChild(empty);
        }
    };

    var selectTime = function (time) {
        state.time = time;
        renderSlots();
        updateSummary();
    };

    var setValue = function (element, text, isSet) {
        if (!element) {
            return;
        }

        element.textContent = text;
        element.classList.toggle('is-set', !!isSet);
    };

    var updateSummary = function () {
        setValue(els.summaryService, state.serviceName || 'Not chosen', !!state.serviceName);
        setValue(els.summaryStaff, state.staffName || 'Not chosen', !!state.staffName);
        setValue(els.summaryDate, state.date ? formatDateLong(state.date) : 'Not chosen', !!state.date);
        setValue(els.summaryTime, state.time || 'Not chosen', !!state.time);
        setValue(
            els.summaryDuration,
            state.serviceId ? state.serviceDuration + ' minutes' : '—',
            !!state.serviceId
        );

        if (els.totalPrice) {
            els.totalPrice.textContent = formatMoney(state.servicePrice);
        }

        var ready =
            state.serviceId &&
            state.staffId &&
            state.date &&
            state.time &&
            els.userName &&
            els.userName.value.trim() &&
            els.userPhone &&
            els.userPhone.value.trim() &&
            els.userEmail &&
            els.userEmail.value.trim();

        if (els.confirmBtn) {
            els.confirmBtn.disabled = !ready;
        }
    };


    var showConfirmation = function (booking) {
        var reference = booking && booking.id ? String(booking.id).padStart(4, '0') : '—';

        setValue(els.confirmService, state.serviceName, true);
        setValue(els.confirmStaff, state.staffName, true);
        setValue(els.confirmDate, state.date ? formatDateLong(state.date) : '—', true);
        setValue(els.confirmTime, state.time || '—', true);
        setValue(els.confirmTotal, formatMoney(state.servicePrice), true);

        if (els.confirmRef) {
            els.confirmRef.textContent = 'TG-' + reference;
        }

        if (els.confirmText) {
            els.confirmText.textContent =
                'Thank you, ' +
                (els.userName ? els.userName.value.trim() : '') +
                '. The house has your chair reserved for ' +
                (state.time || '') +
                ' on ' +
                (state.date ? formatDateLong(state.date) : '') +
                '.';
        }

        if (els.bookingSection) {
            els.bookingSection.hidden = true;
        }

        if (els.bookingMasthead) {
            els.bookingMasthead.hidden = true;
        }

        if (els.bookingResult) {
            els.bookingResult.hidden = false;
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    var storeBooking = function (payload) {
        return fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify(payload)
        }).then(function (response) {
            return response.json().then(function (body) {
                return { ok: response.ok, status: response.status, body: body };
            });
        });
    };

    var submitBooking = function () {
        var name = els.userName.value.trim();
        var phone = els.userPhone.value.trim();
        var email = els.userEmail.value.trim();
        var notes = els.userNotes ? els.userNotes.value.trim() : '';

        if (!name || !phone || !email || !state.serviceId || !state.staffId || !state.date || !state.time) {
            say('Every line of the dossier must be completed before the book is closed.', true);
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            say('That email address does not look right. Please check it.', true);
            els.userEmail.focus();
            return;
        }

        if (phone.length > 20) {
            say('The telephone number is too long for the book — please use 20 characters or fewer.', true);
            els.userPhone.focus();
            return;
        }

        say('');
        els.confirmBtn.disabled = true;
        els.confirmBtn.textContent = 'Closing the book…';

        storeBooking({
            customer_name: name,
            customer_contact: phone,
            customer_email: email,
            service_id: state.serviceId,
            staff_id: state.staffId,
            booking_date: state.date,
            booking_time: state.time,
            notes: notes,
            status: 'pending'
        })
            .then(function (result) {
                if (!result.ok) {
                    var errors = result.body && result.body.errors;
                    var first = errors ? Object.keys(errors)[0] : null;

                    throw new Error(
                        first && errors[first] && errors[first][0]
                            ? errors[first][0]
                            : result.body && result.body.message
                              ? result.body.message
                              : 'The book would not take the entry. Please telephone the house on 0324 4050830.'
                    );
                }

                showConfirmation(result.body && result.body.booking);
            })
            .catch(function (error) {
                say(error.message || 'Something went wrong. Please try again.', true);
                els.confirmBtn.disabled = false;
                els.confirmBtn.textContent = 'Confirm the Appointment';
            });
    };

    var resetBooking = function () {
        state.serviceId = '';
        state.serviceName = '';
        state.servicePrice = 0;
        state.serviceDuration = DEFAULT_DURATION;
        state.staffId = '';
        state.staffName = '';
        state.date = '';
        state.time = '';
        state.currentMonth = new Date().getMonth();
        state.currentYear = new Date().getFullYear();

        if (els.bookingForm) {
            els.bookingForm.reset();
        }

        if (els.staffSelect) {
            els.staffSelect.innerHTML = '<option value="">Choose a treatment first…</option>';
            els.staffSelect.disabled = true;
        }

        setNote(els.staffNote, 'Choose a treatment first');
        setNote(els.slotNote, 'Choose a date to see the hours');
        say('');

        if (els.bookingResult) {
            els.bookingResult.hidden = true;
        }

        if (els.bookingSection) {
            els.bookingSection.hidden = false;
        }

        renderCalendar();
        renderSlots();
        updateSummary();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };


    var initCalendarKeys = function () {
        on(els.calendarGrid, 'keydown', function (event) {
            var moves = {
                ArrowLeft: -1,
                ArrowRight: 1,
                ArrowUp: -7,
                ArrowDown: 7
            };

            if (!(event.key in moves)) {
                return;
            }

            var current = document.activeElement;

            if (!current || !current.dataset || !current.dataset.date) {
                return;
            }

            event.preventDefault();

            var date = parseDateKey(current.dataset.date);
            date.setDate(date.getDate() + moves[event.key]);

            var today = startOfToday();
            var last = lastBookableDate();

            if (date < today || date > last || isSunday(date)) {
                return;
            }

            var target = els.calendarGrid.querySelector('[data-date="' + toDateKey(date) + '"]');

            if (target) {
                target.focus();
            }
        });
    };


    var init = function () {
        els.serviceSelect = $('#serviceSelect');
        els.staffSelect = $('#staffSelect');
        els.staffNote = $('#staffNote');
        els.calendarGrid = $('#calendarGrid');
        els.currentMonth = $('#currentMonth');
        els.prevMonth = $('#prevMonth');
        els.nextMonth = $('#nextMonth');
        els.timeSlots = $('#timeSlots');
        els.slotNote = $('#slotNote');
        els.userName = $('#userName');
        els.userPhone = $('#userPhone');
        els.userEmail = $('#userEmail');
        els.userNotes = $('#userNotes');
        els.summaryService = $('#summaryService');
        els.summaryStaff = $('#summaryStaff');
        els.summaryDate = $('#summaryDate');
        els.summaryTime = $('#summaryTime');
        els.summaryDuration = $('#summaryDuration');
        els.totalPrice = $('#totalPrice');
        els.confirmBtn = $('#confirmBtn');
        els.message = $('#dossierMessage');
        els.bookingForm = $('#bookingForm');
        els.bookingSection = $('#bookingSection');
        els.bookingResult = $('#bookingResult');
        els.confirmRef = $('#confirmRef');
        els.confirmText = $('#confirmText');
        els.confirmService = $('#confirmService');
        els.confirmStaff = $('#confirmStaff');
        els.confirmDate = $('#confirmDate');
        els.confirmTime = $('#confirmTime');
        els.confirmTotal = $('#confirmTotal');

        if (!els.bookingForm) {
            return;
        }

        on(els.serviceSelect, 'change', onServiceChange);

        on(els.staffSelect, 'change', function (event) {
            var option = event.target.selectedOptions[0];

            state.staffId = option && option.value ? option.value : '';
            state.staffName = option && option.value ? option.textContent : '';
            updateSummary();
        });

        [els.userName, els.userPhone, els.userEmail].forEach(function (field) {
            on(field, 'input', updateSummary);
        });

        on(els.prevMonth, 'click', function () {
            moveMonth(-1);
        });

        on(els.nextMonth, 'click', function () {
            moveMonth(1);
        });

        on(els.confirmBtn, 'click', submitBooking);
        on($('#bookAnother'), 'click', resetBooking);

        initCalendarKeys();
        renderCalendar();
        renderSlots();
        updateSummary();
        loadServices();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
