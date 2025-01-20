<template>
    <div class="mt-4 flex flex-col sm:flex-row items-center gap-3 sm:gap-4 animate-fade-in">
      <!-- 🏷️ Label -->
      <label class="text-sm text-gray-300 flex items-center">
        🎯 Filtrer par :
      </label>
  
      <!-- 📂 Sélecteur -->
      <div class="relative w-full sm:w-auto">
        <select 
          v-model="localFilter" 
          @change="updateFilters" 
          class="w-full sm:w-auto p-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer"
        >
          <option value="id">Identifiant</option>
          <option value="last_name">Nom</option>
          <option value="first_name">Prénom</option>
          <option value="email">Email</option>
        </select>
      </div>
  
      <!-- 🔍 Champ de recherche -->
      <div class="relative w-full sm:w-64">
        <span class="absolute inset-y-0 left-2 flex items-center text-gray-400">🔍</span>
        <input
          v-model="localQuery"
          @input="updateFilters"
          type="text"
          placeholder="Rechercher un tuteur..."
          class="w-full p-2 pl-8 border rounded-md bg-gray-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
        />
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
  /* 🔥 Animation d'apparition fluide */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-5px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
  }
  </style>
  