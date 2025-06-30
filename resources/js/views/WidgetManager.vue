<template>
  <div>
    <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg mb-6">
      <div class="p-6">
        <h1 class="text-2xl font-semibold text-dark-900 dark:text-dark-100">Widget Manager</h1>
        <p class="mt-2 text-dark-600 dark:text-dark-400">Add, remove, and configure widgets for your dashboard.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-danger-50 dark:bg-danger-900/20 p-6 rounded-lg text-danger-700 dark:text-danger-300">
      <p>{{ error }}</p>
      <button @click="fetchWidgets" class="mt-4 px-4 py-2 bg-danger-100 dark:bg-danger-800 rounded-md hover:bg-danger-200 dark:hover:bg-danger-700 transition">
        Retry
      </button>
    </div>

    <div v-else>
      <!-- Widget List -->
      <div v-if="widgets.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="widget in widgets" :key="widget.id" class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg">
          <div class="p-4" :class="getHeaderColorClass(widget.type)">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-semibold text-white">{{ widget.name }}</h2>
              <div class="flex space-x-2">
                <button @click="editWidget(widget)" class="text-white hover:text-dark-100 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                  </svg>
                </button>
                <button @click="deleteWidget(widget.id)" class="text-white hover:text-danger-200 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <div class="p-6">
            <p class="text-dark-600 dark:text-dark-400 mb-4">Type: {{ formatWidgetType(widget.type) }}</p>
            <p class="text-dark-600 dark:text-dark-400 mb-4">Size: {{ widget.width }}x{{ widget.height }}</p>
            <p class="text-dark-600 dark:text-dark-400">Position: ({{ widget.position_x }}, {{ widget.position_y }})</p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white dark:bg-dark-800 overflow-hidden shadow-md rounded-lg p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-dark-300 dark:text-dark-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
        </svg>
        <h2 class="text-xl font-semibold text-dark-900 dark:text-dark-100 mb-2">No Widgets Found</h2>
        <p class="text-dark-600 dark:text-dark-400 mb-6">You haven't added any widgets to your dashboard yet.</p>
      </div>

      <!-- Add Widget Button -->
      <div class="mt-6">
        <button @click="showAddWidgetModal = true" class="w-full py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors flex items-center justify-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add New Widget
        </button>
      </div>

      <!-- Add Widget Modal (Placeholder) -->
      <div v-if="showAddWidgetModal" class="fixed inset-0 bg-dark-900/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-md w-full">
          <div class="p-4 border-b border-dark-200 dark:border-dark-700">
            <h2 class="text-xl font-semibold text-dark-900 dark:text-dark-100">Add New Widget</h2>
          </div>
          <div class="p-6">
            <p class="text-dark-600 dark:text-dark-400 mb-4">Widget form would go here...</p>
          </div>
          <div class="p-4 border-t border-dark-200 dark:border-dark-700 flex justify-end space-x-3">
            <button @click="showAddWidgetModal = false" class="px-4 py-2 bg-dark-200 dark:bg-dark-700 text-dark-700 dark:text-dark-300 rounded-md hover:bg-dark-300 dark:hover:bg-dark-600 transition-colors">
              Cancel
            </button>
            <button class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-md transition-colors">
              Add Widget
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import axios from 'axios';

interface Widget {
  id: number;
  name: string;
  type: string;
  position_x: number;
  position_y: number;
  width: number;
  height: number;
  settings: any;
  created_at: string;
  updated_at: string;
}

export default defineComponent({
  name: 'WidgetManager',

  setup() {
    const widgets = ref<Widget[]>([]);
    const loading = ref(true);
    const error = ref<string | null>(null);
    const showAddWidgetModal = ref(false);

    const fetchWidgets = async () => {
      loading.value = true;
      error.value = null;

      try {
        const response = await axios.get('/api/widgets');
        widgets.value = response.data.widgets || [];
      } catch (err) {
        console.error('Error fetching widgets:', err);
        error.value = 'Failed to load widgets. Please try again later.';
      } finally {
        loading.value = false;
      }
    };

    const deleteWidget = async (id: number) => {
      if (!confirm('Are you sure you want to delete this widget?')) return;

      try {
        await axios.delete(`/api/widgets/${id}`);
        widgets.value = widgets.value.filter(widget => widget.id !== id);
      } catch (err) {
        console.error('Error deleting widget:', err);
        alert('Failed to delete widget. Please try again later.');
      }
    };

    const editWidget = (widget: Widget) => {
      // This would open an edit modal in a real implementation
      alert(`Edit widget: ${widget.name}`);
    };

    const formatWidgetType = (type: string): string => {
      return type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    };

    const getHeaderColorClass = (type: string): string => {
      switch (type) {
        case 'weather':
          return 'bg-primary-500 dark:bg-primary-700';
        case 'bin-collection':
          return 'bg-secondary-500 dark:bg-secondary-700';
        case 'plant-watering':
          return 'bg-success-500 dark:bg-success-700';
        default:
          return 'bg-dark-500 dark:bg-dark-700';
      }
    };

    onMounted(() => {
      fetchWidgets();
    });

    return {
      widgets,
      loading,
      error,
      showAddWidgetModal,
      fetchWidgets,
      deleteWidget,
      editWidget,
      formatWidgetType,
      getHeaderColorClass
    };
  }
});
</script>
