<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
      <!-- Overlay cliquable pour fermer la modale -->
      <div @click.self="cancelDelete" class="fixed inset-0"></div>
  
      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
        <!-- ⚠️ Titre de la modale avec icône -->
        <div class="flex items-center justify-between border-b pb-2">
          <h2 class="text-xl font-semibold text-red-600 flex items-center">
            ⚠️ Confirmation de suppression
          </h2>
          <button @click="cancelDelete" class="text-gray-500 hover:text-gray-700 transition">
            ✖️
          </button>
        </div>
  
        <!-- 📝 Message d'avertissement -->
        <p class="text-gray-700 mt-4 text-lg text-center">
          Êtes-vous sûr de vouloir supprimer <br> 
          <strong class="text-gray-900">{{ renter?.last_name.toUpperCase() }} {{ renter?.first_name }}</strong> ?
        </p>
        <p class="text-sm text-gray-500 text-center mt-2">Cette action est <span class="font-semibold text-red-600">irréversible</span>.</p>
  
        <!-- ⚡️ Boutons d'action -->
        <div class="flex justify-center space-x-4 mt-6">
          <button @click="cancelDelete" class="btn-secondary">Annuler</button>
          <button @click="confirmDelete" class="btn-danger flex items-center">
            <span class="mr-2">🗑️</span> Supprimer
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    show: Boolean,
    renter: Object,
  });
  
  const emit = defineEmits(["confirm", "cancel"]);
  
  const cancelDelete = () => {
    emit("cancel");
  };
  
  const confirmDelete = () => {
    emit("confirm");
  };
  </script>
  
  <style scoped>
  /* Animation d'apparition de la modale */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  .animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
  }
  
  /* Bouton principal */
  .btn-danger {
    @apply px-4 py-2 bg-red-500 text-white rounded-md font-semibold hover:bg-red-600 transition;
  }
  
  /* Bouton secondaire */
  .btn-secondary {
    @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
  }
  </style>
  