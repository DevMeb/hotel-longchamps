<template>
    <div class="mt-6">
      <!-- 🟡 Chargement -->
      <div v-if="loading" class="flex justify-center my-6">
        <div class="flex items-center space-x-2 bg-gray-800 px-6 py-3 rounded-lg shadow-lg animate-pulse">
          <span class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></span>
          <p class="text-white text-lg font-medium">Chargement des chambres...</p>
        </div>
      </div>
  
      <!-- 🟥 Erreur -->
      <div v-else-if="error" class="flex justify-center my-6">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
          <span class="text-xl">❌</span>
          <p class="text-lg font-medium">Une erreur est survenue : {{ error }}</p>
        </div>
      </div>
  
      <!-- 🟢 Aucune chambre trouvée -->
      <div v-else-if="rooms.length === 0" class="flex justify-center my-6">
        <div class="bg-gray-800 px-6 py-4 rounded-lg shadow-lg text-center">
          <p class="text-gray-300 text-lg">📭 Aucune chambre trouvée.</p>
          <p class="text-gray-400 text-sm mt-2">Ajoutez-en une pour commencer !</p>
        </div>
      </div>
  
      <!-- 📋 Liste des chambres -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="room in rooms" 
          :key="room.id" 
          class="bg-gray-800 p-5 rounded-lg shadow-lg flex flex-col justify-between hover:shadow-xl transition-all transform hover:scale-[1.02] animate-fade-in"
        >
          <div>
            <h2 class="text-lg font-semibold text-white flex items-center">
              🏨 <span class="ml-2">{{ room.name }}</span>
            </h2>
            <p class="text-gray-400 text-sm mt-1">💰 <strong>Loyer :</strong> {{ room.rent }} €</p>
            <p class="text-gray-400 text-sm mt-1">🕒 <strong>Ajoutée le :</strong> {{ room.created_at }}</p>
            <p class="text-gray-400 text-sm">✏️ <strong>Mis à jour le :</strong> {{ room.updated_at }}</p>
          </div>
          
          <!-- 📌 Actions -->
          <div class="mt-4 flex justify-between space-x-2">
            <button 
              @click="editRoom(room)"
              class="px-3 py-1 bg-indigo-500 text-white text-sm rounded-md hover:bg-indigo-400 flex items-center transition-all"
            >
              ✏️ <span class="ml-1">Modifier</span>
            </button>
            <button 
              @click="confirmDeleteRoom(room)"
              class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-400 flex items-center transition-all"
            >
              🗑️ <span class="ml-1">Supprimer</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    rooms: Array,
    loading: Boolean,
    error: String
  });
  
  const emit = defineEmits(["edit", "delete"]);
  
  const editRoom = (room) => {
    emit("edit", room);
  };
  
  const confirmDeleteRoom = (room) => {
    emit("delete", room);
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
  