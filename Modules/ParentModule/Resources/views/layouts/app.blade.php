<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة أولياء الأمور')</title>
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    @stack('styles')
    
    <style>
        * {
            font-family: 'Cairo', sans-serif !important;
        }
        
        body {
            direction: rtl;
            text-align: right;
        }
        
        .parent-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .parent-sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 15px 25px;
            border-radius: 10px;
            margin: 8px 15px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .parent-sidebar .nav-link:hover,
        .parent-sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(-5px);
        }
        
        .parent-sidebar .nav-link i {
            margin-left: 10px;
            width: 20px;
            text-align: center;
        }
        
        .parent-header {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 20px 30px;
            border-bottom: 3px solid #667eea;
        }
        
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-right: 4px solid #667eea;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        
        .child-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .child-card:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        
        .btn {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 700;
            font-size: 18px;
        }
        
        .badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .table {
            font-size: 15px;
        }
        
        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 700;
            color: #667eea;
        }
        
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border: none;
        }
        
        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #667eea;
        }
        
        /* Impersonation Banner */
        .impersonation-banner {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 12px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1050;
            border-bottom: 3px solid #d63031;
        }
        
        .impersonation-banner .btn {
            background: rgba(255,255,255,0.3);
            border: 2px solid white;
            color: white;
            font-weight: 700;
            padding: 8px 20px;
        }
        
        .impersonation-banner .btn:hover {
            background: white;
            color: #f5576c;
        }
        
        /* RTL Specific */
        .text-left {
            text-align: right !important;
        }
        
        .text-right {
            text-align: left !important;
        }
        
        .mr-2, .mx-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }
        
        .ml-2, .mx-2 {
            margin-left: 0 !important;
            margin-right: 0.5rem !important;
        }
        
        .mr-3, .mx-3 {
            margin-right: 0 !important;
            margin-left: 1rem !important;
        }
        
        .ml-3, .mx-3 {
            margin-left: 0 !important;
            margin-right: 1rem !important;
        }
        
        .float-right {
            float: left !important;
        }
        
        .float-left {
            float: right !important;
        }
        
        .fa, .far, .fas {
            font-family: "Font Awesome 5 Free" !important;
        }
    </style>
</head>
<body>
    <!-- Impersonation Banner -->
    @if(session('is_impersonating', false))
    <div class="impersonation-banner">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-shield ml-2"></i>
                <strong>وضع التحكم:</strong>
                أنت الآن تتصفح كـ <strong>{{ auth()->user()->name }}</strong>
                <small class="mr-3" style="opacity: 0.9;">
                    (ولي الأمر: {{ session('impersonating_parent_name') }})
                </small>
            </div>
            <form method="POST" action="{{ route('parent.impersonation.return') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm">
                    <i class="fas fa-sign-out-alt ml-1"></i>
                    العودة إلى حساب ولي الأمر
                </button>
            </form>
        </div>
    </div>
    @endif
    
    <div class="d-flex">
        <!-- Sidebar -->
        @include('parentmodule::partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Header -->
            @include('parentmodule::partials.header')
            
            <!-- Page Content -->
            <main class="p-4" style="background: #f8f9fa; min-height: calc(100vh - 80px);">
                @include('parentmodule::partials.alerts')
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    
    <script>
        // Mark notification as read
        function markNotificationRead(id) {
            $.post("/parent/notifications/" + id + "/read", {
                _token: "{{ csrf_token() }}"
            }).done(function() {
                $('#notification-' + id).fadeOut();
                // Optionally reload to update notification count
                setTimeout(function() {
                    location.reload();
                }, 500);
            });
        }
    </script>
</body>
</html>