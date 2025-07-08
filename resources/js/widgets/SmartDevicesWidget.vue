<template>
  <div class="smart-devices-widget">
    <div v-if="dashboardStore.loading" class="flex justify-center items-center p-6">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <div v-else-if="!dashboardStore.smartDevices || dashboardStore.smartDevices.length === 0" class="p-6 text-dark-500 dark:text-dark-400 text-center">
      <p>No smart devices found. Add devices to this widget from the Smart Devices page.</p>
    </div>

    <div v-else class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="device in dashboardStore.smartDevices" :key="device.id"
             class="bg-white dark:bg-dark-700 rounded-lg shadow-sm overflow-hidden border border-dark-100 dark:border-dark-600">
          <div class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <!-- Device Icon based on type -->
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 mr-3">
                  <svg v-if="device.deviceType.includes('light')" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                  <svg v-else-if="device.deviceType.includes('switch')" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                  <svg v-else-if="device.deviceType.includes('sensor')" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-dark-900 dark:text-dark-100">{{ device.name }}</h3>
                  <p class="text-sm text-dark-500 dark:text-dark-400">{{ device.room || 'No Room' }}</p>
                </div>
              </div>

              <!-- Toggle Switch -->
              <button
                @click="toggleDevice(device)"
                :disabled="device.isToggling"
                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                :class="[
                  device.isOn ? 'bg-primary-600' : 'bg-dark-200 dark:bg-dark-600',
                  device.isToggling ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                ]"
              >
                <span
                  class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform"
                  :class="[
                    device.isOn ? 'translate-x-6' : 'translate-x-1',
                    device.isToggling ? 'animate-pulse' : ''
                  ]"
                ></span>
              </button>
            </div>

            <!-- Device Status -->
            <div class="mt-3 flex items-center">
              <span
                class="inline-block w-2 h-2 rounded-full mr-2"
                :class="device.isOn ? 'bg-success-500' : 'bg-dark-300 dark:bg-dark-500'"
              ></span>
              <span class="text-sm text-dark-600 dark:text-dark-400">
                {{ device.isOn ? 'On' : 'Off' }}
              </span>
              <span v-if="device.platform" class="ml-auto text-xs text-dark-400 dark:text-dark-500">
                {{ device.platform }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useDashboardStore } from '../stores';
import axios from 'axios';

interface SmartDevice {
  id: number;
  name: string;
  deviceType: string;
  room: string;
  isOn: boolean;
  platform: string;
  capabilities: string[];
  isToggling?: boolean;
}

export default defineComponent({
  name: 'SmartDevicesWidget',

  setup() {
    const dashboardStore = useDashboardStore();

    // Function to toggle a device's state
    const toggleDevice = async (device: SmartDevice) => {
      // Skip if already toggling
      if (device.isToggling) return;

      // Set toggling state
      const deviceIndex = dashboardStore.smartDevices.findIndex(d => d.id === device.id);
      if (deviceIndex !== -1) {
        dashboardStore.smartDevices[deviceIndex].isToggling = true;
      }

      try {
        // Call the API to toggle the device
        const response = await axios.post(`/api/smart-devices/${device.id}/toggle`);

        // Update the device state in the store
        if (response.data && response.data.success) {
          if (deviceIndex !== -1) {
            dashboardStore.smartDevices[deviceIndex].isOn = !device.isOn;
          }
        } else {
          console.error('Failed to toggle device:', response.data?.message || 'Unknown error');
        }
      } catch (error) {
        console.error('Error toggling device:', error);
      } finally {
        // Clear toggling state
        if (deviceIndex !== -1) {
          dashboardStore.smartDevices[deviceIndex].isToggling = false;
        }
      }
    };

    return {
      dashboardStore,
      toggleDevice
    };
  }
});
</script>
