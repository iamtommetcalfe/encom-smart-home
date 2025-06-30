<template>
  <div class="weather-widget">
    <div v-if="loading" class="flex justify-center items-center p-6">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <div v-else-if="error" class="p-6 text-danger-500">
      <p>{{ error }}</p>
    </div>

    <div v-else>
      <div class="flex items-center justify-between">
        <div>
          <p class="text-4xl font-bold text-dark-900 dark:text-dark-100">
            {{ currentWeather?.temperature ?? '?' }}{{ currentWeather?.temperature_unit ?? '°C' }}
          </p>
          <p class="text-dark-600 dark:text-dark-400">
            {{ currentWeather?.weather_description ?? 'Unknown' }}
          </p>
        </div>
        <div>
          <svg class="w-16 h-16 text-primary-500 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getWeatherIcon(currentWeather?.weather_code)"></path>
          </svg>
        </div>
      </div>

      <div v-if="forecast.length > 0" class="mt-4 grid grid-cols-4 gap-2">
        <div v-for="(day, index) in forecast" :key="index" class="text-center">
          <p class="text-dark-500 dark:text-dark-400 text-xs">
            {{ formatDay(day.date) }}
          </p>
          <p class="text-dark-900 dark:text-dark-100 text-sm font-medium">
            {{ Math.round(day.max_temperature) }}°C
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import axios from 'axios';

interface CurrentWeather {
  temperature: number;
  temperature_unit: string;
  apparent_temperature: number;
  humidity: number;
  precipitation: number;
  weather_code: number;
  weather_description: string;
  wind_speed: number;
  wind_direction: number;
  time: string;
}

interface ForecastDay {
  date: string;
  max_temperature: number;
  min_temperature: number;
  precipitation_sum: number;
  weather_code: number;
  weather_description: string;
}

export default defineComponent({
  name: 'WeatherWidget',

  props: {
    settings: {
      type: Object,
      default: () => ({
        location: 'Austrey, UK',
        units: 'metric',
        show_forecast: true,
        days_to_show: 4
      })
    }
  },

  setup(props) {
    const currentWeather = ref<CurrentWeather | null>(null);
    const forecast = ref<ForecastDay[]>([]);
    const loading = ref(true);
    const error = ref<string | null>(null);
    const location = ref(props.settings.location || 'Austrey, UK');

    const fetchWeatherData = async () => {
      try {
        loading.value = true;
        error.value = null;

        // Fetch current weather
        const currentResponse = await axios.get('/api/weather/current');
        if (currentResponse.data.error) {
          error.value = currentResponse.data.error;
        } else {
          currentWeather.value = currentResponse.data.current;
          location.value = currentResponse.data.location;
        }

        // Fetch forecast
        const forecastResponse = await axios.get('/api/weather/forecast', {
          params: {
            days: props.settings.days_to_show || 4
          }
        });

        if (forecastResponse.data.error) {
          error.value = forecastResponse.data.error;
        } else {
          forecast.value = forecastResponse.data.forecast || [];
        }
      } catch (err) {
        console.error('Error fetching weather data:', err);
        error.value = 'Failed to load weather data. Please try again later.';
      } finally {
        loading.value = false;
      }
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

    onMounted(() => {
      fetchWeatherData();
    });

    return {
      currentWeather,
      forecast,
      loading,
      error,
      location,
      formatDay,
      getWeatherIcon,
      fetchWeatherData
    };
  }
});
</script>
