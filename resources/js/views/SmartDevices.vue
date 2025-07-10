<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-dark-900 dark:text-dark-100">Smart Devices</h1>
      <p class="mt-2 text-dark-600 dark:text-dark-400">Smart device functionality has been removed.</p>
    </div>

    <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg p-6">
      <div class="text-center">
        <svg class="w-16 h-16 mx-auto text-dark-400 dark:text-dark-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>

        <h2 class="mt-4 text-xl font-medium text-dark-900 dark:text-dark-100">Smart Device Functionality Removed</h2>

        <p class="mt-2 text-dark-600 dark:text-dark-400">
          The smart device control functionality has been removed from the application for the following reasons:
        </p>

        <ul class="mt-4 text-left max-w-md mx-auto text-dark-600 dark:text-dark-400">
          <li class="flex items-start mb-2">
            <svg class="w-5 h-5 mr-2 text-danger-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            The Tuya API is a paid service
          </li>
          <li class="flex items-start mb-2">
            <svg class="w-5 h-5 mr-2 text-danger-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            The Alexa integration was too difficult to implement properly
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-danger-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            We would rather not have device control functionality than have incomplete functionality
          </li>
        </ul>

        <h3 class="mt-6 text-lg font-medium text-dark-900 dark:text-dark-100">Alternative Solutions</h3>

        <p class="mt-2 text-dark-600 dark:text-dark-400">
          For controlling your smart home devices, consider using one of these alternatives:
        </p>

        <ul class="mt-4 text-left max-w-md mx-auto text-dark-600 dark:text-dark-400">
          <li class="flex items-start mb-2">
            <svg class="w-5 h-5 mr-2 text-primary-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <strong>Official Apps</strong>: Use the official apps from device manufacturers (Alexa, Smart Life, etc.)
          </li>
          <li class="flex items-start mb-2">
            <svg class="w-5 h-5 mr-2 text-primary-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <strong>Home Assistant</strong>: An open-source home automation platform that supports many device types
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-primary-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <strong>Smart Things</strong>: Samsung's smart home platform that can integrate with many device types
          </li>
        </ul>

        <div class="mt-8">
          <a href="/" class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600 transition">
            Return to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'SmartDevices'
});
</script>
