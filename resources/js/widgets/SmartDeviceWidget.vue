<template>
  <div class="smart-device-widget">
    <div v-if="loading" class="flex justify-center items-center p-6">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <div v-else-if="error" class="p-6 text-danger-500">
      <p>{{ error }}</p>
    </div>

    <div v-else-if="!hasDevices" class="p-6 text-dark-500 dark:text-dark-400 text-center">
      <p>No smart devices configured.</p>
      <router-link to="/smart-devices" class="mt-2 inline-block text-primary-500 hover:text-primary-600 dark:hover:text-primary-400">
        Configure devices
      </router-link>
    </div>

    <div v-else class="space-y-4">
      <!-- Group devices by room -->
      <div v-for="(devices, room) in devicesByRoom" :key="room" class="bg-dark-50 dark:bg-dark-700 p-4 rounded-lg">
        <h3 class="text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">{{ room }}</h3>

        <div class="space-y-3">
          <div v-for="device in devices" :key="device.id" class="flex items-center justify-between">
            <div class="flex items-center">
              <!-- Device icon based on type -->
              <div class="w-8 h-8 flex items-center justify-center mr-3">
                <svg v-if="device.deviceType === 'light'" class="w-5 h-5" :class="device.isOn ? 'text-warning-400' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                </svg>
                <svg v-else-if="device.deviceType === 'switch'" class="w-5 h-5" :class="device.isOn ? 'text-success-500' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-5 h-5" :class="device.isOn ? 'text-primary-500' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                </svg>
              </div>

              <div>
                <p class="text-dark-900 dark:text-dark-100 font-medium">{{ device.name }}</p>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ device.platform }}</p>
              </div>
            </div>

            <!-- Toggle switch -->
            <button
              @click="toggleDevice(device)"
              :disabled="device.isToggling"
              class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              :class="[
                device.isOn ? 'bg-success-500' : 'bg-dark-300',
                device.isToggling ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
              ]"
            >
              <span
                class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform"
                :class="device.isOn ? 'translate-x-6' : 'translate-x-1'"
              ></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useDashboardStore } from '../stores';

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
  name: 'SmartDeviceWidget',

  setup() {
    const dashboardStore = useDashboardStore();
    const loading = ref(false);
    const error = ref<string | null>(null);
    const devices = ref<SmartDevice[]>([]);

    // Group devices by room
    const devicesByRoom = computed(() => {
      const grouped: Record<string, SmartDevice[]> = {};

      devices.value.forEach(device => {
        const room = device.room || 'Other';
        if (!grouped[room]) {
          grouped[room] = [];
        }
        grouped[room].push(device);
      });

      // Sort rooms alphabetically
      return Object.keys(grouped)
        .sort()
        .reduce((acc: Record<string, SmartDevice[]>, room) => {
          acc[room] = grouped[room];
          return acc;
        }, {});
    });

    const hasDevices = computed(() => devices.value.length > 0);

    // Fetch smart devices from the store
    const fetchDevices = () => {
      if (!dashboardStore.smartDevices) {
        error.value = 'Smart devices data not available';
        return;
      }

      devices.value = dashboardStore.smartDevices;
    };

    // Toggle device state
    const toggleDevice = async (device: SmartDevice) => {
      try {
        // Set toggling state
        const index = devices.value.findIndex(d => d.id === device.id);
        if (index !== -1) {
          devices.value[index].isToggling = true;
        }

        // Call API to toggle device
        const newState = !device.isOn;
        await fetch(`/api/smart-devices/${device.id}/toggle`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({ state: newState }),
        });

        // Update local state
        if (index !== -1) {
          devices.value[index].isOn = newState;
          devices.value[index].isToggling = false;
        }
      } catch (err) {
        console.error('Error toggling device:', err);
        error.value = 'Failed to toggle device. Please try again.';

        // Reset toggling state
        const index = devices.value.findIndex(d => d.id === device.id);
        if (index !== -1) {
          devices.value[index].isToggling = false;
        }
      }
    };

    onMounted(() => {
      loading.value = true;
      error.value = null;

      try {
        fetchDevices();
      } catch (err) {
        console.error('Error fetching smart devices:', err);
        error.value = 'Failed to load smart devices. Please try again later.';
      } finally {
        loading.value = false;
      }
    });

    return {
      loading,
      error,
      devices,
      devicesByRoom,
      hasDevices,
      toggleDevice,
    };
  }
});
</script>
