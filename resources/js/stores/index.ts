import { defineStore } from 'pinia';
import axios from 'axios';

// Define types for the store state
interface CurrentWeather {
  temperature: number;
  temperature_unit: string;
  weather_description: string;
  weather_code: number;
  [key: string]: any;
}

interface WeatherData {
  current: CurrentWeather;
  location: string;
  error?: string;
}

interface ForecastDay {
  date: string;
  max_temperature: number;
  min_temperature: number;
  weather_code: number;
  weather_description: string;
  [key: string]: any;
}

interface ForecastData {
  forecast: ForecastDay[];
  location: string;
  error?: string;
}

interface BinCollection {
  id: number;
  collection_date: string;
  bin_type: string;
  color: string;
  days_until: number;
  days_until_human: string;
}

interface DashboardState {
  currentWeather: WeatherData | null;
  forecastWeather: ForecastData | null;
  binCollections: BinCollection[];
  loading: boolean;
  error: string | null;
}

// Create the dashboard store
export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    currentWeather: null,
    forecastWeather: null,
    binCollections: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchDashboardData() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/api/dashboard');

        if (response.data) {
          this.currentWeather = response.data.currentWeather;
          this.forecastWeather = response.data.forecastWeather;
          this.binCollections = response.data.binCollections;
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        this.error = 'Failed to load dashboard data. Please try again later.';
      } finally {
        this.loading = false;
      }
    }
  }
});

// No need to export from other files as all stores are defined in this file
