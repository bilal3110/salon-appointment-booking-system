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
                                <h4 class="mb-4">Add Staff Here</h4>
                                <a href="{{route('dashboard.staff.create')}}" class="btn btn-primary">Add New Staff</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staff-container">
                                        {{-- Users Data --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
