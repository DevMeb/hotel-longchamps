<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50">
      <!-- Overlay cliquable pour fermer la modale -->
      <div @click.self="closeModal" class="fixed inset-0"></div>

      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
          <!-- ✨ Titre de la modale -->
          <div class="flex items-center justify-between border-b pb-2">
              <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                  <span v-if="reservationData.id" class="mr-2">✏️</span>
                  <span v-else class="mr-2">➕</span>
                  {{ reservationData.id ? 'Éditer la réservation' : 'Ajouter une réservation' }}
              </h2>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
          </div>

          <!-- 📋 Formulaire -->
          <form @submit.prevent="submitForm" class="mt-4 space-y-4">
              <!-- Locataire -->
              <div>
                  <label for="renter_id" class="block text-sm font-medium text-gray-700">Locataire</label>
                  <select v-model="reservationData.renter_id"
                      class="input-field" 
                      :class="{ 'border-red-500': errors.validationErrors?.renter_id }">
                      <option value="">Sélectionner un locataire</option>
                      <option v-for="renter in renters" :value="renter.id" :key="renter.id">
                          {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
                      </option>
                  </select>
                  <p v-if="errors.validationErrors?.renter_id" class="error-message">{{ errors.validationErrors.renter_id.join(' ') }}</p>
              </div>

              <!-- Chambre -->
              <div>
                  <label for="room_id" class="block text-sm font-medium text-gray-700">Chambre</label>
                  <select v-model="reservationData.room_id"
                      class="input-field"
                      :class="{ 'border-red-500': errors.validationErrors?.room_id }">
                      <option value="">Sélectionner une chambre</option>
                      <option v-for="room in rooms" :value="room.id" :key="room.id">
                          {{ room.name }}
                      </option>
                  </select>
                  <p v-if="errors.validationErrors?.room_id" class="error-message">{{ errors.validationErrors.room_id.join(' ') }}</p>
              </div>

              <!-- Date de début -->
              <div>
                  <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                  <input type="date" v-model="reservationData.start_date" 
                      class="input-field"
                      :class="{ 'border-red-500': errors.validationErrors?.start_date }">
                  <p v-if="errors.validationErrors?.start_date" class="error-message">{{ errors.validationErrors.start_date.join(' ') }}</p>
              </div>

              <!-- Date de fin -->
              <div>
                  <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin (optionnelle)</label>
                  <input type="date" v-model="reservationData.end_date"
                      class="input-field"
                      :class="{ 'border-red-500': errors.validationErrors?.end_date }">
                  <p v-if="errors.validationErrors?.end_date" class="error-message">{{ errors.validationErrors.end_date.join(' ') }}</p>
              </div>

              <!-- Messages d'erreur spécifiques -->
              <p v-if="errors.validationErrors?.date_conflict" class="error-message">{{ errors.validationErrors.date_conflict[0] }}</p>
              <p v-if="errors.validationErrors?.renter_conflict" class="error-message">{{ errors.validationErrors.renter_conflict[0] }}</p>
              <p v-if="errors.validationErrors?.room_conflict" class="error-message">{{ errors.validationErrors.room_conflict[0] }}</p>

              <!-- ⚡️ Boutons d'action -->
              <div class="flex justify-end space-x-3 mt-4">
                  <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>

                  <!-- Bouton désactivé si en cours de soumission -->
                  <button type="submit" class="btn-primary flex items-center" :disabled="reservationData.id ? loading.update : loading.add">
                      <span v-if="reservationData.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
                      {{ reservationData.id ? 'Mettre à jour' : 'Ajouter' }}
                  </button>
              </div>
          </form>
      </div>
  </div>
</template>


<script setup>
import { ref, watchEffect, onMounted } from 'vue';
import { useReservationsStore } from '@/stores/reservations';
import { useRentersStore } from '@/stores/renters';
import { useRoomsStore } from '@/stores/rooms';
import { storeToRefs } from 'pinia';
import { formatDateToISO } from '@/utils/'

const props = defineProps({
  reservation: Object,
});

const emit = defineEmits(['close']);

const reservationsStore = useReservationsStore();
const { addReservation, updateReservation, clearErrors } = reservationsStore;
const { errors, loading } = storeToRefs(reservationsStore);

const rentersStore = useRentersStore();
const { renters } = storeToRefs(rentersStore);
const { fetchRenters } = rentersStore;

const roomsStore = useRoomsStore();
const { rooms } = storeToRefs(roomsStore);
const { fetchRooms } = roomsStore;

const reservationData = ref({
  id: null,
  renter_id: '',
  room_id: '',
  start_date: '',
  end_date: ''
});

onMounted(() => {
  fetchRenters();
  fetchRooms();
});

// Synchronisation des données avec les props
watchEffect(() => {
  reservationData.value = props.reservation
      ? {
          id: props.reservation.id,
          renter_id: props.reservation.renter?.id || '',
          room_id: props.reservation.room?.id || '',
          start_date: formatDateToISO(props.reservation.start_date) || '',
          end_date: formatDateToISO(props.reservation.end_date) || '',
      }
      : {
          id: null,
          renter_id: '',
          room_id: '',
          start_date: '',
          end_date: ''
      };
});

// Soumission du formulaire
const submitForm = async () => {
  const success = reservationData.value.id
      ? await updateReservation(reservationData.value)
      : await addReservation(reservationData.value);

  if (success) {
      closeModal();
  }
};

// Fermeture de la modale et réinitialisation des erreurs
const closeModal = () => {
  clearErrors('validationErrors'); // Réinitialiser uniquement les erreurs de validation
  emit("close");
};
</script>
