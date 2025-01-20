<template>
    <div>
      <!-- 🟡 Chargement -->
      <div v-if="loading" class="flex items-center justify-center my-6 animate-pulse">
        <div class="flex items-center space-x-2 bg-gray-800 px-6 py-3 rounded-lg shadow-lg">
          <span class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></span>
          <p class="text-white text-lg font-medium">Chargement des locataires...</p>
        </div>
      </div>
      
      <!-- 🟥 Erreur -->
      <div v-else-if="error" class="flex items-center justify-center my-6">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
          <span class="text-xl">❌</span>
          <p class="text-lg font-medium">Une erreur est survenue : {{ error }}</p>
        </div>
      </div>
      
      <!-- 🟢 Aucun locataire trouvé -->
      <div v-else-if="renters.length === 0" class="flex flex-col items-center my-6">
        <div class="bg-gray-800 px-6 py-4 rounded-lg shadow-lg text-center">
          <p class="text-gray-300 text-lg">📭 Aucun locataire trouvé.</p>
          <p class="text-gray-400 text-sm mt-2">Ajoutez-en un pour commencer !</p>
        </div>
      </div>
  
      <!-- ✅ Affichage des cartes des locataires -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="renter in renters" 
          :key="renter.id" 
          class="bg-gray-800 p-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] animate-fade-in border border-gray-700"
        >
          <!-- 🏠 En-tête avec icône et nom -->
          <div class="flex items-center space-x-3 mb-3">
            <div class="bg-gray-700 text-white p-2 rounded-full flex items-center justify-center w-12 h-12">
              🏠
            </div>
            <div>
              <h2 class="text-lg font-semibold text-white">{{ renter.last_name.toUpperCase() }} {{ renter.first_name }}</h2>
            </div>
          </div>
          
          <!-- 📌 Informations du locataire -->
          <div class="border-t border-gray-600 my-3"></div>
          
          <div class="flex flex-col space-y-2">
            <p class="text-gray-400 text-sm flex items-center">
              <span class="mr-2">👨‍👩‍👦</span> <strong>Tuteur :</strong> 
              <span v-if="renter.tutor" class="text-indigo-400 font-semibold ml-1">
                {{ renter.tutor.last_name.toUpperCase() }} {{ renter.tutor.first_name }}
              </span>
              <span v-else class="text-gray-500">Aucun</span>
            </p>
            <p class="text-gray-400 text-sm flex items-center">
              <span class="mr-2">🕒</span> Ajouté le <span class="font-semibold ml-1">{{ renter.created_at }}</span>
            </p>
            <p class="text-gray-400 text-sm flex items-center">
              <span class="mr-2">✏️</span> Modifié le <span class="font-semibold ml-1">{{ renter.updated_at }}</span>
            </p>
          </div>
          
          <!-- Actions -->
          <div class="mt-4 flex justify-between">
            <button 
              @click="editRenter(renter)"
              class="px-4 py-2 bg-indigo-500 text-white text-sm rounded-md hover:bg-indigo-400 flex items-center transition-all duration-200"
            >
              ✏️ <span class="ml-2">Modifier</span>
            </button>
            <button 
              @click="confirmDeleteRenter(renter)"
              class="px-4 py-2 bg-red-500 text-white text-sm rounded-md hover:bg-red-400 flex items-center transition-all duration-200"
            >
              🗑️ <span class="ml-2">Supprimer</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  defineProps({
    renters: Array,
    loading: Boolean,
    error: String
  });
  
  const emit = defineEmits(['edit', 'delete']);
  
  const editRenter = (renter) => {
    emit('edit', renter);
  };
  
  const confirmDeleteRenter = (renter) => {
    emit('delete', renter);
  };
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
  