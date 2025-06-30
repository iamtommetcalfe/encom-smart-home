<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Encom') }} - Smart Home Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="h-full bg-dark-50 dark:bg-dark-900 text-dark-900 dark:text-dark-50 antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white dark:bg-dark-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-primary-600 dark:text-primary-400 text-2xl font-bold">Encom</span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-primary-500 text-sm font-medium leading-5 text-dark-900 dark:text-dark-100 focus:outline-none focus:border-primary-700 transition">
                                Dashboard
                            </a>
                            <a href="{{ route('widgets.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-dark-500 dark:text-dark-300 hover:text-dark-700 dark:hover:text-dark-200 hover:border-dark-300 dark:hover:border-dark-700 focus:outline-none focus:text-dark-700 dark:focus:text-dark-200 focus:border-dark-300 dark:focus:border-dark-700 transition">
                                Widgets
                            </a>
                            <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-dark-500 dark:text-dark-300 hover:text-dark-700 dark:hover:text-dark-200 hover:border-dark-300 dark:hover:border-dark-700 focus:outline-none focus:text-dark-700 dark:focus:text-dark-200 focus:border-dark-300 dark:focus:border-dark-700 transition">
                                Settings
                            </a>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center">
                        <button id="theme-toggle" type="button" class="text-dark-500 dark:text-dark-400 hover:bg-dark-100 dark:hover:bg-dark-700 focus:outline-none focus:ring-4 focus:ring-dark-200 dark:focus:ring-dark-700 rounded-lg text-sm p-2.5">
                            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-dark-400 dark:text-dark-500 hover:text-dark-500 dark:hover:text-dark-400 hover:bg-dark-100 dark:hover:bg-dark-800 focus:outline-none focus:bg-dark-100 dark:focus:bg-dark-800 focus:text-dark-500 dark:focus:text-dark-400 transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path id="mobile-menu-icon-open" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path id="mobile-menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 border-primary-500 text-base font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/20 focus:outline-none focus:text-primary-800 dark:focus:text-primary-200 focus:bg-primary-100 dark:focus:bg-primary-900 focus:border-primary-700 dark:focus:border-primary-300 transition">
                        Dashboard
                    </a>
                    <a href="{{ route('widgets.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-dark-600 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700 hover:border-dark-300 dark:hover:border-dark-600 focus:outline-none focus:text-dark-800 dark:focus:text-dark-200 focus:bg-dark-50 dark:focus:bg-dark-700 focus:border-dark-300 dark:focus:border-dark-600 transition">
                        Widgets
                    </a>
                    <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-dark-600 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700 hover:border-dark-300 dark:hover:border-dark-600 focus:outline-none focus:text-dark-800 dark:focus:text-dark-200 focus:bg-dark-50 dark:focus:bg-dark-700 focus:border-dark-300 dark:focus:border-dark-600 transition">
                        Settings
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-dark-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-dark-500 dark:text-dark-400">
                    &copy; {{ date('Y') }} Encom Smart Home. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <!-- Dark Mode Script -->
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.getElementById('theme-toggle-dark-icon').classList.add('hidden');
            document.getElementById('theme-toggle-light-icon').classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            document.getElementById('theme-toggle-light-icon').classList.add('hidden');
            document.getElementById('theme-toggle-dark-icon').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuIconOpen = document.getElementById('mobile-menu-icon-open');
            const mobileMenuIconClose = document.getElementById('mobile-menu-icon-close');

            // Toggle dark/light mode
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    // Toggle icons
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    // If set via local storage previously
                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    } else {
                        // If not set via local storage previously
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            }

            // Toggle mobile menu
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenuIconOpen.classList.toggle('hidden');
                    mobileMenuIconOpen.classList.toggle('inline-flex');
                    mobileMenuIconClose.classList.toggle('hidden');
                    mobileMenuIconClose.classList.toggle('inline-flex');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
