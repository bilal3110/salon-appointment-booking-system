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
                                <h4 class="mb-4">Edit User Here</h4>
                            </div>
                            <div class="table-responsive">
                                <form id="edit-user-form" data-user-id ="{{ $id }}">
                                    <div class="mb-3">
                                        <label for="user-name" class="form-label">User Name</label>
                                        <input type="text" class="form-input" id="user-name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-role" class="form-label">User Role</label>
                                        <select class="form-select" id="user-type" required>
                                            <option value="" selected disabled>Select Role</option>
                                            <option value="admin">Admin</option>
                                            <option value="staff">Staff</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-phone" class="form-label">User Phone</label>
                                        <input type="text" class="form-input" id="user-phone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-email" class="form-label">User Email</label>
                                        <input type="email" class="form-input" id="user-email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-password" class="form-label">User Password</label>
                                        <input type="password" class="form-input" id="user-password" >
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-password-confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-input" id="password_confirmation"
                                            name="password_confirmation" >
                                    </div>
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <script>
            const userId = "{{ $id }}";
            (async () => {
                try {
                    const response = await fetch(`/api/users/${userId}`);
                    if (!response.ok) throw new Error("Failed to fetch user");
                    const user = await response.json();
                    document.getElementById("user-name").value = user.name;
                    document.getElementById("user-type").value = user.type;
                    document.getElementById("user-phone").value = user.phone;
                    document.getElementById("user-email").value = user.email;
                } catch (error) {
                    console.error("Error loading user data:", error);
                }
            })();
        </script>
    @endsection
