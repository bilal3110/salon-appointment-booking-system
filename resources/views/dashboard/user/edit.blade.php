@extends('dashboard.layout.main')

@section('title', 'Edit Account')
@section('section-name', 'The House')
@section('page-name', 'Edit Account')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Edit Account</h1>
            <p class="page-lede">
                Record #{{ $id }} — adjust the sign-in or issue a new password.
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
                    <p class="panel__sub">Leave the password blank to keep the current one</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="editUserForm" data-user-id="{{ $id }}" novalidate>
                    <div class="field">
                        <label class="field__label" for="userName">Full name <span class="req">*</span></label>
                        <input class="input" type="text" id="userName" name="name" required autocomplete="off">
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
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="userPhone">Contact number</label>
                            <input class="input" type="text" id="userPhone" name="phone" autocomplete="off">
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="userEmail">Email address <span class="req">*</span></label>
                        <input class="input" type="email" id="userEmail" name="email" required autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <hr class="rule" style="margin: 26px 0 22px">

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="userPassword">New password</label>
                            <input class="input" type="password" id="userPassword" name="password"
                                autocomplete="new-password" placeholder="Unchanged">
                            <p class="field__hint">At least 8 characters, if you are changing it.</p>
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="passwordConfirmation">Confirm new password</label>
                            <input class="input" type="password" id="passwordConfirmation" name="password_confirmation"
                                autocomplete="new-password" placeholder="Unchanged">
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard.users') }}" class="btn btn--ghost">Cancel</a>
                        <button type="submit" class="btn btn--primary">
                            <svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg>
                            Save changes
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
                        <svg class="icon" aria-hidden="true"><use href="#i-lock"></use></svg>
                        <span><strong>The current password stays</strong> until you type a new one here.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-shield-check"></use></svg>
                        <span><strong>Changing a role changes access.</strong> A barber sees only their own appointments.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-mail"></use></svg>
                        <span><strong>The email is the sign-in.</strong> It must be unique across the register.</span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>

@endsection
