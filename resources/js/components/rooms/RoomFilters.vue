<template>
    <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 bg-gray-800 p-4 rounded-lg shadow-lg mt-4">
      <!-- 🔍 Label Filtrage -->
      <label class="text-sm text-gray-300 font-semibold">Filtrer par :</label>
  
      <!-- 🎛 Sélection du filtre -->
      <select v-model="localFilter" @change="updateFilters" class="filter-dropdown">
        <option value="id">Identifiant</option>
        <option value="name">Nom</option>
        <option value="rent">Loyer</option>
      </select>
  
      <!-- 🔎 Champ de recherche -->
      <div class="relative w-64">
        <input v-model="localQuery" @input="updateFilters" type="text" class="filter-input" placeholder="Rechercher..." />
        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  
  const props = defineProps({
    selectedFilter: String,
    searchQuery: String,
  });
  
  const emit = defineEmits(["updateFilter"]);
  
  // Variables locales pour éviter la latence dans la mise à jour des filtres
  const localFilter = ref(props.selectedFilter);
  const localQuery = ref(props.searchQuery);
  
  // Mettre à jour les filtres dès que l'utilisateur tape ou change l'option sélectionnée
  const updateFilters = () => {
    emit("updateFilter", { filter: localFilter.value, query: localQuery.value });
  };
  
  // Synchroniser les props avec les refs locales si elles changent
  watch(() => props.selectedFilter, (newFilter) => {
    localFilter.value = newFilter;
  });
  
  watch(() => props.searchQuery, (newQuery) => {
    localQuery.value = newQuery;
  });
  </script>
  
  <style scoped>
  /* Style du dropdown */
  .filter-dropdown {
    @apply p-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition;
  }
  
  /* Style du champ de recherche */
  .filter-input {
    @apply p-2 border rounded-md bg-gray-900 text-white w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition;
  }
  </style>
  