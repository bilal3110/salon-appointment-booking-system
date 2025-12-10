@extends('dashboard.layout.main')
@section('main-section')

    <body>
        <div class="d-flex" id="wrapper">
            @include('dashboard.layout.side')
            <div id="page-content-wrapper">

                @include('dashboard.layout.nav')
                <div class="row">
                    <div class="col-12 p-5 d-flex justify-content-center align-items-center">
                        <div id="staff-details" data-staff-id="{{ $id }}">
                            <div class="staff-card">
                                <div class="staff-card-header">
                                    <div class="staff-photo-container">
                                        <img src="" alt="Staff Photo">
                                    </div>
                                    <h2 class="staff-name">Loading...</h2>
                                    <p class="staff-bio">Loading...</p>
                                </div>
                                <div class="staff-card-body">
                                    <div class="staff-info-section">
                                        <h3 class="staff-section-title">Contact Information</h3>
                                        <div class="staff-info-item">
                                            <svg class="staff-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span class="staff-info-text">Loading...</span>
                                        </div>
                                        <div class="staff-info-item">
                                            <svg class="staff-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span class="staff-info-text">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="staff-info-section">
                                        <h3 class="staff-section-title">Services</h3>
                                        <div class="staff-services-list">
                                            <p class="staff-info-text">Loading...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
