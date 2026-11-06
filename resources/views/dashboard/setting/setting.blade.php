@extends('dashboard.layout.main')

@section('title', 'Settings')
@section('section-name', 'Settings')
@section('page-name', 'Your Account')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">Settings</p>
            <h1 class="page-title">Your Account</h1>
            <p class="page-lede">
                The sign-in you use to open the management office, and how you are known within it.
            </p>
        </div>
    </div>

    <div class="grid-2--even grid-2">

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Profile</h2>
                    <p class="panel__sub">Your name, contact details and sign-in</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="settingsProfileForm" data-user-id="{{ $currentUser->id }}" novalidate>

                    <div class="field">
                        <label class="field__label" for="settingsName">Full name <span class="req">*</span></label>
                        <input class="input" type="text" id="settingsName" name="name"
                            value="{{ $currentUser->name }}" required autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="settingsEmail">Email address <span class="req">*</span></label>
                        <input class="input" type="email" id="settingsEmail" name="email"
                            value="{{ $currentUser->email }}" required autocomplete="off">
                        <p class="field__hint">This is the address you sign in with.</p>
                        <p class="field__error"></p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="settingsPhone">Contact number</label>
                        <input class="input" type="text" id="settingsPhone" name="phone"
                            value="{{ $currentUser->phone }}" autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <hr class="rule" style="margin: 26px 0 22px">

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="settingsPassword">New password</label>
                            <input class="input" type="password" id="settingsPassword" name="password"
                                autocomplete="new-password" placeholder="Unchanged">
                            <p class="field__hint">At least 8 characters, if you are changing it.</p>
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="settingsPasswordConfirmation">Confirm new password</label>
                            <input class="input" type="password" id="settingsPasswordConfirmation" name="password_confirmation"
                                autocomplete="new-password" placeholder="Unchanged">
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">
                            <svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg>
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="stack">
            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">Your Standing</h2>
                </div>
                <div class="panel__body">
                    <div class="row-between" style="margin-bottom: 18px">
                        <span class="avatar avatar--lg">{{ \Illuminate\Support\Str::of($currentUser->name)->substr(0, 2)->upper() }}</span>
                        <span style="text-align: right">
                            <span class="person__name" style="font-size: 15px">{{ $currentUser->name }}</span>
                            <span class="person__meta">{{ $currentUser->email }}</span>
                        </span>
                    </div>

                    <div class="detail">
                        <div class="detail__row">
                            <span class="detail__key">Role</span>
                            <span class="detail__val">
                                @if ($currentUser->type === 'admin')
                                    <span class="seal seal--admin">Proprietor</span>
                                @else
                                    <span class="seal seal--staff">Barber</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail__row">
                            <span class="detail__key">Contact</span>
                            <span class="detail__val">{{ $currentUser->phone ?: '—' }}</span>
                        </div>
                        <div class="detail__row">
                            <span class="detail__key">Access</span>
                            <span class="detail__val detail__val--muted">
                                @if ($currentUser->type === 'admin')
                                    The whole house — services, staff, accounts, reports and reviews.
                                @else
                                    Your own chair — your appointments and nothing else.
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">House Notes</h2>
                </div>
                <div class="panel__body">
                    <ul class="notes-list">
                        <li>
                            <svg class="icon" aria-hidden="true"><use href="#i-lock"></use></svg>
                            <span><strong>Passwords are hashed.</strong> Nobody — including the house — can read yours back.</span>
                        </li>
                        <li>
                            <svg class="icon" aria-hidden="true"><use href="#i-shield-check"></use></svg>
                            <span><strong>Your email is your sign-in.</strong> Changing it changes where you log in from.</span>
                        </li>
                        <li>
                            <svg class="icon" aria-hidden="true"><use href="#i-log-out"></use></svg>
                            <span><strong>Sign out from the top right</strong> or from the sidebar when you are done.</span>
                        </li>
                    </ul>
                </div>
            </section>
        </aside>

    </div>

@endsection
