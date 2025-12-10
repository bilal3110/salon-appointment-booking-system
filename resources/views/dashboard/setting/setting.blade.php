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
                                <h4 class="mb-4">Settings</h4>
                                {{-- <a href="Add User" class="btn btn-primary">Add New Service</a> --}}
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    @endsection
