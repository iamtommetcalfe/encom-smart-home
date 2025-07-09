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

interface SmartDevice {
  id: number;
  name: string;
  deviceType: string;
  room: string;
  isOn: boolean;
  platform: string;
  capabilities: string[];
}

interface DashboardState {
  currentWeather: WeatherData | null;
  forecastWeather: ForecastData | null;
  binCollections: BinCollection[];
  smartDevices: SmartDevice[];
  loading: boolean;
  error: string | null;
}

// Create the dashboard store
export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    currentWeather: null,
    forecastWeather: null,
    binCollections: [],
    smartDevices: [],
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

        // Fetch smart devices for the widget
        try {
          const smartDevicesResponse = await axios.get('/api/smart-home/widget-config');
          if (smartDevicesResponse.data && smartDevicesResponse.data.devices) {
            this.smartDevices = smartDevicesResponse.data.devices.map(device => ({
              id: device.id,
              name: device.name,
              deviceType: device.device_type,
              room: device.room || '',
              isOn: device.last_state?.power || false,
              platform: device.platform?.name || 'Unknown',
              capabilities: device.capabilities || []
            }));
          }
        } catch (smartDevicesError) {
          console.error('Error fetching smart devices:', smartDevicesError);
          // Don't set the main error state, just log the error
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
