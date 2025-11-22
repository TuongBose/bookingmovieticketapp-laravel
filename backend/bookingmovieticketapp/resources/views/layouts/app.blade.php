<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cinestar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/CineJoy.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
       .content-bg-cinestar {
        background: linear-gradient(135deg, 
            #0a0e1a 0%, 
            #1a1f2e 30%, 
            #2a1f3e 50%, 
            #8b58a3 55%,   
            #1a1f2e 60%, 
            #0a0e1a 100%
        ) !important;
        min-height: 80vh;
        color: #fff;
        
    }
    </style>
    @yield('styles')
    @stack('styles')


</head>


<body>
    @include('partials.header')

    <main class="content-bg-cinestar">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
