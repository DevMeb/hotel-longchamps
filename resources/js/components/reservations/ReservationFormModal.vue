<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
        <!-- Overlay cliquable pour fermer la modale -->
        <div @click.self="closeModal" class="fixed inset-0"></div>

        <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
            <!-- ✨ Titre de la modale -->
            <div class="flex items-center justify-between border-b pb-2">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <span v-if="isEditing" class="mr-2">✏️</span>
                    <span v-else class="mr-2">➕</span>
                    {{ isEditing ? 'Éditer la réservation' : 'Ajouter une réservation' }}
                </h2>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
            </div>

            <!-- 📋 Formulaire -->
            <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                <!-- Locataire -->
                <div>
                    <label for="renter_id" class="block text-sm font-medium text-gray-700">Locataire</label>
                    <select v-model="reservationData.renter_id" class="input-field">
                        <option value="">Sélectionner un locataire</option>
                        <option v-for="renter in renters" :value="renter.id" :key="renter.id">
                            {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
                        </option>
                    </select>
                    <p v-if="errors.renter_id" class="error-message">{{ errors.renter_id?.join(' ') }}</p>
                </div>

                <!-- Chambre -->
                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700">Chambre</label>
                    <select v-model="reservationData.room_id" class="input-field">
                        <option value="">Sélectionner une chambre</option>
                        <option v-for="room in rooms" :value="room.id" :key="room.id">
                            {{ room.name }}
                        </option>
                    </select>
                    <p v-if="errors.room_id" class="error-message">{{ errors.room_id?.join(' ') }}</p>
                </div>

                <!-- Date de début -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" v-model="reservationData.start_date" class="input-field">
                    <p v-if="errors.start_date" class="error-message">{{ errors.start_date?.join(' ') }}</p>
                </div>

                <!-- Date de fin -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin (optionnelle)</label>
                    <input type="date" v-model="reservationData.end_date" class="input-field">
                    <p v-if="errors.end_date" class="error-message">{{ errors.end_date?.join(' ') }}</p>
                </div>

                <!-- Messages d'erreur spécifiques -->
                <p v-if="errors.date_conflict" class="error-message">{{ errors.date_conflict[0] }}</p>
                <p v-if="errors.renter_conflict" class="error-message">{{ errors.renter_conflict[0] }}</p>
                <p v-if="errors.room_conflict" class="error-message">{{ errors.room_conflict[0] }}</p>

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
  import { useReservationsStore } from '@/stores/reservations';
  import { useRentersStore } from '@/stores/renters';
  import { useRoomsStore } from '@/stores/rooms';
  import { useToast } from 'vue-toastification';
  import { storeToRefs } from 'pinia';
  
  const props = defineProps({
    show: Boolean,
    reservation: Object,
    isEditing: Boolean
  });
  
  const emit = defineEmits(['close']);
  
  const reservationsStore = useReservationsStore();
  const { addReservation, updateReservation } = reservationsStore;
  
  const rentersStore = useRentersStore();
  const { renters } = storeToRefs(rentersStore);
  
  const roomsStore = useRoomsStore();
  const { rooms } = storeToRefs(roomsStore);
  
  const toast = useToast();
  
  const reservationData = ref({ id: null, renter_id: '', room_id: '', start_date: '', end_date: '' });
  const errors = ref({});
  const isSubmitting = ref(false);
  
  // ✅ Charger la liste des locataires et chambres avant ouverture du formulaire
  watch(() => props.show, async (isOpen) => {
    if (isOpen) {
      await rentersStore.fetchRenters();
      await roomsStore.fetchRooms();
    }
  });
  
  // ✅ Mise à jour automatique des données du formulaire
  watch(() => props.reservation, (newReservation) => {
    if (newReservation && props.isEditing) {
      reservationData.value = { 
        ...newReservation, 
        room_id: newReservation.room ? newReservation.room.id : '',
        renter_id: newReservation.renter ? newReservation.renter.id : '',
        start_date: formatDateDMYtoYMD(newReservation.start_date),
        end_date: formatDateDMYtoYMD(newReservation.end_date)
      };
    } else {
      resetForm();
    }
  }, { immediate: true, deep: true });
  
  // ✅ Ferme la modale en appuyant sur `Échap`
  onMounted(() => {
    window.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && props.show) {
        closeModal();
      }
    });
  });
  
  // 📌 Soumission du formulaire
  const submitForm = async () => {
    try {
      isSubmitting.value = true;
      
      if (props.isEditing) {
        await updateReservation(reservationData.value);
        toast.success('Réservation mise à jour avec succès.');
      } else {
        await addReservation(reservationData.value);
        toast.success('Réservation ajoutée avec succès.');
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
  
  // 📌 Fermer la modale
  const closeModal = () => {
    resetForm();
    emit('close');
  };
  
  // 📌 Réinitialisation du formulaire
  function resetForm() {
    reservationData.value = { id: null, renter_id: '', room_id: '', start_date: '', end_date: '' };
    errors.value = {};
  };

  function formatDateDMYtoYMD(dateString) {
    if (!dateString) return '';

    const [day, month, year] = dateString.split('/');
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
  }
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
  