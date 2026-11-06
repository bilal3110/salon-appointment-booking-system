<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Management Office — The Garrison</title>

    <link rel="shortcut icon" href="{{ asset('Frontend/assets/icon.png') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap"
    />
    <link rel="stylesheet" href="{{ asset('dashboard/css/admin.css') }}">
</head>

<body class="auth">

    @include('dashboard.layout.icons')

    <aside class="auth__aside">
        <div class="auth__mark">
            <span class="auth__seal" aria-hidden="true">TG</span>
            <span>
                <p class="auth__wordmark">The Garrison</p>
                <p class="auth__sub">Est. 1919 · Management Office</p>
            </span>
        </div>

        <div class="auth__claim">
            <h1 class="auth__claim-title">Run the establishment like a gentleman.</h1>
            <div class="auth__claim-rule"></div>
            <p class="auth__claim-text">
                The ledger behind every chair — appointments, clients, services and the takings, kept in
                one quiet office.
            </p>
        </div>

        <p class="auth__foot">By order of the proprietor</p>
    </aside>

    <main class="auth__main">
        <div class="auth__card">
            <p class="eyebrow">Management Office</p>
            <h2 class="auth__title">Welcome back</h2>
            <p class="auth__lede">Sign in to open the ledger.</p>

            <div class="auth__alert" id="authAlert" role="alert">
                <svg class="icon" aria-hidden="true"><use href="#i-circle-alert"></use></svg>
                <span id="authAlertText"></span>
            </div>

            <form id="loginForm" novalidate>
                <div class="field">
                    <label class="field__label" for="email">Email address <span class="req">*</span></label>
                    <input class="input" type="email" id="email" name="email" placeholder="name@garrison.test"
                        required autocomplete="username" autofocus>
                    <p class="field__error"></p>
                </div>

                <div class="field">
                    <label class="field__label" for="password">Password <span class="req">*</span></label>
                    <input class="input" type="password" id="password" name="password" placeholder="••••••••"
                        required minlength="6" autocomplete="current-password">
                    <p class="field__error"></p>
                </div>

                <button type="submit" class="btn btn--primary btn--lg btn--block" id="loginSubmit">
                    <svg class="icon" aria-hidden="true"><use href="#i-lock"></use></svg>
                    Sign in
                </button>
            </form>

            <a class="auth__back" href="{{ route('frontend.index') }}">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to the public site
            </a>
        </div>
    </main>

    <script>
        (function () {
            'use strict';

            var form = document.getElementById('loginForm');
            var alertBox = document.getElementById('authAlert');
            var alertText = document.getElementById('authAlertText');
            var submit = document.getElementById('loginSubmit');
            var csrf = document.querySelector('meta[name="csrf-token"]');

            function showError(message) {
                alertText.textContent = message;
                alertBox.classList.add('is-visible');
            }

            function clearError() {
                alertBox.classList.remove('is-visible');
            }

            function fieldError(input, message) {
                var wrapper = input.closest('.field');
                if (!wrapper) {
                    return;
                }
                wrapper.classList.add('has-error');
                var slot = wrapper.querySelector('.field__error');
                if (slot) {
                    slot.innerHTML =
                        '<svg class="icon" aria-hidden="true"><use href="#i-circle-alert"></use></svg>' +
                        '<span>' + message + '</span>';
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                clearError();

                var email = document.getElementById('email');
                var password = document.getElementById('password');
                var valid = true;

                [email, password].forEach(function (input) {
                    var wrapper = input.closest('.field');
                    if (wrapper) {
                        wrapper.classList.remove('has-error');
                        var slot = wrapper.querySelector('.field__error');
                        if (slot) {
                            slot.innerHTML = '';
                        }
                    }
                });

                if (!email.value.trim()) {
                    fieldError(email, 'Enter your email address.');
                    valid = false;
                } else if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
                    fieldError(email, 'That does not look like an email address.');
                    valid = false;
                }

                if (!password.value) {
                    fieldError(password, 'Enter your password.');
                    valid = false;
                } else if (password.value.length < 6) {
                    fieldError(password, 'Password must be at least 6 characters.');
                    valid = false;
                }

                if (!valid) {
                    return;
                }

                submit.classList.add('is-busy');
                submit.disabled = true;

                fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf ? csrf.content : ''
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        email: email.value.trim(),
                        password: password.value
                    })
                })
                    .then(function (response) {
                        return response
                            .json()
                            .catch(function () {
                                return {};
                            })
                            .then(function (data) {
                                return { ok: response.ok, data: data };
                            });
                    })
                    .then(function (result) {
                        if (result.ok) {
                            window.location.href = '/admin-panel';
                            return;
                        }
                        submit.classList.remove('is-busy');
                        submit.disabled = false;
                        showError(result.data.message || 'Invalid credentials. Try again.');
                    })
                    .catch(function () {
                        submit.classList.remove('is-busy');
                        submit.disabled = false;
                        showError('Something went wrong. Check your connection and try again.');
                    });
            });
        })();
    </script>
</body>

</html>
