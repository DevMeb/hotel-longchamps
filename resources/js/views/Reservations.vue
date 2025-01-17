<template>
    <navbar></navbar>
    <div class="bg-gray-900">
      <div class="mx-auto max-w-7xl">
        <div class="bg-gray-900 py-10">
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-white">Réservations</h1>
                <p class="mt-2 text-sm text-gray-300">
                  Liste de toutes les réservations, y compris les locataires, chambres, et dates.
                </p>
              </div>
              <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button
                  @click="showAddReservationModal = true; resetForm();"
                  type="button"
                  class="block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                >
                  Ajouter une réservation
                </button>
              </div>
            </div>
  
            <div class="mt-8 flow-root">
              <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                      <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Locataire</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Chambre</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date de début</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date de fin</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                          <span class="sr-only">Actions</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                      <tr v-if="loading">
                        <td colspan="5" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center">Chargement...</td>
                      </tr>
                      <tr v-if="error && !loading">
                        <td colspan="5" class="whitespace-nowrap py-4 text-sm text-red-500 text-center">{{ error }}</td>
                      </tr>
                      <tr v-else-if="reservations.length === 0 && !loading">
                        <td colspan="5" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center">Aucune réservation trouvée.</td>
                      </tr>
                      <tr v-else v-for="reservation in reservations" :key="reservation.id">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">
                          {{ reservation.renter.last_name.toUpperCase() }} {{ reservation.renter.first_name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ reservation.room.name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ reservation.start_date }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ reservation.end_date || 'En cours' }}</td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                          <a @click="handleAddInvoice(reservation)" href="#" class="text-indigo-400 hover:text-indigo-300">Créer une facture</a>
                          <a @click="editReservation(reservation)" href="#" class="ml-4 text-indigo-400 hover:text-indigo-300">Éditer</a>
                          <a @click="confirmDeleteReservation(reservation)" href="#" class="ml-4 text-red-400 hover:text-red-300">Supprimer</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
  
            <!-- Modal pour ajouter/mettre à jour une réservation -->
            <div v-if="showAddReservationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">{{ isEditing ? 'Éditer la réservation' : 'Ajouter une réservation' }}</h2>
                <form @submit.prevent="saveReservation">
                  <div>
                    <label for="renter_id" class="block text-sm font-medium leading-6 text-gray-900">Locataire</label>
                    <select v-model="newReservation.renter_id" id="renter_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                      <option value="">Sélectionner un locataire</option>
                      <option v-for="renter in renters" :key="renter.id" :value="renter.id">
                        {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
                      </option>
                    </select>
                    <p v-if="errors.renter_id" class="mt-2 text-sm text-red-600">{{ errors.renter_id.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="room_id" class="block text-sm font-medium leading-6 text-gray-900">Chambre</label>
                    <select v-model="newReservation.room_id" id="room_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                      <option value="">Sélectionner une chambre</option>
                      <option v-for="room in rooms" :key="room.id" :value="room.id">
                        {{ room.name }}
                      </option>
                    </select>
                    <p v-if="errors.room_id" class="mt-2 text-sm text-red-600">{{ errors.room_id.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900">Date de début</label>
                    <input type="date" v-model="newReservation.start_date" id="start_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.start_date" class="mt-2 text-sm text-red-600">{{ errors.start_date.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900">Date de fin (optionnelle)</label>
                    <input type="date" v-model="newReservation.end_date" id="end_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.end_date" class="mt-2 text-sm text-red-600">{{ errors.end_date.join(' ') }}</p>
                  </div>

                  <!-- Afficher le message d'erreur de conflit de date -->
                  <p v-if="errors.date_conflict" class="mt-2 text-sm text-red-600">{{ errors.date_conflict[0] }}</p>
                  <p v-if="errors.renter_conflict" class="mt-2 text-sm text-red-600">{{ errors.renter_conflict[0] }}</p>
                  <p v-if="errors.room_conflict" class="mt-2 text-sm text-red-600">{{ errors.room_conflict[0] }}</p>

                  
                  <div class="flex justify-end mt-4">
                    <button type="button" @click="showAddReservationModal = false; resetForm();" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
                  </div>
                </form>
              </div>
            </div>
  
            <!-- Modal de confirmation de suppression -->
            <div v-if="showDeleteReservationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Confirmation de suppression</h2>
                <p class="mb-4">Êtes-vous sûr de vouloir supprimer cette réservation ?</p>
                <div class="flex justify-end">
                  <button type="button" @click="showDeleteReservationModal = false" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                  <button @click="performDeleteReservation" type="button" class="px-4 py-2 bg-red-500 text-white rounded-md">Supprimer</button>
                </div>
              </div>
            </div>

            <!-- Modal pour ajouter une facture -->
            <div v-if="showAddInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Ajouter une facture</h2>
                <form @submit.prevent="saveInvoice">
                  <div>
                    <label for="subject" class="block text-sm font-medium leading-6 text-gray-900">Sujet</label>
                    <input type="text" v-model="newInvoice.subject" id="subject" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.subject" class="mt-2 text-sm text-red-600">{{ errors.subject.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="billing_start_date" class="block text-sm font-medium leading-6 text-gray-900">Date de début de facturation</label>
                    <input type="date" v-model="newInvoice.billing_start_date" id="billing_start_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.billing_start_date" class="mt-2 text-sm text-red-600">{{ errors.billing_start_date.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="billing_end_date" class="block text-sm font-medium leading-6 text-gray-900">Date de fin de facturation</label>
                    <input type="date" v-model="newInvoice.billing_end_date" id="billing_end_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.billing_end_date" class="mt-2 text-sm text-red-600">{{ errors.billing_end_date.join(' ') }}</p>
                  </div>
                  <div class="flex justify-end mt-4">
                    <button type="button" @click="showAddInvoiceModal = false; resetForm();" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
                  </div>
                </form>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Navbar from '@/components/Navbar.vue';
  import { onMounted, ref } from 'vue';
  import { useReservationsStore } from '@/stores/reservations';
  import { storeToRefs } from 'pinia';
  import { useToast } from 'vue-toastification';
  import { useRentersStore } from '@/stores/renters';
  import { useRoomsStore } from '@/stores/rooms';
  import { useInvoicesStore } from '@/stores/invoices';
  import { useRoute } from 'vue-router';

  const reservationsStore = useReservationsStore();
  const { reservations, error, loading } = storeToRefs(reservationsStore);
  const { fetchReservations, addReservation, updateReservation, deleteReservation } = reservationsStore;
  
  const rentersStore = useRentersStore();
  const { renters } = storeToRefs(rentersStore);
  const { fetchRenters } = rentersStore;
  
  const roomsStore = useRoomsStore();
  const { rooms } = storeToRefs(roomsStore);
  const { fetchRooms } = roomsStore;

  const route = useRoute()
  const selectedReservationId = ref(route.query.id || null);

  const invoicesStore = useInvoicesStore();
  const { addInvoice } = invoicesStore;
  
  const showAddReservationModal = ref(false);
  const showDeleteReservationModal = ref(false);
  const showAddInvoiceModal = ref(false); 
  const isEditing = ref(false);

  const newInvoice = ref({
    reservation_id: null,
    subject: '',
    billing_start_date: '',
    billing_end_date: '',
  });

  const handleAddInvoice = (reservation) => {

    newInvoice.value = {
      reservation_id: reservation.id,
      subject: '', // Réinitialisé
      billing_start_date: '', // Réinitialisé
      billing_end_date: '', // Réinitialisé
      status: 'pending', // Réinitialisé ou valeur par défaut
    };
    showAddInvoiceModal.value = true;
  }

  const saveInvoice = async () => {
    try {
      const responseAddInvoice = await addInvoice(newInvoice.value)
      toast.success(responseAddInvoice.data.message)
      showAddInvoiceModal.value = false;
    } catch(err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors.value = err.response.data.errors; // Stocker les erreurs pour chaque champ
        toast.error("Des erreurs de validation ont été détectées. Veuillez vérifier les champs.");
      } else {
        toast.error("Une erreur est survenue depuis le serveur lors de l'enregistrement de la réservation. Veuillez contacter votre administrateur.");
      }
    }
  }
  
  const newReservation = ref({
    id: null,
    renter_id: '',
    room_id: '',
    start_date: '',
    end_date: ''
  });
  
  const reservationToDelete = ref(null);
  
  const errors = ref({
    renter_id: '',
    room_id: '',
    start_date: '',
    end_date: '',
    subject: '',
    billing_start_date: '',
    billing_end_date: '',
    status: '',
  });
  
  const toast = useToast();
  
  onMounted(() => {
    fetchReservations(selectedReservationId.value);
    fetchRenters();
    fetchRooms();
  });
  
  async function saveReservation() {
    try {
      if (isEditing.value) {
        const responseUpdateReservation = await updateReservation(newReservation.value);
        toast.success(responseUpdateReservation.data.message);
      } else {
        const responseAddReservation = await addReservation(newReservation.value);
        toast.success(responseAddReservation.data.message);
      }
      showAddReservationModal.value = false;
      resetForm();
      fetchReservations(); // Mettre à jour la liste des réservations après l'ajout ou la mise à jour
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors.value = err.response.data.errors; // Stocker les erreurs pour chaque champ
        toast.error("Des erreurs de validation ont été détectées. Veuillez vérifier les champs.");
      } else {
        toast.error("Une erreur est survenue depuis le serveur lors de l'enregistrement de la réservation. Veuillez contacter votre administrateur.");
      }
    }
  }
  
    function formatDateForForm(dateString) {
        const [day, month, year] = dateString.split('/');
        return `${year}-${month}-${day}`;
    }

    function editReservation(reservation) {
        newReservation.value = {
            id: reservation.id,
            renter_id: reservation.renter.id,
            room_id: reservation.room.id,
            start_date: formatDateForForm(reservation.start_date), // Convertir au format 'Y-m-d'
            end_date: reservation.end_date ? formatDateForForm(reservation.end_date) : '' // Gérer le cas où end_date est null
        };
        isEditing.value = true;
        showAddReservationModal.value = true;
    }
  
  function confirmDeleteReservation(reservation) {
    reservationToDelete.value = reservation;
    showDeleteReservationModal.value = true;
  }
  
  async function performDeleteReservation() {
    try {
      const responseDeleteReservation = await deleteReservation(reservationToDelete.value.id);
      toast.success(responseDeleteReservation.data.message);
      showDeleteReservationModal.value = false;
      reservationToDelete.value = null;
      fetchReservations();
    } catch (err) {
      toast.error("Une erreur est survenue depuis le serveur lors de la suppression de la réservation. Veuillez contacter votre administrateur.");
    }
  }
  
  function resetForm() {
    newReservation.value = { id: null, renter_id: '', room_id: '', start_date: '', end_date: '' };
    errors.value = { renter_id: '', room_id: '', start_date: '', end_date: '' };
    isEditing.value = false;
  }
  </script>
  