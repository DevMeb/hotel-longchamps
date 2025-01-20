<template>
    <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 bg-gray-800 p-4 rounded-lg shadow-lg">
      <!-- 🔍 Label de filtrage -->
      <label class="text-sm text-gray-300 font-semibold">Filtrer par :</label>
  
      <!-- 🎛 Sélection du filtre -->
      <select v-model="localFilter" @change="updateFilters" class="filter-dropdown">
        <option value="status">Statut</option>
        <option value="renter">Locataire</option>
        <option value="month_year">Mois et Année</option>
      </select>
  
      <!-- 🎯 Sélection dynamique en fonction du filtre -->
      <div v-if="localFilter === 'renter'" class="relative w-64">
        <select v-model="localQuery" @change="updateFilters" class="filter-input">
          <option value="">Tous les locataires</option>
          <option v-for="renter in renters" :value="renter.id" :key="renter.id">
            {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
          </option>
        </select>
      </div>
  
      <div v-else-if="localFilter === 'status'" class="relative w-64">
        <select v-model="localQuery" @change="updateFilters" class="filter-input">
          <option value="">Tous les statuts</option>
          <option value="pending">En attente</option>
          <option value="issued">Envoyée</option>
          <option value="paid">Payée</option>
        </select>
      </div>
  
      <!-- 📅 Filtre par période -->
      <div v-else class="relative w-64">
        <input v-model="localQuery" @input="updateFilters" type="month" class="filter-input" />
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  import { useRentersStore } from "@/stores/renters";
  import { storeToRefs } from "pinia";
  
  const props = defineProps({ selectedFilter: String, searchQuery: String });
  const emit = defineEmits(["updateFilter"]);
  
  const rentersStore = useRentersStore();
  const { renters } = storeToRefs(rentersStore);
  
  const localFilter = ref(props.selectedFilter || "status");
  const localQuery = ref(props.searchQuery || "");
  
  // Émettre les mises à jour des filtres
  const updateFilters = () => {
    emit("updateFilter", { filter: localFilter.value, query: localQuery.value });
  };
  
  // Synchronisation avec les props
  watch(() => props.selectedFilter, (newFilter) => { localFilter.value = newFilter; });
  watch(() => props.searchQuery, (newQuery) => { localQuery.value = newQuery; });
  </script>
  
  <style scoped>
  .filter-dropdown {
    @apply p-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 transition;
  }
  .filter-input {
    @apply p-2 border rounded-md bg-gray-900 text-white w-full focus:ring-2 focus:ring-indigo-500 transition;
  }
  </style>
  