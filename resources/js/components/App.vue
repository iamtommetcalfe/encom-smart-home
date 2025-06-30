<template>
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
                            <router-link
                                to="/"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition"
                                :class="[$route.name === 'dashboard' ? 'border-primary-500 text-dark-900 dark:text-dark-100' : 'border-transparent text-dark-500 dark:text-dark-300 hover:text-dark-700 dark:hover:text-dark-200 hover:border-dark-300 dark:hover:border-dark-700']"
                            >
                                Dashboard
                            </router-link>
                            <router-link
                                to="/widgets"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition"
                                :class="[$route.name === 'widgets' ? 'border-primary-500 text-dark-900 dark:text-dark-100' : 'border-transparent text-dark-500 dark:text-dark-300 hover:text-dark-700 dark:hover:text-dark-200 hover:border-dark-300 dark:hover:border-dark-700']"
                            >
                                Widgets
                            </router-link>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center">
                        <button @click="toggleDarkMode" type="button" class="text-dark-500 dark:text-dark-400 hover:bg-dark-100 dark:hover:bg-dark-700 focus:outline-none focus:ring-4 focus:ring-dark-200 dark:focus:ring-dark-700 rounded-lg text-sm p-2.5">
                            <svg v-if="!isDarkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="toggleMobileMenu" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-dark-400 dark:text-dark-500 hover:text-dark-500 dark:hover:text-dark-400 hover:bg-dark-100 dark:hover:bg-dark-800 focus:outline-none focus:bg-dark-100 dark:focus:bg-dark-800 focus:text-dark-500 dark:focus:text-dark-400 transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div v-if="mobileMenuOpen" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <router-link
                        to="/"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition"
                        :class="[$route.name === 'dashboard' ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/20' : 'border-transparent text-dark-600 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700 hover:border-dark-300 dark:hover:border-dark-600']"
                        @click="mobileMenuOpen = false"
                    >
                        Dashboard
                    </router-link>
                    <router-link
                        to="/widgets"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition"
                        :class="[$route.name === 'widgets' ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/20' : 'border-transparent text-dark-600 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700 hover:border-dark-300 dark:hover:border-dark-600']"
                        @click="mobileMenuOpen = false"
                    >
                        Widgets
                    </router-link>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <router-view />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-dark-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-dark-500 dark:text-dark-400">
                    &copy; {{ new Date().getFullYear() }} Encom Smart Home. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

export default defineComponent({
    name: 'App',
    setup() {
        const route = useRoute();
        const isDarkMode = ref(false);
        const mobileMenuOpen = ref(false);

        const toggleDarkMode = () => {
            isDarkMode.value = !isDarkMode.value;
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }
        };

        const toggleMobileMenu = () => {
            mobileMenuOpen.value = !mobileMenuOpen.value;
        };

        onMounted(() => {
            // Check for dark mode preference
            if (
                localStorage.getItem('color-theme') === 'dark' ||
                (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {
                isDarkMode.value = true;
                document.documentElement.classList.add('dark');
            } else {
                isDarkMode.value = false;
                document.documentElement.classList.remove('dark');
            }
        });

        return {
            route,
            isDarkMode,
            mobileMenuOpen,
            toggleDarkMode,
            toggleMobileMenu
        };
    }
});
</script>
