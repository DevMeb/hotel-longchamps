<template>
  <div class="mt-6">
    <!-- 🔄 Chargement -->
    <div v-if="loading" class="flex justify-center my-6">
      <div class="flex items-center space-x-2 bg-gray-800 px-6 py-3 rounded-lg shadow-lg animate-pulse">
        <span class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></span>
        <p class="text-white text-lg font-medium">Chargement des réservations...</p>
      </div>
    </div>

    <!-- ❌ Erreur -->
    <div v-else-if="error" class="flex justify-center my-6">
      <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
        <span class="text-xl">❌</span>
        <p class="text-lg font-medium">Une erreur est survenue : {{ error }}</p>
      </div>
    </div>

    <!-- 📭 Aucune réservation trouvée -->
    <div v-else-if="reservations.length === 0" class="flex justify-center my-6">
      <div class="bg-gray-800 px-6 py-4 rounded-lg shadow-lg text-center">
        <p class="text-gray-300 text-lg">📭 Aucune réservation trouvée.</p>
        <p class="text-gray-400 text-sm mt-2">Ajoutez-en une pour commencer !</p>
      </div>
    </div>

    <!-- ✅ Liste des réservations -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="reservation in reservations" 
        :key="reservation.id" 
        class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col justify-between transform transition-transform hover:scale-105 animate-fade-in"
      >
        <div>
          <h2 class="text-lg font-semibold text-white flex items-center">
            🏨 {{ reservation.room.name }}
          </h2>
          <p class="text-gray-400 text-sm mt-1">👤 Locataire : 
            <span class="text-indigo-400">{{ reservation.renter.last_name.toUpperCase() }} {{ reservation.renter.first_name }}</span>
          </p>
          <p class="text-gray-400 text-sm mt-1">📅 Début : {{ reservation.start_date }}</p>
          <p class="text-gray-400 text-sm">📅 Fin : {{ reservation.end_date || 'En cours' }}</p>
        </div>
        
        <!-- 🎯 Actions -->
        <div class="mt-4 flex justify-between space-x-2">
          <button 
            @click="editReservation(reservation)"
            class="px-3 py-1 bg-indigo-500 text-white text-sm rounded-md hover:bg-indigo-400 flex items-center"
          >
            ✏️ <span class="ml-1">Modifier</span>
          </button>

          <button 
            @click="confirmDeleteReservation(reservation)"
            class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-400 flex items-center"
          >
            🗑️ <span class="ml-1">Supprimer</span>
          </button>

          <button 
            @click="createInvoice(reservation)"
            class="px-3 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-400 flex items-center"
          >
            🧾 <span class="ml-1">Créer facture</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  reservations: Array,
  loading: Boolean,
  error: String,
});

const emit = defineEmits(["edit", "delete", "createInvoice"]);

const editReservation = (reservation) => {
  emit("edit", reservation);
};

const confirmDeleteReservation = (reservation) => {
  emit("delete", reservation);
};

// ✅ Émettre un événement pour ouvrir la modale de création de facture
const createInvoice = (reservation) => {
  emit("createInvoice", reservation);
};
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
