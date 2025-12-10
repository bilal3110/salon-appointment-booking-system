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
                                <h4 class="mb-4">Add New Staff Here</h4>
                            </div>
                            <div class="table-responsive">
                                <form action="" id="create-staff-form" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Staff</label>
                                        <select name="" id="user-id" class="form-select">
                                            <option value="">Select Staff</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Services</label>
                                        @foreach ($services as $service)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="{{ $service->id }}">
                                                <label class="form-label">{{ $service->service_name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="form-label">Photo</label>
                                        <input type="file" class="form-input" name="photo" id="photo">
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="form-label">Bio</label>
                                        <textarea name="bio" id="bio" cols="30" rows="5" class="form-input"></textarea>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Add Staff</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    @endsection
