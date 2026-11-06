@extends('dashboard.layout.main')

@section('title', 'Edit Barber')
@section('section-name', 'The House')
@section('page-name', 'Edit Barber')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Edit Barber</h1>
            <p class="page-lede">
                Record #{{ $staff->id }} — adjust what this barber performs and how they are introduced.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.staff') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to staff
            </a>
        </div>
    </div>

    <div class="grid-2--even grid-2">

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Barber Record</h2>
                    <p class="panel__sub">{{ $staff->user->name ?? 'Unknown barber' }}</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="editStaffForm" data-staff-id="{{ $staff->id }}" enctype="multipart/form-data" novalidate>

                    <div class="field">
                        <label class="field__label" for="staffUser">Sign-in account <span class="req">*</span></label>
                        <select class="select" id="staffUser" name="user_id" required>
                            <option value="">Select an account</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                            @endforeach
                        </select>
                        <p class="field__error"></p>
                    </div>

                    <div class="field">
                        <span class="field__label">Services performed <span class="req">*</span></span>
                        @if (count($services))
                            <div class="check-list">
                                @foreach ($services as $service)
                                    <label class="check">
                                        <input type="checkbox" name="service_id[]" value="{{ $service->id }}"
                                            @checked(in_array($service->id, $staff->services->pluck('id')->all()))>
                                        <span class="check__text">
                                            {{ $service->service_name }}
                                            <small>{{ ucfirst($service->service_type) }} · {{ number_format((float) $service->price, 0) }} PKR</small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="panel__sub">No services exist yet. Add them first so barbers can be assigned.</p>
                        @endif
                        <p class="field__error"></p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="staffPhoto">Portrait</label>
                        <div class="filepick">
                            <label class="btn btn--ghost btn--sm" for="staffPhoto">
                                <svg class="icon" aria-hidden="true"><use href="#i-upload"></use></svg>
                                Replace photo
                            </label>
                            <span class="filepick__name" id="staffPhotoName">No new file chosen</span>
                            <input type="file" id="staffPhoto" name="photo" accept="image/*" class="visually-hidden">
                        </div>
                        <p class="field__hint">Leave empty to keep the current portrait. Up to 2MB.</p>
                        <p class="field__error"></p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="staffBio">Short biography</label>
                        <textarea class="textarea" id="staffBio" name="bio" rows="4">{{ $staff->bio }}</textarea>
                        <p class="field__hint">Shown on the public site.</p>
                        <p class="field__error"></p>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard.staff') }}" class="btn btn--ghost">Cancel</a>
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
                        <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                        <span><strong>Removing a service closes the door</strong> on new bookings for it, but existing appointments stay in the ledger.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-id-card"></use></svg>
                        <span><strong>The sign-in can be swapped</strong> to another barber account if the record changes hands.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-image"></use></svg>
                        <span><strong>Replacing the portrait</strong> overwrites the old file — leave the field empty to keep it.</span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>

@endsection
