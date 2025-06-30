<template>
  <div class="bin-collection-widget">
    <div v-if="loading" class="flex justify-center items-center p-6">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <div v-else-if="error" class="p-6 text-danger-500">
      <p>{{ error }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="collection in collections" :key="collection.id" class="flex items-center">
        <div
          class="w-4 h-4 rounded-full mr-3"
          :class="getBinColorClass(collection.color)"
        ></div>
        <div>
          <p class="text-dark-900 dark:text-dark-100 font-medium">
            {{ formatBinType(collection.bin_type) }}
          </p>
          <p class="text-dark-500 dark:text-dark-400 text-sm">
            {{ collection.days_until_human }}
          </p>
        </div>
      </div>

      <div v-if="collections.length === 0" class="text-dark-500 dark:text-dark-400 text-center p-4">
        No upcoming bin collections found.
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import axios from 'axios';

interface BinCollection {
  id: number;
  collection_date: string;
  bin_type: string;
  color: string;
  days_until: number;
  days_until_human: string;
}

export default defineComponent({
  name: 'BinCollectionWidget',

  props: {
    settings: {
      type: Object,
      default: () => ({
        show_countdown: true
      })
    }
  },

  setup(props) {
    const collections = ref<BinCollection[]>([]);
    const loading = ref(true);
    const error = ref<string | null>(null);

    const fetchCollections = async () => {
      try {
        loading.value = true;
        error.value = null;

        const response = await axios.get('/api/bin-collections/next');
        collections.value = response.data.collections;
      } catch (err) {
        console.error('Error fetching bin collections:', err);
        error.value = 'Failed to load bin collection data. Please try again later.';
      } finally {
        loading.value = false;
      }
    };

    const getBinColorClass = (color: string): string => {
      switch (color.toLowerCase()) {
        case 'green':
          return 'bg-success-500';
        case 'black':
          return 'bg-dark-800';
        case 'brown':
          return 'bg-warning-500';
        case 'blue':
          return 'bg-primary-500';
        case 'red':
          return 'bg-danger-500';
        default:
          return 'bg-dark-300';
      }
    };

    const formatBinType = (binType: string): string => {
      // Capitalize first letter of each word
      return binType
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    };

    onMounted(() => {
      fetchCollections();
    });

    return {
      collections,
      loading,
      error,
      getBinColorClass,
      formatBinType
    };
  }
});
</script>
