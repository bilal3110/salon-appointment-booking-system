@extends('dashboard.layout.main')

@section('title', 'Edit Service')
@section('section-name', 'The House')
@section('page-name', 'Edit Service')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Edit Service</h1>
            <p class="page-lede">
                Changes take effect at once, on the public page and in every new booking.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.services') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to services
            </a>
        </div>
    </div>

    <div class="grid-2--even grid-2">

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Service Details</h2>
                    <p class="panel__sub">Record #{{ $id }}</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="editServiceForm" data-service-id="{{ $id }}" novalidate>
                    <div class="field">
                        <label class="field__label" for="serviceName">Service name <span class="req">*</span></label>
                        <input class="input" type="text" id="serviceName" name="service_name" required autocomplete="off">
                        <p class="field__error"></p>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="serviceType">Service type <span class="req">*</span></label>
                            <select class="select" id="serviceType" name="service_type" required>
                                <option value="">Select type</option>
                                <option value="men">Men</option>
                                <option value="women">Women</option>
                            </select>
                            <p class="field__error"></p>
                        </div>

                        <div class="field">
                            <label class="field__label" for="servicePrice">Price (PKR) <span class="req">*</span></label>
                            <input class="input" type="number" id="servicePrice" name="price" min="0" step="1" required>
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="serviceDescription">Description</label>
                        <textarea class="textarea" id="serviceDescription" name="description" rows="5"></textarea>
                        <p class="field__hint">Shown on the public booking page.</p>
                        <p class="field__error"></p>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard.services') }}" class="btn btn--ghost">Cancel</a>
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
                        <span><strong>Past bookings keep their price.</strong> Changing the price never rewrites what a client was charged.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-scissors"></use></svg>
                        <span><strong>Assigned barbers are untouched.</strong> Edit a barber's record to change who performs this service.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-banknote"></use></svg>
                        <span><strong>Revenue follows completion.</strong> Only appointments marked completed count towards the takings.</span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>

@endsection
