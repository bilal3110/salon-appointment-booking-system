@extends('frontend.layout.main')

@section('title', 'Reserve Your Chair — The Garrison')

@section('body-class', 'page-booking')

@section('main-section')
    {{-- ============================ BOOKING MASTHEAD ============================ --}}
    <section class="booking-hero" id="bookingMasthead">
        <div class="shell booking-hero__inner">
            <div class="section-head">
                <p class="eyebrow">The Appointment Ledger</p>
                <h1 class="display display--lg">
                    Reserve Your <span class="serif-em">Chair</span>
                </h1>
                <p class="lead">
                    Five short steps — name the treatment, the artisan and the hour. The house keeps the
                    book in pen and ink; nothing is held until you confirm.
                </p>
            </div>

            <div class="booking-hero__meta">
                <div class="booking-hero__meta-item">
                    Address
                    <strong>Gulberg, Lyallpur</strong>
                </div>
                <div class="booking-hero__meta-item">
                    Telephone
                    <strong><a href="tel:03244050830">0324 4050830</a></strong>
                </div>
                <div class="booking-hero__meta-item">
                    Hours
                    <strong>Mon — Fri 9–20 &middot; Sat 9–18</strong>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ BOOKING LEDGER ============================ --}}
    <section class="section" id="bookingSection">
        <div class="shell booking-layout">
            <form class="steps" id="bookingForm" novalidate>
                {{-- 01 · Service --}}
                <section class="step" aria-labelledby="stepServiceTitle">
                    <header class="step__head">
                        <span class="step__num">01</span>
                        <h2 class="step__title" id="stepServiceTitle">The Treatment</h2>
                        <span class="step__note">What shall we do for you?</span>
                    </header>

                    <div class="step__body">
                        <div class="field select">
                            <label class="field__label" for="serviceSelect">
                                Service <span class="req" aria-hidden="true">*</span>
                            </label>
                            <select class="field__control" id="serviceSelect" required aria-describedby="serviceHint">
                                <option value="">Choose a treatment&hellip;</option>
                            </select>
                            <p class="field__hint" id="serviceHint">
                                Rates and chair time are shown once a treatment is chosen.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- 02 · Artisan --}}
                <section class="step" aria-labelledby="stepStaffTitle">
                    <header class="step__head">
                        <span class="step__num">02</span>
                        <h2 class="step__title" id="stepStaffTitle">The Artisan</h2>
                        <span class="step__note" id="staffNote">Choose a treatment first</span>
                    </header>

                    <div class="step__body">
                        <div class="field select">
                            <label class="field__label" for="staffSelect">
                                Staff member <span class="req" aria-hidden="true">*</span>
                            </label>
                            <select class="field__control" id="staffSelect" required disabled>
                                <option value="">Choose a treatment first&hellip;</option>
                            </select>
                            <p class="field__hint" id="staffHint">
                                Only the artisans who hold the selected treatment are listed.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- 03 · Date --}}
                <section class="step" aria-labelledby="stepDateTitle">
                    <header class="step__head">
                        <span class="step__num">03</span>
                        <h2 class="step__title" id="stepDateTitle">The Date</h2>
                        <span class="step__note" id="dateNote">Sundays the house is closed</span>
                    </header>

                    <div class="step__body">
                        <div class="calendar">
                            <div class="calendar__head">
                                <button class="calendar__nav" type="button" id="prevMonth" aria-label="Previous month">
                                    &lsaquo;
                                </button>
                                <span class="calendar__month" id="currentMonth" aria-live="polite"></span>
                                <button class="calendar__nav" type="button" id="nextMonth" aria-label="Next month">
                                    &rsaquo;
                                </button>
                            </div>
                            <div class="calendar__grid" id="calendarGrid" role="group" aria-label="Choose an appointment date"></div>
                        </div>
                    </div>
                </section>

                {{-- 04 · Hour --}}
                <section class="step" aria-labelledby="stepTimeTitle">
                    <header class="step__head">
                        <span class="step__num">04</span>
                        <h2 class="step__title" id="stepTimeTitle">The Hour</h2>
                        <span class="step__note" id="slotNote">Choose a date to see the hours</span>
                    </header>

                    <div class="step__body">
                        <div class="slots" id="timeSlots" role="group" aria-label="Choose an appointment hour"></div>
                    </div>
                </section>

                {{-- 05 · Particulars --}}
                <section class="step" aria-labelledby="stepDetailsTitle">
                    <header class="step__head">
                        <span class="step__num">05</span>
                        <h2 class="step__title" id="stepDetailsTitle">Your Particulars</h2>
                        <span class="step__note">For the confirmation call</span>
                    </header>

                    <div class="step__body">
                        <div class="step__grid">
                            <div class="field">
                                <label class="field__label" for="userName">
                                    Full name <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input
                                    class="field__control"
                                    type="text"
                                    id="userName"
                                    name="customer_name"
                                    autocomplete="name"
                                    placeholder="As it should appear on the book"
                                />
                            </div>

                            <div class="field">
                                <label class="field__label" for="userPhone">
                                    Telephone <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input
                                    class="field__control"
                                    type="tel"
                                    id="userPhone"
                                    name="customer_contact"
                                    autocomplete="tel"
                                    placeholder="03xx xxxxxxx"
                                />
                            </div>
                        </div>

                        <div class="field">
                            <label class="field__label" for="userEmail">
                                Email <span class="req" aria-hidden="true">*</span>
                            </label>
                            <input
                                class="field__control"
                                type="email"
                                id="userEmail"
                                name="customer_email"
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                        </div>

                        <div class="field">
                            <label class="field__label" for="userNotes">Notes for the artisan</label>
                            <textarea
                                class="field__control"
                                id="userNotes"
                                name="notes"
                                rows="3"
                                placeholder="Optional — anything the chair should know"
                            ></textarea>
                        </div>
                    </div>
                </section>
            </form>

            {{-- Dossier — the running summary --}}
            <aside class="dossier" id="dossier" aria-labelledby="dossierTitle">
                <div class="dossier__head">
                    <h2 class="dossier__title" id="dossierTitle">Appointment Dossier</h2>
                    <span class="dossier__stamp">Draft</span>
                </div>

                <div class="dossier__rows">
                    <div class="dossier__row">
                        <span class="dossier__label">Treatment</span>
                        <span class="dossier__dots"></span>
                        <span class="dossier__value" id="summaryService">Not chosen</span>
                    </div>
                    <div class="dossier__row">
                        <span class="dossier__label">Artisan</span>
                        <span class="dossier__dots"></span>
                        <span class="dossier__value" id="summaryStaff">Not chosen</span>
                    </div>
                    <div class="dossier__row">
                        <span class="dossier__label">Date</span>
                        <span class="dossier__dots"></span>
                        <span class="dossier__value" id="summaryDate">Not chosen</span>
                    </div>
                    <div class="dossier__row">
                        <span class="dossier__label">Hour</span>
                        <span class="dossier__dots"></span>
                        <span class="dossier__value" id="summaryTime">Not chosen</span>
                    </div>
                    <div class="dossier__row">
                        <span class="dossier__label">Chair time</span>
                        <span class="dossier__dots"></span>
                        <span class="dossier__value" id="summaryDuration">&mdash;</span>
                    </div>
                </div>

                <div class="dossier__total">
                    <span class="dossier__total-label">Total</span>
                    <span class="dossier__total-value" id="totalPrice">PKR 0</span>
                </div>

                <div class="dossier__foot">
                    <div id="dossierMessage" role="status" aria-live="polite"></div>

                    <button class="btn btn--gold btn--block" type="button" id="confirmBtn" disabled>
                        Confirm the Appointment
                    </button>

                    <p class="dossier__note">
                        A confirmation call follows once the book is settled. Cancel or move the hour by
                        telephone.
                    </p>
                </div>
            </aside>
        </div>
    </section>

    {{-- ============================ CONFIRMATION ============================ --}}
    <section class="section" id="bookingResult" hidden>
        <div class="shell">
            <div class="receipt">
                <div class="receipt__inner">
                    <span class="confirm__seal" aria-hidden="true">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 12.5 9.5 18 20 6.5" />
                        </svg>
                    </span>

                    <p class="eyebrow eyebrow--center">The Book Is Set</p>

                    <h2 class="confirm__title">Your Appointment Is Confirmed</h2>

                    <p class="confirm__text" id="confirmText">
                        Thank you. The house has your chair reserved.
                    </p>

                    <p class="confirm__ref">
                        Reference
                        <strong id="confirmRef">&mdash;</strong>
                    </p>

                    <div class="dossier__rows receipt__rows">
                        <div class="dossier__row">
                            <span class="dossier__label">Treatment</span>
                            <span class="dossier__dots"></span>
                            <span class="dossier__value" id="confirmService">&mdash;</span>
                        </div>
                        <div class="dossier__row">
                            <span class="dossier__label">Artisan</span>
                            <span class="dossier__dots"></span>
                            <span class="dossier__value" id="confirmStaff">&mdash;</span>
                        </div>
                        <div class="dossier__row">
                            <span class="dossier__label">Date</span>
                            <span class="dossier__dots"></span>
                            <span class="dossier__value" id="confirmDate">&mdash;</span>
                        </div>
                        <div class="dossier__row">
                            <span class="dossier__label">Hour</span>
                            <span class="dossier__dots"></span>
                            <span class="dossier__value" id="confirmTime">&mdash;</span>
                        </div>
                        <div class="dossier__row">
                            <span class="dossier__label">Total</span>
                            <span class="dossier__dots"></span>
                            <span class="dossier__value" id="confirmTotal">&mdash;</span>
                        </div>
                    </div>

                    <a class="btn btn--ghost" href="{{ route('frontend.index') }}">
                        Return to the House
                    </a>
                    <button class="link-line" type="button" id="bookAnother">
                        Book another chair
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('Frontend/js/booking.js') }}" defer></script>
@endsection
