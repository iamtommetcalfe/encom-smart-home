<template>
  <div class="plant-watering-widget">
    <div v-if="loading" class="flex justify-center items-center p-6">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
    </div>

      <div v-else-if="wateringStatus === 'error'" class="p-6 text-danger-500">
          <p>Unable to determine watering recommendation due to missing weather data.</p>
    </div>

    <div v-else class="space-y-4">

      <div class="bg-dark-50 dark:bg-dark-700 p-4 rounded-lg">
        <div class="flex items-center mb-2">
          <svg class="w-5 h-5 mr-2" :class="getStatusColor(wateringStatus)" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path v-if="wateringStatus === 'wait'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            <path v-else-if="wateringStatus === 'water'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            <path v-else fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          <p class="font-medium" :class="getStatusColor(wateringStatus)">{{ getStatusText(wateringStatus) }}</p>
        </div>
        <p class="text-dark-600 dark:text-dark-400 text-sm">{{ wateringRecommendation }}</p>
      </div>

      <div class="mt-4">
        <h3 class="text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">Weather Forecast</h3>
        <div class="grid grid-cols-3 gap-2">
          <div v-for="(day, index) in forecastDays" :key="index" class="bg-dark-50 dark:bg-dark-700 p-2 rounded text-center">
            <p class="text-xs text-dark-500 dark:text-dark-400">{{ day.day }}</p>
            <p class="text-sm font-medium text-dark-900 dark:text-dark-100">{{ day.temp }}°C</p>
            <p class="text-xs text-dark-500 dark:text-dark-400">{{ day.precip }}mm</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useDashboardStore } from '../stores';

interface ForecastDay {
  day: string;
  temp: number;
  precip: number;
  weather: string;
}

export default defineComponent({
  name: 'PlantWateringWidget',

  setup() {
    const dashboardStore = useDashboardStore();
    const loading = ref(false);

    // Watering status: 'water', 'wait', 'error'
    const wateringStatus = computed(() => {
      if (!dashboardStore.currentWeather?.current || !dashboardStore.forecastWeather?.forecast) {
        return 'error';
      }

      // Get current precipitation and forecast precipitation
      const currentPrecip = dashboardStore.currentWeather.current.precipitation || 0;
      const forecastPrecip = dashboardStore.forecastWeather.forecast.slice(0, 3).reduce((sum, day) => {
        return sum + (day.precipitation_sum || 0);
      }, 0);

      // Get current temperature
      const currentTemp = dashboardStore.currentWeather.current.temperature || 0;

      // Logic for watering recommendation
      if (currentPrecip > 5) {
        // If it's currently raining heavily, don't water
        return 'wait';
      } else if (forecastPrecip > 10) {
        // If significant rain is forecasted in the next 3 days, don't water
        return 'wait';
      } else if (currentTemp > 28) {
        // If it's very hot, water now
        return 'water';
      } else if (currentPrecip === 0 && forecastPrecip < 5) {
        // If it's dry and little rain is expected, water now
        return 'water';
      }

      // Default: wait for better conditions
      return 'wait';
    });

    // Generate a human-readable recommendation
    const wateringRecommendation = computed(() => {
      if (!dashboardStore.currentWeather?.current || !dashboardStore.forecastWeather?.forecast) {
        return 'Unable to provide watering recommendations due to missing weather data.';
      }

      const currentWeather = dashboardStore.currentWeather.current;
      const forecast = dashboardStore.forecastWeather.forecast;

      if (wateringStatus.value === 'water') {
        if (currentWeather.temperature > 28) {
          return 'It\'s hot today! Your plants will need water to stay hydrated.';
        } else if (currentWeather.precipitation === 0 && (forecast[0]?.precipitation_sum || 0) < 5) {
          return 'Dry conditions expected. Water your plants today to keep them healthy.';
        }
        return 'Conditions are suitable for watering your plants today.';
      } else if (wateringStatus.value === 'wait') {
        if (currentWeather.precipitation > 5) {
          return 'It\'s currently raining. No need to water your plants today.';
        } else if (forecast.slice(0, 3).reduce((sum, day) => sum + (day.precipitation_sum || 0), 0) > 10) {
          return 'Rain is forecasted in the next few days. Hold off on watering for now.';
        }
        return 'Current conditions suggest waiting before watering your plants.';
      }

      return 'Unable to determine watering recommendations at this time.';
    });

    // Format forecast data for display
    const forecastDays = computed(() => {
      if (!dashboardStore.forecastWeather?.forecast) {
        return [];
      }

      return dashboardStore.forecastWeather.forecast.slice(0, 3).map(day => {
        const date = new Date(day.date);
        return {
          day: date.toLocaleDateString('en-US', { weekday: 'short' }),
          temp: Math.round(day.max_temperature),
          precip: day.precipitation_sum || 0,
          weather: day.weather_description
        };
      });
    });

    // Helper functions for UI
    const getStatusColor = (status: string) => {
      switch (status) {
        case 'water':
          return 'text-success-500';
        case 'wait':
          return 'text-warning-500';
        default:
          return 'text-danger-500';
      }
    };

    const getStatusText = (status: string) => {
      switch (status) {
        case 'water':
          return 'Water Now';
        case 'wait':
          return 'Wait to Water';
        default:
          return 'Unable to Determine';
      }
    };

    return {
      loading,
      wateringStatus,
      wateringRecommendation,
      forecastDays,
      getStatusColor,
      getStatusText
    };
  }
});
</script>
