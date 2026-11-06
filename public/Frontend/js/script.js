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

    var roman = function (num) {
        var map = [
            [10, 'X'],
            [9, 'IX'],
            [5, 'V'],
            [4, 'IV'],
            [1, 'I']
        ];
        var out = '';
        var left = Number(num) || 0;

        map.forEach(function (pair) {
            while (left >= pair[0]) {
                out += pair[1];
                left -= pair[0];
            }
        });

        return out || 'I';
    };

    var formatPrice = function (value) {
        var num = Number(value) || 0;
        return Number.isInteger(num) ? String(num) : num.toFixed(2);
    };

    var prefersReducedMotion = function () {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };


    function initNavState() {
        var nav = $('#siteNav');
        if (!nav) {
            return;
        }

        var apply = function () {
            nav.classList.toggle('is-stuck', window.scrollY > 24);
        };

        apply();
        on(window, 'scroll', apply, { passive: true });
    }

    function initDrawer() {
        var drawer = $('#mobileNav');
        var toggle = $('#menuToggle');

        if (!drawer || !toggle) {
            return;
        }

        var lastFocused = null;

        var open = function () {
            lastFocused = document.activeElement;
            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Close menu');
            document.body.classList.add('is-locked');

            var first = drawer.querySelector('a[href], button:not([disabled])');
            if (first) {
                first.focus();
            }
        };

        var close = function () {
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
            document.body.classList.remove('is-locked');

            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        };

        var isOpen = function () {
            return drawer.classList.contains('is-open');
        };

        on(toggle, 'click', function () {
            if (isOpen()) {
                close();
            } else {
                open();
            }
        });

        $$('[data-drawer-close]', drawer).forEach(function (element) {
            on(element, 'click', close);
        });

        $$('a[href]', drawer).forEach(function (link) {
            on(link, 'click', close);
        });

        on(document, 'keydown', function (event) {
            if (!isOpen()) {
                return;
            }

            if (event.key === 'Escape') {
                close();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            var focusables = $$('a[href], button:not([disabled]), input, select, textarea', drawer).filter(
                function (element) {
                    return element.offsetParent !== null;
                }
            );

            if (!focusables.length) {
                return;
            }

            var first = focusables[0];
            var last = focusables[focusables.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        on(window, 'resize', function () {
            if (window.innerWidth > 960 && isOpen()) {
                close();
            }
        });
    }


    function initReveal() {
        var items = $$('[data-reveal]');

        if (!items.length) {
            return;
        }

        var reveal = function (element) {
            element.classList.add('is-visible');
        };

        if (!('IntersectionObserver' in window) || prefersReducedMotion()) {
            items.forEach(reveal);
            return;
        }

        var pending = items.slice();
        var ticking = false;
        var sweep = function () {
            var height = window.innerHeight;

            pending = pending.filter(function (element) {
                if (element.classList.contains('is-visible')) {
                    return false;
                }

                var rect = element.getBoundingClientRect();

                if (rect.top < height * 0.98 && rect.bottom > -240) {
                    reveal(element);
                    return false;
                }

                return true;
            });

            if (!pending.length) {
                window.removeEventListener('scroll', onScroll);
                window.removeEventListener('resize', onScroll);
            }
        };

        var onScroll = function () {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(function () {
                    ticking = false;
                    sweep();
                });
            }
        };

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        reveal(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0, rootMargin: '0px 0px 12% 0px' }
        );

        items.forEach(function (element) {
            observer.observe(element);
        });

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        window.addEventListener('load', onScroll);
        sweep();
    }

    function initParallax() {
        var media = $('.hero__media img');

        if (!media || prefersReducedMotion()) {
            return;
        }

        var ticking = false;

        var update = function () {
            var offset = window.scrollY;

            if (offset < window.innerHeight * 1.3) {
                media.style.transform = 'translate3d(0,' + offset * 0.15 + 'px,0)';
            }

            ticking = false;
        };

        on(
            window,
            'scroll',
            function () {
                if (!ticking) {
                    ticking = true;
                    window.requestAnimationFrame(update);
                }
            },
            { passive: true }
        );
    }

    var SERVICE_META = {
        'classic haircut': { duration: 45, glyph: 'shears' },
        'beard trim': { duration: 30, glyph: 'razor' },
        "men's facial": { duration: 60, glyph: 'facial' },
        'hair coloring': { duration: 120, glyph: 'bottle' },
        'blowout styling': { duration: 45, glyph: 'dryer' },
        manicure: { duration: 45, glyph: 'manicure' },
        'classic pedicure': { duration: 60, glyph: 'basin' },
        'makeup session': { duration: 90, glyph: 'lipstick' }
    };

    var DEFAULT_SERVICE_META = { duration: 60, glyph: 'shears' };

    var serviceMeta = function (name) {
        var key = String(name || '')
            .trim()
            .toLowerCase();
        return SERVICE_META[key] || DEFAULT_SERVICE_META;
    };

    function serviceRow(service, index) {
        var meta = serviceMeta(service.service_name);
        var house = service.service_type === 'women' ? 'Ladies' : 'Gentlemen';
        var row = document.createElement('article');

        row.className = 'ledger__row';
        row.dataset.type = service.service_type;
        row.innerHTML =
            '<div class="ledger__plate plate">' +
            '<span class="plate__num">' +
            roman(index + 1) +
            '</span>' +
            '<svg class="plate__glyph" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><use href="#g-' +
            meta.glyph +
            '"></use></svg>' +
            '<span class="plate__tag">' +
            house +
            '</span>' +
            '</div>' +
            '<div class="ledger__content">' +
            '<h3 class="ledger__title">' +
            escapeHtml(service.service_name) +
            '</h3>' +
            '<p class="ledger__desc">' +
            escapeHtml(service.description || 'A treatment given with the house in mind.') +
            '</p>' +
            '<div class="ledger__meta">' +
            '<span class="ledger__meta-item">' +
            meta.duration +
            ' min chair time</span>' +
            '<span class="ledger__meta-item">Reserved by appointment</span>' +
            '</div>' +
            '</div>' +
            '<div class="ledger__price">' +
            '<div class="ledger__price-value">' +
            formatPrice(service.price) +
            '</div>' +
            '<div class="ledger__price-label">PKR</div>' +
            '</div>' +
            '<div class="ledger__action">' +
            '<a class="btn btn--ghost" href="' +
            bookingHref() +
            '?service=' +
            encodeURIComponent(service.id) +
            '">Reserve</a>' +
            '</div>';

        return row;
    }

    var bookingHref = function () {
        var link = document.body.getAttribute('data-booking-url');
        return link || 'booking';
    };

    function applyFilter(filter) {
        var rows = $$('#servicesLedger .ledger__row');
        var visible = 0;

        rows.forEach(function (row) {
            var match = filter === 'all' || row.dataset.type === filter;
            row.hidden = !match;

            if (match) {
                visible += 1;
            }
        });

        var empty = $('#servicesEmpty');

        if (empty) {
            empty.hidden = visible !== 0;
        }
    }

    function initServiceFilters(services) {
        var buttons = $$('.filter__btn');

        buttons.forEach(function (button) {
            on(button, 'click', function () {
                buttons.forEach(function (other) {
                    var active = other === button;
                    other.classList.toggle('is-active', active);
                    other.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                applyFilter(button.dataset.filter);
            });
        });

        var counts = { all: services.length, men: 0, women: 0 };

        services.forEach(function (service) {
            if (counts[service.service_type] !== undefined) {
                counts[service.service_type] += 1;
            }
        });

        Object.keys(counts).forEach(function (key) {
            var target = $('[data-count="' + key + '"]');

            if (target) {
                target.textContent = String(counts[key]);
            }
        });
    }

    function renderServices(services) {
        var ledger = $('#servicesLedger');

        if (!ledger) {
            return;
        }

        ledger.setAttribute('aria-busy', 'false');
        ledger.innerHTML = '';

        if (!services.length) {
            ledger.innerHTML =
                '<div class="state">' +
                '<span class="state__mark">&mdash;</span>' +
                '<span class="state__title">The ledger is closed</span>' +
                '<span class="state__text">No treatments are listed at present. Please call the house.</span>' +
                '</div>';
            return;
        }

        var fragment = document.createDocumentFragment();

        services.forEach(function (service, index) {
            fragment.appendChild(serviceRow(service, index));
        });

        ledger.appendChild(fragment);

        initServiceFilters(services);
    }

    function loadServices() {
        var ledger = $('#servicesLedger');

        if (!ledger) {
            return;
        }

        fetch('/api/services', { headers: { Accept: 'application/json' } })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Service list unavailable');
                }
                return response.json();
            })
            .then(function (services) {
                renderServices(Array.isArray(services) ? services : []);
            })
            .catch(function () {
                ledger.setAttribute('aria-busy', 'false');
                ledger.innerHTML =
                    '<div class="state">' +
                    '<span class="state__mark">!</span>' +
                    '<span class="state__title">The ledger will not open</span>' +
                    '<span class="state__text">The price list could not be reached. Please telephone the house on 0324 4050830.</span>' +
                    '</div>';
            });
    }


    function creditCard(entry) {
        var name = entry.name || 'Anonymous';
        var role = entry.user_profession || 'Guest of the house';
        var message = entry.message || '';
        var rating = Number(entry.rating) || 0;

        var stars = '';
        var index;

        for (index = 1; index <= 5; index += 1) {
            stars +=
                '<span class="' +
                (index <= rating ? '' : 'is-off') +
                '" aria-hidden="true">&#9733;</span>';
        }

        return (
            '<article class="credit">' +
            '<span class="credit__mark" aria-hidden="true">&ldquo;</span>' +
            '<p class="credit__text">' +
            escapeHtml(message) +
            '</p>' +
            '<div class="credit__foot">' +
            '<span class="credit__who">' +
            '<span class="credit__name">' +
            escapeHtml(name) +
            '</span>' +
            '<span class="credit__role">' +
            escapeHtml(role) +
            '</span>' +
            '</span>' +
            '<span class="credit__stars" role="img" aria-label="Rated ' +
            rating +
            ' out of 5">' +
            stars +
            '</span>' +
            '</div>' +
            '</article>'
        );
    }

    function renderCredits(credits) {
        var list = $('#creditsList');

        if (!list) {
            return;
        }

        list.setAttribute('aria-busy', 'false');
        list.innerHTML = '';

        if (!credits.length) {
            list.innerHTML =
                '<div class="state">' +
                '<span class="state__mark">&mdash;</span>' +
                '<span class="state__title">The ledger is empty</span>' +
                '<span class="state__text">No guest has left a credit yet. Yours would be the first.</span>' +
                '</div>';
            return;
        }

        list.innerHTML = credits.map(creditCard).join('');
    }

    function loadCredits() {
        var list = $('#creditsList');

        if (!list) {
            return;
        }

        fetch('/api/testimonials', { headers: { Accept: 'application/json' } })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Credits unavailable');
                }
                return response.json();
            })
            .then(function (payload) {
                renderCredits(payload && payload.data ? payload.data : []);
            })
            .catch(function () {
                list.setAttribute('aria-busy', 'false');
                list.innerHTML =
                    '<div class="state">' +
                    '<span class="state__mark">!</span>' +
                    '<span class="state__title">The ledger will not open</span>' +
                    '<span class="state__text">The guests&rsquo; notes could not be reached just now.</span>' +
                    '</div>';
            });
    }


    function initCreditForm() {
        var form = $('#testimonial-form');

        if (!form) {
            return;
        }

        var message = $('#testimonialMessage');
        var submit = $('#testimonialSubmit');
        var tokenMeta = $('meta[name="csrf-token"]');
        var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

        var say = function (text, isError) {
            if (!message) {
                return;
            }

            message.innerHTML = text
                ? '<div class="dossier__message' +
                  (isError ? ' dossier__message--error' : '') +
                  '">' +
                  escapeHtml(text) +
                  '</div>'
                : '';
        };

        on(form, 'submit', function (event) {
            event.preventDefault();

            var name = $('#testimonial-name').value.trim();
            var profession = $('#testimonial-profession').value.trim();
            var review = $('#testimonial-review').value.trim();
            var ratingInput = form.querySelector('input[name="testimonial-rating"]:checked');

            if (!name || !profession || !review || !ratingInput) {
                say('Every field is required before the credit can be entered.', true);
                return;
            }

            if (submit) {
                submit.disabled = true;
                submit.textContent = 'Entering the ledger…';
            }

            fetch('/api/testimonials', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    name: name,
                    user_profession: profession,
                    message: review,
                    rating: parseInt(ratingInput.value, 10)
                })
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, payload: payload };
                    });
                })
                .then(function (result) {
                    if (!result.ok) {
                        var errors = result.payload && result.payload.errors;
                        var first = errors ? Object.keys(errors)[0] : null;

                        throw new Error(
                            first && errors[first] && errors[first][0]
                                ? errors[first][0]
                                : 'The ledger would not accept the credit. Please try again.'
                        );
                    }

                    form.reset();
                    say('Your credit is in the ledger. Thank you.', false);
                    loadCredits();
                })
                .catch(function (error) {
                    say(error.message || 'Something went wrong. Please try again.', true);
                })
                .then(function () {
                    if (submit) {
                        submit.disabled = false;
                        submit.textContent = 'Submit the Credit';
                    }
                });
        });
    }


    function init() {
        initNavState();
        initDrawer();
        initReveal();
        initParallax();
        loadServices();
        loadCredits();
        initCreditForm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
