<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
      <div @click.self="closeModal" class="fixed inset-0"></div>
  
      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
        <h2 class="text-xl font-semibold text-gray-800">❌ Confirmation de suppression</h2>
        <p class="mt-4 text-gray-600">
          Êtes-vous sûr de vouloir supprimer la réservation de 
          <strong>{{ reservation?.renter?.last_name.toUpperCase() }} {{ reservation?.renter?.first_name }}</strong>
          pour la chambre <strong>{{ reservation?.room?.name }}</strong> ?
        </p>
  
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          <button @click="confirmDelete" class="btn-danger">Supprimer</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  const props = defineProps({ show: Boolean, reservation: Object });
  const emit = defineEmits(['cancel', 'delete']);
  
  const closeModal = () => {
    emit('cancel');
  };
  
  const confirmDelete = () => {
    emit('delete', props.reservation.id);
  };
  </script>
  
  <style scoped>
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
  .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
  .btn-secondary { @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition; }
  .btn-danger { @apply px-4 py-2 bg-red-500 text-white rounded-md font-semibold hover:bg-red-400 transition; }
  </style>
  