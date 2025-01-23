<template>
    <div class="mt-6">

      <!-- 🎯 Filtres -->
    <RentersFilters />

      <!-- 🟡 Chargement -->
      <div v-if="loading.fetch" class="flex items-center justify-center my-6 animate-pulse">
        <div class="flex items-center space-x-2 bg-gray-800 px-6 py-3 rounded-lg shadow-lg">
          <span class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></span>
          <p class="text-white text-lg font-medium">Chargement des locataires...</p>
        </div>
      </div>
      
      <!-- 🟥 Erreur -->
      <div v-else-if="errors.fetch" class="flex items-center justify-center my-6">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
          <span class="text-xl">❌</span>
          <p class="text-lg font-medium">Une erreur est survenue : {{ errors.fetch }}</p>
        </div>
      </div>
      
      <!-- 🟢 Aucun locataire trouvé -->
      <div v-else-if="filteredRenters.length === 0" class="flex flex-col items-center my-6">
        <div class="bg-gray-800 px-6 py-4 rounded-lg shadow-lg text-center">
          <p class="text-gray-300 text-lg">📭 Aucun locataire trouvé.</p>
          <p class="text-gray-400 text-sm mt-2">Ajoutez-en un pour commencer !</p>
        </div>
      </div>
  
      <!-- ✅ Affichage des cartes des locataires -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        <RenterListItem v-for="renter in filteredRenters" :renter="renter" :key="renter.id" />
      </div>
    </div>
  </template>
  
<script setup>
  import { RenterListItem, RentersFilters } from '@/components/renters/'
  import { useRentersStore } from '@/stores/renters';
  import { storeToRefs } from 'pinia';
  import { onMounted } from 'vue';

  const rentersStore = useRentersStore()
  const { filteredRenters, loading, errors, renters } = storeToRefs(rentersStore)
  const { fetchRenters } = rentersStore

  onMounted(() => {
    fetchRenters();
  });
</script>
  
<style scoped>
  /* Animation d'apparition */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-fade-in {
    animation: fadeIn 0.3s ease-out;
  }
</style>
  