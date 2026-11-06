@extends('frontend.layout.main')

@section('title', 'Rate Us — The Garrison')

@section('body-class', 'page-testimonial')

@section('main-section')
    <section class="booking-hero">
        <div class="shell booking-hero__inner">
            <div class="section-head">
                <p class="eyebrow">Letters of Credit</p>
                <h1 class="display display--lg">
                    Leave Your <span class="serif-em">Credit</span>
                </h1>
                <p class="lead">
                    The house keeps a ledger of every guest's note. Write yours and it will be read
                    before the morning shift.
                </p>
            </div>

            <div class="booking-hero__meta">
                <div class="booking-hero__meta-item">
                    Entries kept
                    <strong>Open ledger</strong>
                </div>
                <div class="booking-hero__meta-item">
                    Published
                    <strong>With your name</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell contact">
            <div class="contact__details">
                <form class="steps" id="testimonial-form" novalidate>
                    <section class="step">
                        <header class="step__head">
                            <span class="step__num">01</span>
                            <h2 class="step__title">Your Note</h2>
                        </header>

                        <div class="step__body">
                            <div class="field">
                                <label class="field__label" for="testimonial-name">
                                    Your name <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input
                                    class="field__control"
                                    type="text"
                                    id="testimonial-name"
                                    name="name"
                                    autocomplete="name"
                                    required
                                    placeholder="As it should appear in the ledger"
                                />
                            </div>

                            <div class="field">
                                <label class="field__label" for="testimonial-profession">
                                    Occupation <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input
                                    class="field__control"
                                    type="text"
                                    id="testimonial-profession"
                                    name="user_profession"
                                    required
                                    placeholder="Merchant, clerk, engineer&hellip;"
                                />
                            </div>

                            <div class="field">
                                <label class="field__label" for="testimonial-review">
                                    Your credit <span class="req" aria-hidden="true">*</span>
                                </label>
                                <textarea
                                    class="field__control"
                                    id="testimonial-review"
                                    name="message"
                                    rows="5"
                                    required
                                    placeholder="How was the chair, the cut, the hour?"
                                ></textarea>
                            </div>

                            <div class="field">
                                <span class="field__label" id="rating-label">
                                    Rating <span class="req" aria-hidden="true">*</span>
                                </span>
                                <div class="rating-input" role="radiogroup" aria-labelledby="rating-label">
                                    <input type="radio" name="testimonial-rating" id="star5" value="5" required />
                                    <label for="star5" aria-label="5 stars">&#9733;</label>
                                    <input type="radio" name="testimonial-rating" id="star4" value="4" />
                                    <label for="star4" aria-label="4 stars">&#9733;</label>
                                    <input type="radio" name="testimonial-rating" id="star3" value="3" />
                                    <label for="star3" aria-label="3 stars">&#9733;</label>
                                    <input type="radio" name="testimonial-rating" id="star2" value="2" />
                                    <label for="star2" aria-label="2 stars">&#9733;</label>
                                    <input type="radio" name="testimonial-rating" id="star1" value="1" />
                                    <label for="star1" aria-label="1 star">&#9733;</label>
                                </div>
                            </div>

                            <div id="testimonialMessage" role="status" aria-live="polite"></div>

                            <button class="btn btn--gold" type="submit" id="testimonialSubmit">
                                Submit the Credit
                            </button>
                        </div>
                    </section>
                </form>
            </div>

            <div>
                <h2 class="subhead">The Ledger</h2>
                <div class="credits" id="creditsList" aria-live="polite" aria-busy="true">
                    <div class="state">
                        <span class="state__mark">&hellip;</span>
                        <span class="state__title">Opening the ledger</span>
                        <span class="state__text">Fetching the guests&rsquo; notes&hellip;</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
