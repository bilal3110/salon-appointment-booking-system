        <div id="sidebar-wrapper">
            <div class="sidebar-heading logo">
                <img src="{{asset('Frontend/assets/logo.png')}}" alt="">
            </div>
            <div class="list-group list-group-flush">
                <a href="{{route('dashboard.index')}}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{route('dashboard.staff')}}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i> Staff
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line me-2"></i> Bookings
                </a>
                <a href="{{route('dashboard.services')}}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i> Services
                </a>
                <a href="{{route('dashboard.users')}}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i>
                    Users
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
                <a href="#" class="list-group-item list-group-item-action mt-auto">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
