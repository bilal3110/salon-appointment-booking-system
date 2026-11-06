@extends('dashboard.layout.main')

@section('title', 'Add Service')
@section('section-name', 'The House')
@section('page-name', 'Add Service')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Add a Service</h1>
            <p class="page-lede">
                A new entry appears on the public booking page the moment it is saved.
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
                    <p class="panel__sub">Everything a client needs to know before booking</p>
                </div>
            </div>

            <div class="panel__body">
                <form id="createServiceForm" novalidate>
                    <div class="field">
                        <label class="field__label" for="serviceName">Service name <span class="req">*</span></label>
                        <input class="input" type="text" id="serviceName" name="service_name"
                            placeholder="Hot towel shave" required autocomplete="off">
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
                            <input class="input" type="number" id="servicePrice" name="price" min="0" step="1"
                                placeholder="2500" required>
                            <p class="field__error"></p>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="serviceDescription">Description</label>
                        <textarea class="textarea" id="serviceDescription" name="description" rows="5"
                            placeholder="What the client receives, in the house's own words."></textarea>
                        <p class="field__hint">Shown on the public booking page.</p>
                        <p class="field__error"></p>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard.services') }}" class="btn btn--ghost">Cancel</a>
                        <button type="submit" class="btn btn--primary">
                            <svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg>
                            Create service
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
                        <svg class="icon" aria-hidden="true"><use href="#i-scissors"></use></svg>
                        <span><strong>Type matters.</strong> It decides which list the service appears under on the public site.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-banknote"></use></svg>
                        <span><strong>Price is fixed.</strong> Every booking records this figure, and completed work counts towards revenue.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-contact"></use></svg>
                        <span><strong>Barbers are assigned next.</strong> Open the barber's record to attach this service to them.</span>
                    </li>
                    <li>
                        <svg class="icon" aria-hidden="true"><use href="#i-inbox"></use></svg>
                        <span><strong>Nothing is invented.</strong> The ledger only ever counts bookings that were genuinely made.</span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>

@endsection
