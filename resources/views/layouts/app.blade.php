<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - School Management System</title>
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    
    {{-- Google Fonts - Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Custom Sidebar Styling --}}
    <style>
        /* Override font family untuk seluruh aplikasi */
        body, .sidebar-wrapper, .sidebar-menu, .menu, .sidebar-link, .submenu-link {
            font-family: 'Poppins', sans-serif !important;
        }

        /* Sidebar background color - White */
        #sidebar .sidebar-wrapper {
            background: #ffffff !important;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        /* Sidebar header styling */
        #sidebar .sidebar-header {
            background: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Sidebar title */
        #sidebar .sidebar-title {
            color: #607080 !important;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Menu item default */
        #sidebar .sidebar-link {
            color: #25396f !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #sidebar .sidebar-link:hover {
            background: #f0f2f5 !important;
            color: #435ebe !important;
            padding-left: 1.2rem;
        }

        /* Active menu item */
        #sidebar .sidebar-item.active > .sidebar-link {
            background: #435ebe !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(67, 94, 190, 0.3);
        }

        /* Submenu styling */
        #sidebar .submenu {
            background: transparent !important;
        }

        #sidebar .submenu-link {
            color: #25396f !important;
            font-weight: 400;
        }

        #sidebar .submenu-link:hover {
            color: #435ebe !important;
            background: transparent !important;
            margin-left: 5px;
        }

        #sidebar .submenu-item.active .submenu-link {
            color: #435ebe !important;
            font-weight: 600;
        }

        /* Icon color */
        #sidebar .sidebar-link i,
        #sidebar .submenu-link i {
            color: inherit;
        }

        /* Theme toggle styling */
        #sidebar .theme-toggle svg {
            color: #607080 !important;
        }

        /* Sidebar toggler */
        #sidebar .sidebar-toggler a {
            color: #607080 !important;
        }

        /* Logo area - no filter needed for colored favicon */
        #sidebar .logo img {
            filter: none;
            height: 50px;
        }

        /* Scrollbar styling untuk sidebar */
        #sidebar .sidebar-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar .sidebar-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        #sidebar .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        /* Dark mode adjustments - keep dark sidebar for dark mode */
        body[data-bs-theme="dark"] #sidebar .sidebar-wrapper {
            background: #1b1b29 !important;
            box-shadow: none;
        }
        
        body[data-bs-theme="dark"] #sidebar .sidebar-header {
            background: #1b1b29 !important;
        }
        
        body[data-bs-theme="dark"] #sidebar .sidebar-link {
            color: #c2c7d0 !important;
        }
        
        body[data-bs-theme="dark"] #sidebar .sidebar-title {
            color: #c2c7d0 !important;
        }

        body[data-bs-theme="dark"] #sidebar .sidebar-item.active > .sidebar-link {
            background: #435ebe !important;
            color: #ffffff !important;
        }
        
        body[data-bs-theme="dark"] #sidebar .sidebar-link:hover {
            background: #2b2b3f !important;
            color: #ffffff !important;
        }
    </style>
    
    @stack('styles')
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        @include('layouts.partials.sidebar')
        <div id="main">
            @include('layouts.partials.header')
            @yield('content')
            @include('layouts.partials.footer')
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
    {{-- TAMBAHKAN SCRIPT ALPINE.JS DI SINI --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')



    <script>
        window.onerror = function(message, source, lineno, colno, error) {
            if (message.includes('getBoundingClientRect')) {
                return true; // Mengabaikan error tersebut
            }
            return false; // Biarkan error lain tetap muncul
        };
    </script>
</body>

</html>
