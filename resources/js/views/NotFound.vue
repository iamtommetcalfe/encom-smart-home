<template>
  <div class="flex flex-col items-center justify-center min-h-[60vh] px-4 text-center">
    <div class="text-9xl font-bold text-primary-300 dark:text-primary-700 mb-4">404</div>
    <h1 class="text-4xl font-bold text-dark-800 dark:text-dark-200 mb-4">Page Not Found</h1>
    <p class="text-lg text-dark-600 dark:text-dark-400 mb-8">
      The page you are looking for doesn't exist or has been moved.
    </p>
    <router-link
      to="/"
      class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
    >
      Return to Dashboard
    </router-link>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'NotFound'
});
</script>
