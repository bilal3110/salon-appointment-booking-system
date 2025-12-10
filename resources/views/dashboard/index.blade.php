@extends('dashboard.layout.main')
@section('main-section')

    <body>
        <div class="d-flex" id="wrapper">
            @include('dashboard.layout.side')
            <div id="page-content-wrapper">

                @include('dashboard.layout.nav')

                <div class="container-fluid px-4 py-4">

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card card-custom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted-custom">Total Services</p>
                                        <h3 class="mb-0">10</h3>
                                    </div>
                                    <i class="fas fa-box me-2 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-custom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted-custom">Total Staff</p>
                                        <h3 class="mb-0">4</h3>
                                    </div>
                                    <i class="fas fa-user-plus stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-custom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted-custom">Pending Bookings</p>
                                        <h3 class="mb-0">56</h3>
                                    </div>
                                    <i class="fas fa-chart-line me-2 stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="bookings-container">
                        <!-- Booking cards will be dynamically inserted here -->
                    </div>

                </div>
            </div>
        </div>
    @endsection
