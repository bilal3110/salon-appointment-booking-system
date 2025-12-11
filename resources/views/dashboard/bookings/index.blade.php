@extends('dashboard.layout.main')
@section('main-section')

    <body>
        <div class="d-flex" id="wrapper">
            @include('dashboard.layout.side')
            <div id="page-content-wrapper">

                @include('dashboard.layout.nav')
                <div class="row">
                    <div class="col-12 p-5">
                        <div class="card card-custom p-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h4 class="mb-4">The Bookings</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#ID</th>
                                            <th scope="col">Client Name</th>
                                            <th scope="col">Client Phone</th>
                                            <th>Service Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Assigned Staff</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="booking-container">
                                        {{-- Services Data --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    @endsection
