<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Dewan — DS SMI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff', 100: '#dde6ff', 200: '#c4d0ff', 400: '#7f93f8',
                            500: '#5a6ef5', 600: '#4254e8', 700: '#3341cc', 800: '#2a34a4', 900: '#1e2578'
                        },
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 antialiased">
    <div class="min-h-screen p-4 md:p-2 lg:p-4">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>