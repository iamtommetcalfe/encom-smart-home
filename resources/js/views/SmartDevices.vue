<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-dark-900 dark:text-dark-100">Smart Devices</h1>
      <p class="mt-2 text-dark-600 dark:text-dark-400">Manage your smart home devices and widget configuration.</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-danger-50 dark:bg-danger-900/20 p-6 rounded-lg text-danger-700 dark:text-danger-300">
      <p>{{ error }}</p>
      <button @click="fetchData" class="mt-4 px-4 py-2 bg-danger-100 dark:bg-danger-800 rounded-md hover:bg-danger-200 dark:hover:bg-danger-700 transition">
        Retry
      </button>
    </div>

    <div v-else>
      <!-- Platforms Section -->
      <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg mb-6">
        <div class="p-4 bg-primary-500 dark:bg-primary-700">
          <h2 class="text-lg font-semibold text-white">Connected Platforms</h2>
        </div>
        <div class="p-6">
          <div v-if="platforms.length === 0" class="text-center text-dark-500 dark:text-dark-400 py-4">
            No platforms connected. Add a platform to get started.
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="platform in platforms" :key="platform.id" class="border dark:border-dark-600 rounded-lg p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                  <!-- Platform icons -->
                  <div class="w-10 h-10 flex items-center justify-center mr-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                    <svg v-if="platform.slug === 'alexa'" class="w-6 h-6 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-3.5v-7l6 3.5-6 3.5z"/>
                    </svg>
                    <svg v-else-if="platform.slug === 'govee'" class="w-6 h-6 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-3.5v-7l6 3.5-6 3.5z"/>
                    </svg>
                    <svg v-else class="w-6 h-6 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-3.5v-7l6 3.5-6 3.5z"/>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-dark-900 dark:text-dark-100">{{ platform.name }}</h3>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ platform.deviceCount }} devices</p>
                  </div>
                </div>
                <div>
                  <span
                    class="px-2 py-1 text-xs rounded-full"
                    :class="platform.isConnected ? 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200' : 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200'"
                  >
                    {{ platform.isConnected ? 'Connected' : 'Disconnected' }}
                  </span>
                </div>
              </div>

              <div class="flex space-x-2">
                <button
                  v-if="platform.isConnected"
                  @click="disconnectPlatform(platform)"
                  class="px-3 py-1 text-sm bg-dark-100 dark:bg-dark-700 hover:bg-dark-200 dark:hover:bg-dark-600 rounded transition"
                >
                  Disconnect
                </button>
                <button
                  v-else
                  @click="connectPlatform(platform)"
                  class="px-3 py-1 text-sm bg-primary-500 text-white hover:bg-primary-600 rounded transition"
                >
                  Connect
                </button>
                <button
                  @click="refreshDevices(platform)"
                  class="px-3 py-1 text-sm bg-dark-100 dark:bg-dark-700 hover:bg-dark-200 dark:hover:bg-dark-600 rounded transition"
                  :disabled="!platform.isConnected || refreshingPlatforms.includes(platform.id)"
                  :class="{'opacity-50 cursor-not-allowed': !platform.isConnected || refreshingPlatforms.includes(platform.id)}"
                >
                  <span v-if="refreshingPlatforms.includes(platform.id)" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-dark-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Refreshing...
                  </span>
                  <span v-else>Refresh Devices</span>
                </button>
                <button
                  @click="editPlatform(platform)"
                  class="px-3 py-1 text-sm bg-dark-100 dark:bg-dark-700 hover:bg-dark-200 dark:hover:bg-dark-600 rounded transition"
                >
                  Edit
                </button>
                <button
                  @click="deletePlatform(platform)"
                  class="px-3 py-1 text-sm bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200 hover:bg-danger-200 dark:hover:bg-danger-800 rounded transition"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button
              @click="showAddPlatformModal = true"
              class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600 transition"
            >
              Add Platform
            </button>
          </div>
        </div>
      </div>

      <!-- Devices Section -->
      <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg mb-6">
        <div class="p-4 bg-secondary-500 dark:bg-secondary-700">
          <h2 class="text-lg font-semibold text-white">Smart Devices</h2>
        </div>
        <div class="p-6">
          <div v-if="devices.length === 0" class="text-center text-dark-500 dark:text-dark-400 py-4">
            No devices found. Connect a platform to discover devices.
          </div>

          <div v-else>
            <!-- Filter and search -->
            <div class="mb-4 flex flex-wrap gap-2">
              <div class="flex-1 min-w-[200px]">
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search devices..."
                  class="w-full px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
                />
              </div>
              <select
                v-model="roomFilter"
                class="px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
              >
                <option value="">All Rooms</option>
                <option v-for="room in uniqueRooms" :key="room" :value="room">{{ room }}</option>
              </select>
              <select
                v-model="platformFilter"
                class="px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
              >
                <option value="">All Platforms</option>
                <option v-for="platform in platforms" :key="platform.id" :value="platform.slug">{{ platform.name }}</option>
              </select>
              <select
                v-model="typeFilter"
                class="px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
              >
                <option value="">All Types</option>
                <option value="light">Lights</option>
                <option value="switch">Switches</option>
                <option value="thermostat">Thermostats</option>
                <option value="sensor">Sensors</option>
              </select>
            </div>

            <!-- Devices list -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-dark-200 dark:divide-dark-600">
                <thead class="bg-dark-50 dark:bg-dark-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Device</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Room</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Platform</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-dark-800 divide-y divide-dark-200 dark:divide-dark-600">
                  <tr v-for="device in filteredDevices" :key="device.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                          <svg v-if="device.deviceType === 'light'" class="w-6 h-6" :class="device.isOn ? 'text-warning-400' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                          </svg>
                          <svg v-else-if="device.deviceType === 'switch'" class="w-6 h-6" :class="device.isOn ? 'text-success-500' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                          </svg>
                          <svg v-else class="w-6 h-6" :class="device.isOn ? 'text-primary-500' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-dark-900 dark:text-dark-100">{{ device.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-dark-900 dark:text-dark-100">{{ device.room || 'Unassigned' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-dark-900 dark:text-dark-100">{{ formatDeviceType(device.deviceType) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-dark-900 dark:text-dark-100">{{ device.platform }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 py-1 text-xs rounded-full"
                        :class="device.isOn ? 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200' : 'bg-dark-100 text-dark-800 dark:bg-dark-700 dark:text-dark-300'"
                      >
                        {{ device.isOn ? 'On' : 'Off' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="toggleDevice(device)"
                        class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300 mr-3"
                        :disabled="device.isToggling"
                        :class="{'opacity-50 cursor-not-allowed': device.isToggling}"
                      >
                        <span v-if="device.isToggling" class="inline-flex items-center">
                          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Processing...
                        </span>
                        <span v-else>{{ device.isOn ? 'Turn Off' : 'Turn On' }}</span>
                      </button>
                      <button
                        @click="addToWidget(device)"
                        class="text-success-600 dark:text-success-400 hover:text-success-900 dark:hover:text-success-300"
                        :class="{'opacity-50 cursor-not-allowed': isInWidget(device)}"
                        :disabled="isInWidget(device)"
                      >
                        {{ isInWidget(device) ? 'In Widget' : 'Add to Widget' }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Widget Configuration Section -->
      <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg">
        <div class="p-4 bg-success-500 dark:bg-success-700">
          <h2 class="text-lg font-semibold text-white">Widget Configuration</h2>
        </div>
        <div class="p-6">
          <div v-if="widgetDevices.length === 0" class="text-center text-dark-500 dark:text-dark-400 py-4">
            No devices added to widget. Add devices from the list above.
          </div>

          <div v-else>
            <h3 class="text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">Devices in Widget</h3>
            <div class="space-y-2">
              <div
                v-for="device in widgetDevices"
                :key="device.id"
                class="flex items-center justify-between p-3 bg-dark-50 dark:bg-dark-700 rounded-lg"
              >
                <div class="flex items-center">
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
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ device.room || 'Unassigned' }} • {{ device.platform }}</p>
                  </div>
                </div>
                <button
                  @click="removeFromWidget(device)"
                  class="text-danger-600 dark:text-danger-400 hover:text-danger-900 dark:hover:text-danger-300"
                >
                  Remove
                </button>
              </div>
            </div>

            <div class="mt-4">
              <button
                @click="saveWidgetConfig"
                class="px-4 py-2 bg-success-500 text-white rounded hover:bg-success-600 transition"
              >
                Save Widget Configuration
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Platform Modal -->
    <div v-if="showAddPlatformModal" class="fixed inset-0 bg-dark-900 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="p-4 border-b dark:border-dark-600">
          <h3 class="text-lg font-medium text-dark-900 dark:text-dark-100">Add Smart Home Platform</h3>
        </div>
        <div class="p-6">
          <div class="mb-4">
            <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">Platform</label>
            <select
              v-model="newPlatform.slug"
              class="w-full px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
            >
              <option value="">Select a platform</option>
              <option value="alexa">Amazon Alexa</option>
              <option value="govee">Govee</option>
              <option value="tuya">Smart Life (Tuya)</option>
            </select>
          </div>

          <div v-if="newPlatform.slug === 'alexa'" class="space-y-4">
            <p class="text-sm text-dark-600 dark:text-dark-400">
              To connect with Alexa, you'll need to authorize this application with your Amazon account.
            </p>
            <button
              @click="redirectToAmazonAuth"
              class="w-full px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600 transition">
              Connect with Amazon
            </button>
          </div>

          <div v-else-if="newPlatform.slug === 'govee'" class="space-y-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">API Key</label>
              <input
                type="text"
                v-model="newPlatform.apiKey"
                class="w-full px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
                placeholder="Enter your Govee API key"
              />
              <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                You can get your API key from the Govee Developer Portal.
              </p>
            </div>
          </div>

          <div v-else-if="newPlatform.slug === 'tuya'" class="space-y-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">Client ID</label>
              <input
                type="text"
                v-model="newPlatform.clientId"
                class="w-full px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
                placeholder="Enter your Tuya Client ID"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">Client Secret</label>
              <input
                type="password"
                v-model="newPlatform.clientSecret"
                class="w-full px-3 py-2 border dark:border-dark-600 rounded bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-100"
                placeholder="Enter your Tuya Client Secret"
              />
              <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                You can get your Client ID and Secret from the Tuya IoT Platform.
              </p>
            </div>
          </div>
        </div>
        <div class="p-4 border-t dark:border-dark-600 flex justify-end space-x-3">
          <button
            @click="showAddPlatformModal = false"
            class="px-4 py-2 bg-dark-100 dark:bg-dark-700 hover:bg-dark-200 dark:hover:bg-dark-600 rounded transition"
          >
            Cancel
          </button>
          <button
            @click="addPlatform"
            class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600 transition"
            :disabled="!isPlatformFormValid"
            :class="{'opacity-50 cursor-not-allowed': !isPlatformFormValid}"
          >
            Add Platform
          </button>
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

interface SmartHomePlatform {
  id: number;
  name: string;
  slug: string;
  isConnected: boolean;
  deviceCount: number;
}

interface NewPlatform {
  slug: string;
  apiKey?: string;
  clientId?: string;
  clientSecret?: string;
}

export default defineComponent({
  name: 'SmartDevices',

  setup() {
    const dashboardStore = useDashboardStore();
    const loading = ref(false);
    const error = ref<string | null>(null);
    const platforms = ref<SmartHomePlatform[]>([]);
    const devices = ref<SmartDevice[]>([]);
    const widgetDevices = ref<SmartDevice[]>([]);
    const refreshingPlatforms = ref<number[]>([]);

    // Filters
    const searchQuery = ref('');
    const roomFilter = ref('');
    const platformFilter = ref('');
    const typeFilter = ref('');

    // Modal state
    const showAddPlatformModal = ref(false);
    const newPlatform = ref<NewPlatform>({ slug: '' });

    // Computed properties
    const uniqueRooms = computed(() => {
      const rooms = devices.value.map(device => device.room || 'Unassigned');
      return [...new Set(rooms)].sort();
    });

    const filteredDevices = computed(() => {
      return devices.value.filter(device => {
        // Apply search filter
        if (searchQuery.value && !device.name.toLowerCase().includes(searchQuery.value.toLowerCase())) {
          return false;
        }

        // Apply room filter
        if (roomFilter.value && device.room !== roomFilter.value) {
          return false;
        }

        // Apply platform filter
        if (platformFilter.value) {
          const platform = platforms.value.find(p => p.slug === platformFilter.value);
          if (platform && device.platform !== platform.name) {
            return false;
          }
        }

        // Apply type filter
        if (typeFilter.value && device.deviceType !== typeFilter.value) {
          return false;
        }

        return true;
      });
    });

    const isPlatformFormValid = computed(() => {
      if (!newPlatform.value.slug) return false;

      if (newPlatform.value.slug === 'govee') {
        return !!newPlatform.value.apiKey;
      }

      if (newPlatform.value.slug === 'tuya') {
        return !!newPlatform.value.clientId && !!newPlatform.value.clientSecret;
      }

      return true;
    });

    // Methods
    const fetchData = async () => {
      loading.value = true;
      error.value = null;

      try {
        // Fetch platforms from API
        const platformsResponse = await fetch('/api/smart-home/platforms');
        if (!platformsResponse.ok) {
          throw new Error(`Failed to fetch platforms: ${platformsResponse.statusText}`);
        }
        const platformsData = await platformsResponse.json();
        platforms.value = platformsData.platforms.map(platform => ({
          id: platform.id,
          name: platform.name,
          slug: platform.slug,
          isConnected: platform.is_active,
          deviceCount: platform.devices?.length || 0
        }));

        // Fetch devices from API
        const devicesResponse = await fetch('/api/smart-home/devices');
        if (!devicesResponse.ok) {
          throw new Error(`Failed to fetch devices: ${devicesResponse.statusText}`);
        }
        const devicesData = await devicesResponse.json();
        devices.value = devicesData.devices.map(device => {
          const platformName = platforms.value.find(p => p.id === device.platform_id)?.name || 'Unknown';
          return {
            id: device.id,
            name: device.name,
            deviceType: device.device_type,
            room: device.room || '',
            isOn: device.last_state?.power || false,
            platform: platformName,
            capabilities: device.capabilities || []
          };
        });

        // Fetch widget configuration from the API
        try {
          const widgetConfigResponse = await fetch('/api/smart-home/widget-config');
          if (!widgetConfigResponse.ok) {
            throw new Error(`Failed to fetch widget configuration: ${widgetConfigResponse.statusText}`);
          }

          const widgetConfigData = await widgetConfigResponse.json();

          if (widgetConfigData.devices && widgetConfigData.devices.length > 0) {
            // Map the devices to the format expected by the UI
            widgetDevices.value = widgetConfigData.devices.map(device => ({
              id: device.id,
              name: device.name,
              deviceType: device.device_type,
              room: device.room || '',
              isOn: device.last_state?.power || false,
              platform: platforms.value.find(p => p.id === device.platform_id)?.name || 'Unknown',
              capabilities: device.capabilities || []
            }));
          } else {
            widgetDevices.value = [];
          }
        } catch (widgetErr) {
          console.error('Error fetching widget configuration:', widgetErr);
          // Don't set the main error state, just log the error and start with an empty widget
          widgetDevices.value = [];
        }
      } catch (err) {
        console.error('Error fetching smart home data:', err);
        error.value = 'Failed to load smart home data. Please try again later.';
      } finally {
        loading.value = false;
      }
    };

    const formatDeviceType = (type: string): string => {
      return type.charAt(0).toUpperCase() + type.slice(1);
    };

    const toggleDevice = async (device: SmartDevice) => {
      try {
        // Set toggling state
        const index = devices.value.findIndex(d => d.id === device.id);
        if (index !== -1) {
          devices.value[index].isToggling = true;
        }

        // Call API to toggle device
        const response = await fetch(`/api/smart-devices/${device.id}/toggle`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          }
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || `Failed to toggle device: ${response.statusText}`);
        }

        // Check if the toggle operation was successful
        if (!data.success) {
          throw new Error(data.message || 'Failed to toggle device');
        }

        // Update local state only if the toggle operation was successful
        if (index !== -1) {
          devices.value[index].isOn = !devices.value[index].isOn;
          devices.value[index].isToggling = false;

          // Also update in widget devices if present
          const widgetIndex = widgetDevices.value.findIndex(d => d.id === device.id);
          if (widgetIndex !== -1) {
            widgetDevices.value[widgetIndex].isOn = devices.value[index].isOn;
          }
        }
      } catch (err) {
        console.error('Error toggling device:', err);
        // Show a more specific error message
        error.value = err instanceof Error ? err.message : 'Failed to toggle device. Please try again.';

        // Reset toggling state
        const index = devices.value.findIndex(d => d.id === device.id);
        if (index !== -1) {
          devices.value[index].isToggling = false;
        }
      }
    };

    const connectPlatform = async (platform: SmartHomePlatform) => {
      try {
        // Call API to connect platform and sync devices
        const response = await fetch(`/api/smart-home/platforms/${platform.id}/connect`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          }
        });

        if (!response.ok) {
          throw new Error(`Failed to connect platform: ${response.statusText}`);
        }

        // Refresh the data to get updated platform and device information
        await fetchData();
      } catch (err) {
        console.error('Error connecting platform:', err);
        error.value = 'Failed to connect platform. Please try again.';
      }
    };

    const disconnectPlatform = async (platform: SmartHomePlatform) => {
      try {
        // Call API to update platform and set is_active to false
        const response = await fetch(`/api/smart-home/platforms/${platform.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({
            is_active: false
          })
        });

        if (!response.ok) {
          throw new Error(`Failed to disconnect platform: ${response.statusText}`);
        }

        // Refresh the data to get updated platform and device information
        await fetchData();
      } catch (err) {
        console.error('Error disconnecting platform:', err);
        error.value = 'Failed to disconnect platform. Please try again.';
      }
    };

    const refreshDevices = async (platform: SmartHomePlatform) => {
      try {
        // Set the platform as refreshing
        refreshingPlatforms.value.push(platform.id);

        // Call API to refresh devices for the platform
        const response = await fetch(`/api/smart-home/platforms/${platform.id}/refresh`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          }
        });

        if (!response.ok) {
          throw new Error(`Failed to refresh devices: ${response.statusText}`);
        }

        // Refresh the data to get updated platform and device information
        await fetchData();
      } catch (err) {
        console.error('Error refreshing devices:', err);
        error.value = 'Failed to refresh devices. Please try again.';
      } finally {
        // Remove the platform from the refreshing list
        refreshingPlatforms.value = refreshingPlatforms.value.filter(id => id !== platform.id);
      }
    };

    const editPlatform = (platform: SmartHomePlatform) => {
      // Mock implementation
      alert(`Edit platform: ${platform.name}`);
    };

    const deletePlatform = async (platform: SmartHomePlatform) => {
      if (!confirm(`Are you sure you want to delete ${platform.name}?`)) {
        return;
      }

      try {
        // Call API to delete the platform
        const response = await fetch(`/api/smart-home/platforms/${platform.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          }
        });

        if (!response.ok) {
          throw new Error(`Failed to delete platform: ${response.statusText}`);
        }

        // Refresh the data to get updated platform and device information
        await fetchData();
      } catch (err) {
        console.error('Error deleting platform:', err);
        error.value = 'Failed to delete platform. Please try again.';
      }
    };


    const isInWidget = (device: SmartDevice): boolean => {
      return widgetDevices.value.some(d => d.id === device.id);
    };

    const addToWidget = (device: SmartDevice) => {
      if (!isInWidget(device)) {
        widgetDevices.value.push(device);
      }
    };

    const removeFromWidget = (device: SmartDevice) => {
      widgetDevices.value = widgetDevices.value.filter(d => d.id !== device.id);
    };

    const saveWidgetConfig = async () => {
      try {
        // Get the IDs of the devices in the widget
        const deviceIds = widgetDevices.value.map(device => device.id);

        // Call API to save widget configuration
        const response = await fetch('/api/smart-home/widget-config', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({
            devices: deviceIds
          })
        });

        if (!response.ok) {
          throw new Error(`Failed to save widget configuration: ${response.statusText}`);
        }

        // Update the dashboard store to reflect the changes
        dashboardStore.smartDevices = widgetDevices.value;

        alert('Widget configuration saved successfully!');
      } catch (err) {
        console.error('Error saving widget configuration:', err);
        error.value = err instanceof Error ? err.message : 'Failed to save widget configuration. Please try again.';
      }
    };

    const addPlatform = async () => {
      try {
        const platformNames: Record<string, string> = {
          'alexa': 'Amazon Alexa',
          'govee': 'Govee',
          'tuya': 'Smart Life'
        };

        // Prepare platform data
        const platformData = {
          name: platformNames[newPlatform.value.slug] || newPlatform.value.slug,
          slug: newPlatform.value.slug,
          description: `${platformNames[newPlatform.value.slug] || newPlatform.value.slug} Smart Home Platform`,
          is_active: true,
          credentials: {}
        };

        // Add credentials based on platform type
        if (newPlatform.value.slug === 'govee' && newPlatform.value.apiKey) {
          platformData.credentials = {
            api_key: newPlatform.value.apiKey
          };
        } else if (newPlatform.value.slug === 'tuya' && newPlatform.value.clientId && newPlatform.value.clientSecret) {
          platformData.credentials = {
            client_id: newPlatform.value.clientId,
            client_secret: newPlatform.value.clientSecret
          };
        }

        // Make API call to create platform
        const response = await fetch('/api/smart-home/platforms', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify(platformData)
        });

        if (!response.ok) {
          throw new Error(`Failed to add platform: ${response.statusText}`);
        }

        const result = await response.json();

        // If it's a Govee platform, connect and sync devices
        if (newPlatform.value.slug === 'govee' && result.platform && result.platform.id) {
          await connectPlatform({
            id: result.platform.id,
            name: result.platform.name,
            slug: result.platform.slug,
            isConnected: result.platform.is_active,
            deviceCount: 0
          });
        }

        // Refresh the data to get updated platform and device information
        await fetchData();

        showAddPlatformModal.value = false;
        newPlatform.value = { slug: '' };
      } catch (err) {
        console.error('Error adding platform:', err);
        error.value = 'Failed to add platform. Please try again.';
      }
    };

    const redirectToAmazonAuth = () => {
      // Redirect to the Alexa auth endpoint to start the OAuth flow
      window.location.href = '/auth/alexa';
    };

    onMounted(() => {
      fetchData();
    });

    return {
      loading,
      error,
      platforms,
      devices,
      widgetDevices,
      refreshingPlatforms,
      searchQuery,
      roomFilter,
      platformFilter,
      typeFilter,
      uniqueRooms,
      filteredDevices,
      showAddPlatformModal,
      newPlatform,
      isPlatformFormValid,
      fetchData,
      formatDeviceType,
      toggleDevice,
      connectPlatform,
      disconnectPlatform,
      refreshDevices,
      editPlatform,
      deletePlatform,
      isInWidget,
      addToWidget,
      removeFromWidget,
      saveWidgetConfig,
      addPlatform,
      redirectToAmazonAuth
    };
  }
});
</script>
