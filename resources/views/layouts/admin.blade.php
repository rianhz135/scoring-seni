<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <title>{{ $title ?? 'Admin Panel' }} — DS SMI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=block" rel="stylesheet">

    <style>
        body { margin: 0; padding: 0; }
        html { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Sidebar width dikontrol penuh lewat CSS, bukan Tailwind */
        #sidebar { transition: width 0.3s ease; }
        #sidebar.collapsed { width: 64px !important; }

        /* Sembunyikan teks saat collapsed */
        #sidebar.collapsed .sidebar-link-text,
        #sidebar.collapsed .sidebar-group-text,
        #sidebar.collapsed .sidebar-chevron,
        #sidebar.collapsed .sidebar-section-label,
        #sidebar.collapsed #sidebar-brand-text { display: none !important; }

        /* Sembunyikan submenu */
        #sidebar.collapsed .nav-sub-list { max-height: 0 !important; display: none !important; }

        /* Center icon saat collapsed */
        #sidebar.collapsed .sidebar-link,
        #sidebar.collapsed .sidebar-group-btn {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
        }

        /* Topbar & main ikut geser — pakai !important agar kalahkan Tailwind */
        #topbar { transition: left 0.3s ease; left: 240px !important; }
        #topbar.collapsed { left: 64px !important; }

        #main-wrapper { transition: margin-left 0.3s ease; margin-left: 240px !important; }
        #main-wrapper.collapsed { margin-left: 64px !important; }

        /* Dropdown */
        .nav-sub-list { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .nav-sub-list.open { max-height: 400px; }

        /* Chevron */
        .chevron { transition: transform 0.25s ease; }
        .chevron.open { transform: rotate(90deg); }

        /* Active link */
        .sidebar-link.active {
            background-color: #f0f4ff;
            color: #4254e8;
            border-left-color: #4254e8;
        }
    </style>

    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff', 100: '#dde6ff', 200: '#c4d0ff',
                            400: '#7f93f8', 500: '#5a6ef5', 600: '#4254e8',
                            700: '#3341cc', 800: '#2a34a4', 900: '#1e2578'
                        },
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                },
            },
        }
    </script> --}}
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <div class="flex min-h-screen">

        <x-admin.sidebar />

        <div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">
            <x-admin.topbar />

            <main class="flex-1 p-7 pt-20">
                @yield('content')
            </main>

            <footer class="bg-white p-4 border-t text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Admin Panel - Sistem Pertandingan
            </footer>
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'sidebar_collapsed';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('main-wrapper');
            const topbar  = document.getElementById('topbar');
            const isCollapsed = sidebar.classList.toggle('collapsed');
            wrapper.classList.toggle('collapsed', isCollapsed);
            topbar.classList.toggle('collapsed', isCollapsed);
            localStorage.setItem(STORAGE_KEY, isCollapsed ? '1' : '0');
        }

        // Restore state sebelum render agar tidak flash
        (function () {
            if (localStorage.getItem(STORAGE_KEY) === '1') {
                ['sidebar', 'main-wrapper', 'topbar'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.classList.add('collapsed');
                });
            }
        })();

        function toggleSection(id) {
            if (document.getElementById('sidebar').classList.contains('collapsed')) return;
            var subs    = document.getElementById('subs-' + id);
            var chevron = document.getElementById('chevron-' + id);
            if (subs)    subs.classList.toggle('open');
            if (chevron) chevron.classList.toggle('open');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 3000, timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            @if (session('success'))
                Toast.fire({ icon: 'success', title: "{!! session('success') !!}" });
            @endif
            @if (session('error'))
                Toast.fire({ icon: 'error', title: "{!! session('error') !!}" });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>