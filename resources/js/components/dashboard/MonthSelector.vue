<template>
    <div class="flex items-center space-x-3 bg-gray-800 p-3 rounded-lg shadow-md">
      <label for="month-selector" class="text-white text-sm font-semibold">
        📅 Sélectionnez une période :
      </label>
      <input
        type="month"
        id="month-selector"
        v-model.trim="selectedMonth"
        class="bg-gray-700 text-white rounded-md px-4 py-2 border border-gray-600 focus:ring-2 focus:ring-indigo-500"
      />
    </div>
</template>
  
<script setup>
  import { useDashboardStore } from '@/stores/dashboard';
  import { storeToRefs } from 'pinia';

  const dashboardStore = useDashboardStore()
  const { selectedMonth } = storeToRefs(dashboardStore)
</script>
  
  