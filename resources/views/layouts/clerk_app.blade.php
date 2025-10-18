<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JJ Flower Shop - Clerk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/clerk-styles.css') }}">
    @stack('styles')
</head>
<body class="antialiased">
    <div id="app">
        <!-- Clerk Navbar (matches the desired UI) -->
        <nav class="clerk-navbar-bg">
            <div class="clerk-navbar-content">
                <div class="clerk-logo-title">
                    <img src="/images/logo.png" alt="JJ Flower Shop" class="clerk-logo-img">
                    <div class="clerk-shop-title">J ' J FLOWER<br><span>SHOP <span class="fs-6">Est. 2023</span></span></div>
                </div>
                <div class="clerk-user dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" id="clerkProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
                        @else
                            <i class="bi bi-person-circle text-white"></i>
                        @endif
                        {{ Auth::user()->name ?? 'CLERK' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="clerkProfileDropdown">
                        <li><a class="dropdown-item" href="{{ route('clerk.profile.edit') }}">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="clerk-navbar-divider">
            <div class="clerk-navbar-links">
                <a href="{{ route('clerk.product_catalog.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.product_catalog.*')) active @endif">
                    <i class="bi bi-grid"></i>
                    <span>Product catalog</span>
                </a>
                <a href="{{ route('clerk.customize.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.customize.*')) active @endif">
                    <i class="bi bi-brush"></i>
                    <span>Customize</span>
                </a>
                <a href="{{ route('clerk.clerk.inventory.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.clerk.inventory.*')) active @endif">
                    <i class="bi bi-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('clerk.orders.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.orders.*')) active @endif">
                    <i class="bi bi-cart"></i>
                    <span>Sales Orders</span>
                </a>
                <a href="{{ route('clerk.loyalty.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.loyalty.*')) active @endif">
                    <i class="bi bi-gift"></i>
                    <span>Loyalty Cards</span>
                </a>
                <a href="#" class="clerk-navbar-link">
                    <i class="bi bi-chat"></i>
                    <span>Chat</span>
                </a>
            </div>
        </nav>
        
        @if(!(request()->routeIs('clerk.product_catalog.*') || request()->routeIs('clerk.customize.*') || request()->routeIs('clerk.orders.*') || request()->routeIs('clerk.clerk.inventory.*') || request()->routeIs('clerk.inventory.*')))
        <div class="d-flex" id="wrapper">
            <!-- Clerk Sidebar -->
            <div class="sidebar-container sidebar-clean d-flex flex-column align-items-center" style="background: #F6FBF4; min-width: 220px; max-width: 260px; height: 100vh; padding-top: 48px;">
                <div class="sidebar-profile text-center mb-4">
                    <div class="sidebar-profile-icon mx-auto mb-2">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #4CAF50;">
                        @else
                            <i class="bi bi-person-circle" style="font-size: 3.5rem; color: #888;"></i>
                        @endif
                    </div>
                    <div class="sidebar-profile-label" style="font-size: 1.1rem; color: #222; font-weight: 500;">{{ auth()->user()->name ?? 'Clerk' }}</div>
                </div>
                <nav class="w-100">
                    <ul class="nav flex-column align-items-center align-items-md-start w-100">
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.dashboard') }}" class="sidebar-link @if(request()->routeIs('clerk.dashboard')) active @endif">Dashboard</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.invoices.index') }}" class="sidebar-link @if(request()->routeIs('clerk.invoices.*')) active @endif">Invoices</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.profile.edit') }}" class="sidebar-link @if(request()->routeIs('clerk.profile.*')) active @endif">Edit Profile</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.notifications.index') }}" class="sidebar-link @if(request()->routeIs('clerk.notifications.*')) active @endif">Notifications</a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div id="page-content-wrapper" class="flex-grow-1">
                @yield('content')
            </div>
        </div>
        @else
        <!-- Main Content -->
        <div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
            @yield('content')
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert function with OK button and auto-dismiss
        function showSweetAlertWithCheckbox(title, message, icon = 'success', timer = 3000) {
            return Swal.fire({
                title: title,
                text: message,
                icon: icon,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4CAF50',
                timer: timer,
                timerProgressBar: true,
                allowOutsideClick: true,
                didOpen: () => {
                    // Auto-dismiss after specified time
                    setTimeout(() => {
                        Swal.close();
                    }, timer);
                }
            });
        }

        // Show success message on page load if exists
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSweetAlertWithCheckbox('Success!', '{{ session('success') }}', 'success', 3000);
            @endif
        });
    </script>
    <!-- Auto Capitalization Script -->
    <script src="{{ asset('js/auto-capitalization.js') }}"></script>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    if (alert.classList.contains('show')) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                });
            }, 2000);
        });
    </script>
</body>
</html> 