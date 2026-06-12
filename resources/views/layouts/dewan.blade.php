<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Dewan — DS SMI</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    <link rel="preload" href="/fonts/plus-jakarta-sans/static/PlusJakartaSans-Regular.woff2" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/plus-jakarta-sans/static/PlusJakartaSans-Medium.woff2" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/plus-jakarta-sans/static/PlusJakartaSans-SemiBold.woff2" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/plus-jakarta-sans/static/PlusJakartaSans-Bold.woff2" as="font"
        type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    
</head>

<body class="bg-gray-100 antialiased">
    <div class="min-h-screen p-4 md:p-2 lg:p-4">
        @yield('content')
    </div>
    @stack('scripts')
</body>

</html>
