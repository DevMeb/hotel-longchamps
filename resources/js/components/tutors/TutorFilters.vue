<template>
  <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 bg-gray-800 p-4 rounded-lg shadow-md border border-gray-700 mt-4">
    <!-- 🔍 Label Filtrage -->
    <div class="flex items-center space-x-2">
      <span class="text-gray-300 text-sm font-semibold">Filtrer par :</span>
      <!-- 🎛 Sélection du filtre -->
      <select v-model="localFilter" @change="updateFilters" class="filter-dropdown">
        <option value="id">Identifiant</option>
        <option value="last_name">Nom</option>
        <option value="first_name">Prénom</option>
        <option value="email">Email</option>
      </select>
    </div>

    <!-- 🎚️ Recherche dynamique selon le filtre sélectionné -->
    <div v-if="localFilter !== 'tutor'" class="relative w-full sm:w-64">
      <input 
        v-model="localQuery" 
        @input="updateFilters" 
        type="text" 
        class="filter-input" 
        placeholder="Rechercher..."
      />
      <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
        🔍
      </span>
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
    @apply px-3 py-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none;
  }
  
  /* Style du champ de recherche */
  .filter-input {
    @apply px-3 py-2 border rounded-md bg-gray-900 text-white w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none;
  }
  
  /* Ajout d'un léger effet au survol */
  .filter-dropdown:hover,
  .filter-input:hover {
    @apply border-indigo-400;
  }
  </style>
  