@extends('frontend.layout.main')
@section('main-section')

    <body>
        <div class="container">
            <a href="{{ route('frontend.index') }}" class="back-link">← Back to Home</a>

            <div class="header">
                <h1><span class="header-icon"></span>Book an Appointment</h1>
                <p>Select your preferred service, staff, and time</p>
            </div>

            <form action="" id="bookingForm">
                <div class="booking-grid">
                    <div class="booking-section full-width">
                        <h2 class="section-title">Your Information</h2>
                        <p class="section-subtitle">Tell us who’s getting pampered today</p>

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" id="userName" class="form-input" placeholder="Enter your name" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" id="userPhone" class="form-input" placeholder="03xx-xxxxxxx" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" id="userEmail" class="form-input" placeholder="example@gmail.com" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea id="userNotes" class="form-input" rows="3" placeholder="Anything you want us to know?"></textarea>
                        </div>
                    </div>

                    <div class="booking-section">
                        <h2 class="section-title">Select Service</h2>
                        <p class="section-subtitle">Choose the treatment you'd like</p>
                        <div class="form-group">
                            <label class="form-label">Service</label>
                            <select class="form-select" id="serviceSelect">
                                <option value="">Choose a service</option>

                                {{-- <!-- Men Services -->
                                <option value="shelby_cut" data-price="1500">
                                    The Shelby Cut - PKR 1,500
                                </option>
                                <option value="royal_shave" data-price="2000">
                                    The Royal Shave - PKR 2,000
                                </option>
                                <option value="beard_sculpting" data-price="18000">
                                    Beard Sculpting - PKR 18,000
                                </option>

                                <!-- Women Services -->
                                <option value="grace_treatment" data-price="8000">
                                    The Grace Treatment - PKR 8,000
                                </option>
                                <option value="pollys_polish" data-price="4000">
                                    Polly's Polish - PKR 4,000
                                </option>
                                <option value="the_ada" data-price="12000">
                                    The Ada - PKR 12,000
                                </option>
                                <option value="hair_scalp_revival" data-price="10000">
                                    Hair & Scalp Revival - PKR 10,000
                                </option>
                                <option value="complete_affair" data-price="9500">
                                    The Complete Affair - PKR 9,500
                                </option>
                                <option value="private_hour" data-price="23000">
                                    The Private Hour - PKR 23,000
                                </option> --}}
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Staff Member</label>
                            <select class="form-select" id="staffSelect">
                                <option value="">Choose a staff member</option>
                                {{-- <option value="sarah">Sarah Johnson - Senior Stylist</option>
                                <option value="michael">Michael Chen - Master Barber</option>
                                <option value="emma">Emma Davis - Color Specialist</option>
                                <option value="james">James Wilson - Nail Technician</option>
                                <option value="olivia">Olivia Martinez - Esthetician</option> --}}
                            </select>
                        </div>
                    </div>

                    <div class="booking-section">
                        <h2 class="section-title">Select Date</h2>
                        <p class="section-subtitle">Pick your preferred date</p>

                        <div class="calendar">
                            <div class="calendar-header">
                                <button class="calendar-nav" id="prevMonth">‹</button>
                                <span class="calendar-month" id="currentMonth"></span>
                                <button class="calendar-nav" id="nextMonth">›</button>
                            </div>
                            <div class="calendar-grid" id="calendarGrid"></div>
                        </div>
                    </div>

                    <div class="booking-section full-width">
                        <h2 class="section-title">Available Time Slots</h2>
                        <p class="section-subtitle">Select your preferred time</p>

                        <div class="time-slots" id="timeSlots"></div>
                    </div>

                    <div class="booking-section full-width summary-section">
                        <h2 class="section-title">Appointment Summary</h2>
                        <div class="summary-row">
                            <span class="summary-label">Name:</span>
                            <span class="summary-value" id="summaryName">Not provided</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Phone:</span>
                            <span class="summary-value" id="summaryPhone">Not provided</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Email:</span>
                            <span class="summary-value" id="summaryEmail">Not provided</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Service:</span>
                            <span class="summary-value" id="summaryService">Not selected</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Staff:</span>
                            <span class="summary-value" id="summaryStaff">Not selected</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Date:</span>
                            <span class="summary-value" id="summaryDate">Not selected</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Time:</span>
                            <span class="summary-value" id="summaryTime">Not selected</span>
                        </div>

                        <div class="total-row">
                            <span class="total-label">Total:</span>
                            <span class="total-value" id="totalPrice">$0</span>
                        </div>

                        <button class="confirm-btn" id="confirmBtn" disabled>
                            Confirm Booking
                        </button>
                        <p class="confirmation-text">
                            You'll receive a confirmation call soon after booking.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </body>
    <script src="{{ asset('Frontend/js/booking.js') }}"></script>

    </html>
@endsection
