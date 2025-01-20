<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
      <!-- Overlay cliquable pour fermer la modale -->
      <div @click.self="closeModal" class="fixed inset-0"></div>
  
      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
        <!-- ✨ Titre de la modale avec icône -->
        <div class="flex items-center justify-between border-b pb-2">
          <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            <span v-if="isEditing" class="mr-2">✏️</span>
            <span v-else class="mr-2">➕</span>
            {{ isEditing ? 'Éditer la chambre' : 'Ajouter une chambre' }}
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">
            ✖️
          </button>
        </div>
  
        <!-- 📋 Formulaire -->
        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <!-- Nom de la chambre -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nom de la chambre</label>
            <input ref="firstInput" type="text" v-model="roomData.name"
              class="input-field" :class="{ 'border-red-500': errors.name }">
            <p v-if="errors.name" class="error-message">{{ errors.name?.join(' ') }}</p>
          </div>
  
          <!-- Loyer -->
          <div>
            <label for="rent" class="block text-sm font-medium text-gray-700">Loyer (en €)</label>
            <input type="number" v-model="roomData.rent"
              class="input-field" :class="{ 'border-red-500': errors.rent }">
            <p v-if="errors.rent" class="error-message">{{ errors.rent?.join(' ') }}</p>
          </div>
  
          <!-- ⚡️ Boutons d'action -->
          <div class="flex justify-end space-x-3 mt-4">
            <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
            
            <!-- Bouton désactivé si en cours de soumission -->
            <button type="submit" class="btn-primary flex items-center" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="animate-spin mr-2">⏳</span>
              {{ isEditing ? 'Mettre à jour' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
  import { useRoomsStore } from '@/stores/rooms';
  import { useToast } from 'vue-toastification';
  
  const props = defineProps({
    show: Boolean,
    room: Object,
    isEditing: Boolean
  });
  
  const emit = defineEmits(['close']);
  const roomsStore = useRoomsStore();
  const { addRoom, updateRoom } = roomsStore;
  const toast = useToast();
  
  const roomData = ref({ id: null, name: '', rent: '' });
  const errors = ref({});
  const isSubmitting = ref(false);
  const firstInput = ref(null);
  
  // ✅ Mise à jour automatique des données du formulaire
  watch(() => props.room, (newRoom) => {
    if (newRoom && props.isEditing) {
      roomData.value = { ...newRoom };
    } else {
      resetForm();
    }
  }, { immediate: true, deep: true });
  
  // 🔹 Focus automatique sur le premier champ lors de l'ouverture de la modale
  watch(() => props.show, (isOpen) => {
    if (isOpen) {
      setTimeout(() => firstInput.value?.focus(), 100);
    }
  });
  
  // ✅ Ferme la modale en appuyant sur `Échap`
  onMounted(() => {
    window.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && props.show) {
        closeModal();
      }
    });
  });
  
  const submitForm = async () => {
    try {
      isSubmitting.value = true;
      
      if (props.isEditing) {
        await updateRoom(roomData.value);
        toast.success('Chambre mise à jour avec succès.');
      } else {
        await addRoom(roomData.value);
        toast.success('Chambre ajoutée avec succès.');
      }
  
      closeModal();
    } catch (err) {
      if (err.response?.data?.errors) {
        errors.value = err.response.data.errors;
        toast.error("Des erreurs de validation ont été détectées.");
      } else {
        toast.error("Une erreur est survenue.");
      }
    } finally {
      isSubmitting.value = false;
    }
  };
  
  const closeModal = () => {
    resetForm();
    emit('close');
  };
  
  function resetForm() {
    roomData.value = { id: null, name: '', rent: '' };
    errors.value = {};
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
  
  /* Styles des champs de saisie */
  .input-field {
    @apply block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
  }
  
  /* Messages d'erreur */
  .error-message {
    @apply mt-2 text-sm text-red-600;
  }
  
  /* Bouton principal */
  .btn-primary {
    @apply px-4 py-2 bg-indigo-500 text-white rounded-md font-semibold hover:bg-indigo-400 transition disabled:opacity-50 disabled:cursor-not-allowed;
  }
  
  /* Bouton secondaire */
  .btn-secondary {
    @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
  }
  </style>
  