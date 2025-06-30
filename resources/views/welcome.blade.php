@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Dashboard Header -->
        <div class="col-span-1 md:col-span-2 lg:col-span-3">
            <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-semibold text-dark-900 dark:text-dark-100">Welcome to Encom Smart Home</h1>
                    <p class="mt-2 text-dark-600 dark:text-dark-400">Your personal smart home dashboard. Add widgets to monitor and control your home.</p>
                </div>
            </div>
        </div>

        <!-- Weather Widget -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
            <div class="p-4 bg-primary-500 dark:bg-primary-700">
                <h2 class="text-lg font-semibold text-white">Weather in {{ $currentWeather['location'] ?? 'Austrey, UK' }}</h2>
            </div>
            <div class="p-6">
                @if(isset($currentWeather['error']))
                    <div class="text-danger-500 text-center">
                        {{ $currentWeather['error'] }}
                    </div>
                @elseif(isset($currentWeather['current']))
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-4xl font-bold text-dark-900 dark:text-dark-100">
                                {{ $currentWeather['current']['temperature'] ?? '?' }}{{ $currentWeather['current']['temperature_unit'] ?? '°C' }}
                            </p>
                            <p class="text-dark-600 dark:text-dark-400">
                                {{ $currentWeather['current']['weather_description'] ?? 'Unknown' }}
                            </p>
                        </div>
                        <div>
                            @php
                                $weatherCode = $currentWeather['current']['weather_code'] ?? 0;
                                $iconPath = '';

                                // Map weather code to icon
                                if ($weatherCode === 0) {
                                    $iconPath = 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
                                } elseif (in_array($weatherCode, [1, 2, 3])) {
                                    $iconPath = 'M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z';
                                } elseif (in_array($weatherCode, [45, 48])) {
                                    $iconPath = 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z';
                                } elseif (in_array($weatherCode, [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82])) {
                                    $iconPath = 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12';
                                } elseif (in_array($weatherCode, [71, 73, 75, 77, 85, 86])) {
                                    $iconPath = 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z M10 14l2-2m0 0l2 2m-2-2v7';
                                } elseif (in_array($weatherCode, [95, 96, 99])) {
                                    $iconPath = 'M13 10V3L4 14h7v7l9-11h-7z';
                                } else {
                                    $iconPath = 'M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z';
                                }
                            @endphp
                            <svg class="w-16 h-16 text-primary-500 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                            </svg>
                        </div>
                    </div>

                    @if(isset($forecastWeather['forecast']) && count($forecastWeather['forecast']) > 0)
                        <div class="mt-4 grid grid-cols-4 gap-2">
                            @foreach(array_slice($forecastWeather['forecast'], 0, 4) as $day)
                                <div class="text-center">
                                    <p class="text-dark-500 dark:text-dark-400 text-xs">
                                        {{ \Carbon\Carbon::parse($day['date'])->format('D') }}
                                    </p>
                                    <p class="text-dark-900 dark:text-dark-100 text-sm font-medium">
                                        {{ round($day['max_temperature']) }}°C
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-dark-500 dark:text-dark-400 text-center">
                        Weather data not available
                    </div>
                @endif
            </div>
        </div>

        <!-- Bin Collection Widget -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
            <div class="p-4 bg-secondary-500 dark:bg-secondary-700">
                <h2 class="text-lg font-semibold text-white">Bin Collection</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($binCollections as $collection)
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full mr-3
                                @if($collection['color'] == 'green')
                                    bg-success-500
                                @elseif($collection['color'] == 'black')
                                    bg-dark-800
                                @elseif($collection['color'] == 'brown')
                                    bg-warning-500
                                @elseif($collection['color'] == 'blue')
                                    bg-primary-500
                                @elseif($collection['color'] == 'red')
                                    bg-danger-500
                                @else
                                    bg-dark-300
                                @endif
                            "></div>
                            <div>
                                <p class="text-dark-900 dark:text-dark-100 font-medium">
                                    {{ ucfirst($collection['bin_type']) }}
                                </p>
                                <p class="text-dark-500 dark:text-dark-400 text-sm">
                                    {{ $collection['days_until_human'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-dark-500 dark:text-dark-400 text-center p-4">
                            No upcoming bin collections found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Plant Watering Widget Placeholder -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
            <div class="p-4 bg-success-500 dark:bg-success-700">
                <h2 class="text-lg font-semibold text-white">Plant Watering</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-danger-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                            </svg>
                            <p class="text-dark-900 dark:text-dark-100 font-medium">Fiddle Leaf Fig</p>
                        </div>
                        <p class="text-danger-500 text-sm font-medium">Overdue 2 days</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-warning-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                            </svg>
                            <p class="text-dark-900 dark:text-dark-100 font-medium">Snake Plant</p>
                        </div>
                        <p class="text-warning-500 text-sm font-medium">Today</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-success-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                            </svg>
                            <p class="text-dark-900 dark:text-dark-100 font-medium">Monstera</p>
                        </div>
                        <p class="text-success-500 text-sm font-medium">In 3 days</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Widget Button -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg border-2 border-dashed border-dark-300 dark:border-dark-600">
            <div class="p-6 flex items-center justify-center">
                <a href="{{ route('widgets.create') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-lg font-medium">Add Widget</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Vue.js Mount Point -->
    <div id="app" class="hidden"></div>
@endsection
