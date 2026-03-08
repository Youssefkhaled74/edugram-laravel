<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة أولياء الأمور') - {{ config('app.name', 'نظام إدارة التعلم') }}</title>
    
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
        .fa, .far, .fas {
    font-family: "Font Awesome 5 Free" !important;
}
        body {
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            font-weight: 700;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .text-primary {
            color: #667eea !important;
        }
        
        a {
            transition: all 0.2s ease;
        }
        
        a:hover {
            text-decoration: none;
            opacity: 0.8;
        }
        
        /* RTL Fixes */
        .mr-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }
        
        .ml-2 {
            margin-left: 0 !important;
            margin-right: 0.5rem !important;
        }
        
        .ml-1 {
            margin-left: 0 !important;
            margin-right: 0.25rem !important;
        }
    </style>
</head>
<body>
	    <!-- شريط التمثيل -->

    <div class="container">
        @yield('content')
    </div>
    
    <!-- السكريبتات -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>