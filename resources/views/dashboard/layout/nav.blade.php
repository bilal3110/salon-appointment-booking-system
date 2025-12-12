            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="btn btn-toggle" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div>
                        <h6 class="mb-0">
                            Welcome, {{ Auth::guard()->user()->name }}
                        </h3>
                    </div>
                </div>
            </nav>
