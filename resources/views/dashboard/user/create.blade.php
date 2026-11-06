@extends('dashboard.layout.main')

@section('title', 'New Account')
@section('section-name', 'The House')
@section('page-name', 'New Account')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">New Account</h1>
            <p class="page-lede">
                Open a sign-in for a barber or another proprietor.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.users') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to accounts
            </a>
        </div>
    </div>

    <div class="grid-2--even grid-2">

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Account Details</h2>
                    <p class="panel__sub">The person who will sign in</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="createUserForm" novalidate>
                    <div class="field">
                        <label class="field__label" for="userName">Full name <span class="req">*</span></label>
                        <input class="input" type="text" id="userName" name="name" placeholder="Ava Bennett"
                            required autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="userType">Role <span class="req">*</span></label>
                            <select class="select" id="userType" name="type" required>
                                <option value="">Select role</option>
                                <option value="admin">Proprietor</option>
                                <option value="staff">Barber</option>
                            </select>
                            <p class="field__hint">Proprietors see the whole house. Barbers see their own chair.</p>
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="userPhone">Contact number</label>
                            <input class="input" type="text" id="userPhone" name="phone" placeholder="+92 300 1234567"
                                autocomplete="off">
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="userEmail">Email address <span class="req">*</span></label>
                        <input class="input" type="email" id="userEmail" name="email" placeholder="name@garrison.test"
                            required autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <hr class="rule" style="margin: 26px 0 22px">

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="userPassword">Password <span class="req">*</span></label>
                            <input class="input" type="password" id="userPassword" name="password" required
                                autocomplete="new-password">
                            <p class="field__hint">At least 8 characters.</p>
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="passwordConfirmation">Confirm password <span class="req">*</span></label>
                            <input class="input" type="password" id="passwordConfirmation" name="password_confirmation"
                                required autocomplete="new-password">
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard.users') }}" class="btn btn--ghost">Cancel</a>
                        <button type="submit" class="btn btn--primary">
                            <svg class="icon" aria-hidden="true"><use href="#i-user-plus"></use></svg>
                            Create account
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="panel">
            <div class="panel__head">
                <h2 class="panel__title">House Notes</h2>
            </div>
            <div class="panel__body">
                <ul class="notes-list">
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-id-card"></use></svg>
                        <span><strong>An account is a sign-in.</strong> It is not a client record — clients live in the ledger.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-contact"></use></svg>
                        <span><strong>Barbers need a barber record too.</strong> Add one afterwards so they can take appointments.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-lock"></use></svg>
                        <span><strong>Passwords are never shown.</strong> They are stored hashed and cannot be read back.</span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>

@endsection
