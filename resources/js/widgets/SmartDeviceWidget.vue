<template>
  <div class="p-6 text-center">
    <svg class="w-12 h-12 mx-auto text-dark-400 dark:text-dark-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>

    <h3 class="mt-4 text-lg font-medium text-dark-900 dark:text-dark-100">Smart Device Functionality Removed</h3>

    <p class="mt-2 text-dark-600 dark:text-dark-400">
      The smart device control functionality has been removed from the application.
    </p>

    <p class="mt-2 text-dark-600 dark:text-dark-400">
      Please use the official apps from device manufacturers or other third-party solutions to control your smart devices.
    </p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'SmartDeviceWidget'
});
</script>
