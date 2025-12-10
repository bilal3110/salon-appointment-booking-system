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
                                <h4 class="mb-4">Edit Services Here</h4>
                            </div>
                            <div class="table-responsive">
                                <form id="edit-service-form" data-service-id="{{ $id }}">
                                    <div class="mb-3">
                                        <label for="service-name" class="form-label">Service Name</label>
                                        <input type="text" class="form-input" id="service-name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="service-price" class="form-label">Service Price</label>
                                        <input type="number" class="form-input" id="service-price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="service-type" class="form-label">Service Type</label>
                                        <select class="form-select" id="service-type" required>
                                            <option value="" selected disabled>Select Type</option>
                                            <option value="men">Men</option>
                                            <option value="women">Women</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="service-type" class="form-label">Description</label>
                                        <textarea class="form-input" id="service-description" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Service</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <script>
            const serviceId = "{{ $id }}";
            (async () => {
                try {
                    const response = await fetch(`/api/services/${serviceId}`);
                    if (!response.ok) throw new Error("Failed to fetch service");
                    const service = await response.json();
                    document.getElementById("service-name").value = service.service_name;
                    document.getElementById("service-price").value = service.price;
                    document.getElementById("service-type").value = service.service_type;
                    document.getElementById("service-description").value = service.description;
                } catch (error) {
                    console.error("Error loading service data:", error);
                }
            })();
        </script>
    @endsection
