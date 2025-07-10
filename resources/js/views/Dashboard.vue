<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Dashboard Header -->
      <div class="col-span-1 md:col-span-2 lg:col-span-3">
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-semibold text-dark-900 dark:text-dark-100">Welcome to Encom Home Dashboard</h1>
            <p class="mt-2 text-dark-600 dark:text-dark-400">Your personal home dashboard.</p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="dashboardStore.loading" class="col-span-1 md:col-span-2 lg:col-span-3 flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="dashboardStore.error" class="col-span-1 md:col-span-2 lg:col-span-3 bg-danger-50 dark:bg-danger-900/20 p-6 rounded-lg text-danger-700 dark:text-danger-300">
        <p>{{ dashboardStore.error }}</p>
        <button @click="fetchData" class="mt-4 px-4 py-2 bg-danger-100 dark:bg-danger-800 rounded-md hover:bg-danger-200 dark:hover:bg-danger-700 transition">
          Retry
        </button>
      </div>

      <template v-else>
        <!-- Smart device functionality has been removed -->

        <!-- Weather Widget -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
          <div class="p-4 bg-primary-500 dark:bg-primary-700">
            <h2 class="text-lg font-semibold text-white">Weather in {{ dashboardStore.currentWeather?.location || 'Unknown Location' }}</h2>
          </div>
          <div class="p-6">
            <div v-if="dashboardStore.currentWeather?.current" class="flex items-center justify-between">
              <div>
                <p class="text-4xl font-bold text-dark-900 dark:text-dark-100">
                  {{ dashboardStore.currentWeather.current.temperature }}{{ dashboardStore.currentWeather.current.temperature_unit }}
                </p>
                <p class="text-dark-600 dark:text-dark-400">
                  {{ dashboardStore.currentWeather.current.weather_description }}
                </p>
              </div>
              <div>
                <svg class="w-16 h-16 text-primary-500 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getWeatherIcon(dashboardStore.currentWeather.current.weather_code)"></path>
                </svg>
              </div>
            </div>

            <div v-if="dashboardStore.forecastWeather?.forecast && dashboardStore.forecastWeather.forecast.length > 0" class="mt-4 grid grid-cols-4 gap-2">
              <div v-for="(day, index) in dashboardStore.forecastWeather.forecast.slice(0, 4)" :key="index" class="text-center">
                <p class="text-dark-500 dark:text-dark-400 text-xs">
                  {{ formatDay(day.date) }}
                </p>
                <p class="text-dark-900 dark:text-dark-100 text-sm font-medium">
                  {{ Math.round(day.max_temperature) }}°C
                </p>
              </div>
            </div>

            <div v-else class="text-dark-500 dark:text-dark-400 text-center">
              Weather data not available
            </div>
          </div>
        </div>

        <!-- Bin Collection Widget -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
          <div class="p-4 bg-secondary-500 dark:bg-secondary-700">
            <h2 class="text-lg font-semibold text-white">Bin Collection</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="collection in dashboardStore.binCollections" :key="collection.id" class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-3" :class="getBinColorClass(collection.color)"></div>
                <div>
                  <p class="text-dark-900 dark:text-dark-100 font-medium">
                    {{ formatBinType(collection.bin_type) }}
                  </p>
                  <p class="text-dark-500 dark:text-dark-400 text-sm">
                    {{ collection.days_until_human }}
                  </p>
                </div>
              </div>

              <div v-if="dashboardStore.binCollections.length === 0" class="text-dark-500 dark:text-dark-400 text-center p-4">
                No upcoming bin collections found.
              </div>
            </div>
          </div>
        </div>

        <!-- Plant Watering Widget -->
        <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
          <div class="p-4 bg-success-500 dark:bg-success-700">
            <h2 class="text-lg font-semibold text-white">Plant Watering</h2>
          </div>
          <div class="p-6">
            <PlantWateringWidget />
          </div>
        </div>

      </template>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted } from 'vue';
import { useDashboardStore } from '../stores';
import PlantWateringWidget from '../widgets/PlantWateringWidget.vue';
// Smart device functionality has been removed

export default defineComponent({
  name: 'Dashboard',
  components: {
    PlantWateringWidget
    // Smart device functionality has been removed
  },

  setup() {
    const dashboardStore = useDashboardStore();

    const fetchData = () => {
      dashboardStore.fetchDashboardData();
    };

    const formatDay = (dateString: string): string => {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { weekday: 'short' });
    };

    const getWeatherIcon = (weatherCode: number | undefined): string => {
      if (weatherCode === undefined) return 'M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z';

      // Map weather code to icon path
      if (weatherCode === 0) {
        // Clear sky
        return 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
      } else if ([1, 2, 3].includes(weatherCode)) {
        // Partly cloudy
        return 'M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z';
      } else if ([45, 48].includes(weatherCode)) {
        // Fog
        return 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z';
      } else if ([51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82].includes(weatherCode)) {
        // Rain
        return 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12';
      } else if ([71, 73, 75, 77, 85, 86].includes(weatherCode)) {
        // Snow
        return 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z M10 14l2-2m0 0l2 2m-2-2v7';
      } else if ([95, 96, 99].includes(weatherCode)) {
        // Thunderstorm
        return 'M13 10V3L4 14h7v7l9-11h-7z';
      }

      // Default cloud icon
      return 'M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z';
    };

    const getBinColorClass = (color: string): string => {
      switch (color?.toLowerCase()) {
        case 'green':
          return 'bg-success-500';
        case 'black':
          return 'bg-dark-800';
        case 'brown':
          return 'bg-warning-500';
        case 'blue':
          return 'bg-primary-500';
        case 'red':
          return 'bg-danger-500';
        default:
          return 'bg-dark-300';
      }
    };

    const formatBinType = (binType: string): string => {
      // Capitalize first letter of each word
      return binType
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    };

    onMounted(() => {
      fetchData();
    });

    return {
      dashboardStore,
      fetchData,
      formatDay,
      getWeatherIcon,
      getBinColorClass,
      formatBinType
    };
  }
});
</script>
