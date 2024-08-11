<template>
    <navbar></navbar>
    <div class="bg-gray-900">
      <div class="mx-auto max-w-7xl">
        <div class="bg-gray-900 py-10">
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-white">Locataires</h1>
                <p class="mt-2 text-sm text-gray-300">
                  Une liste de tous les locataires dans votre compte, y compris leur nom, prénom et tuteur.
                </p>
              </div>
              <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button
                  @click="showAddRenterModal = true; resetForm();"
                  type="button"
                  class="block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                >
                  Ajouter un locataire
                </button>
              </div>
            </div>
            
            <div class="mt-8 flow-root">
              <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                      <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Nom</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Prénom</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Tuteur</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Crée le</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Mis à jour le</th>
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
                      <tr v-else-if="renters?.length === 0 && !loading">
                        <td colspan="5" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center">Aucun locataire trouvé.</td>
                      </tr>
                      <tr v-else v-for="renter in renters" :key="renter.id">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">{{ renter.last_name.toUpperCase() }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ renter.first_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">
                            <span v-if="renter.tutor">{{ renter.tutor.last_name.toUpperCase() }} {{ renter.tutor.first_name }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ renter.created_at }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ renter.updated_at }}</td>
  
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                          <a @click="editRenter(renter)" href="#" class="text-indigo-400 hover:text-indigo-300">Éditer</a>
                          <a @click="confirmDeleteRenter(renter)" href="#" class="ml-4 text-red-400 hover:text-red-300">Supprimer</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
  
            <!-- Modal pour ajouter/mettre à jour un locataire -->
            <div v-if="showAddRenterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">{{ isEditing ? 'Éditer le locataire' : 'Ajouter un locataire' }}</h2>
                <form @submit.prevent="saveRenter">
                  <div>
                    <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">Nom</label>
                    <div :class="{'relative mt-2 rounded-md shadow-sm': true, 'ring-red-300 text-red-900': errors.last_name}">
                      <input type="text" v-model="newRenter.last_name" name="last_name" id="last_name" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" :aria-invalid="errors.last_name ? 'true' : 'false'" aria-describedby="last_name-error" />
                      <div v-if="errors.last_name" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
                      </div>
                    </div>
                    <p v-if="errors.last_name" class="mt-2 text-sm text-red-600" id="last_name-error">{{ errors.last_name.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">Prénom</label>
                    <div :class="{'relative mt-2 rounded-md shadow-sm': true, 'ring-red-300 text-red-900': errors.first_name}">
                      <input type="text" v-model="newRenter.first_name" name="first_name" id="first_name" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" :aria-invalid="errors.first_name ? 'true' : 'false'" aria-describedby="first_name-error" />
                      <div v-if="errors.first_name" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
                      </div>
                    </div>
                    <p v-if="errors.first_name" class="mt-2 text-sm text-red-600" id="first_name-error">{{ errors.first_name.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="tutor_id" class="block text-sm font-medium leading-6 text-gray-900">Tuteur</label>
                    <div :class="{'relative mt-2 rounded-md shadow-sm': true, 'ring-red-300 text-red-900': errors.tutor_id}">
                      <select v-model="newRenter.tutor_id" name="tutor_id" id="tutor_id" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        <option value="">Sélectionner un tuteur (optionnel)</option>
                        <option v-for="tutor in tutors" :value="tutor.id" :key="tutor.id">{{ tutor.first_name }} {{ tutor.last_name }}</option>
                      </select>
                      <div v-if="errors.tutor_id" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
                      </div>
                    </div>
                    <p v-if="errors.tutor_id" class="mt-2 text-sm text-red-600" id="tutor_id-error">{{ errors.tutor_id.join(' ') }}</p>
                  </div>
                  <div class="flex justify-end mt-4">
                    <button type="button" @click="showAddRenterModal = false; resetForm();" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
                  </div>
                </form>
              </div>
            </div>
  
            <!-- Modal de confirmation de suppression -->
            <div v-if="showDeleteRenterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Confirmation de suppression</h2>
                <p class="mb-4">Êtes-vous sûr de vouloir supprimer le locataire <strong>{{ renterToDelete?.last_name.toUpperCase() }} {{ renterToDelete?.first_name }}</strong> ?</p>
                <div class="flex justify-end">
                  <button type="button" @click="showDeleteRenterModal = false" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                  <button @click="performDeleteRenter" type="button" class="px-4 py-2 bg-red-500 text-white rounded-md">Supprimer</button>
                </div>
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
  import { useRentersStore } from '@/stores/renters';
  import { useTutorsStore } from '@/stores/tutors'; // Pour charger la liste des tuteurs
  import { storeToRefs } from 'pinia';
  import { useToast } from "vue-toastification";
  import { ExclamationCircleIcon } from '@heroicons/vue/16/solid';
  
  const rentersStore = useRentersStore();
  const { renters, error, loading } = storeToRefs(rentersStore);
  const { fetchRenters, addRenter, updateRenter, deleteRenter } = rentersStore;
  
  const tutorsStore = useTutorsStore();
  const { tutors } = storeToRefs(tutorsStore);
  
  const showAddRenterModal = ref(false);
  const showDeleteRenterModal = ref(false);
  const isEditing = ref(false);
  
  const newRenter = ref({
    id: null,
    last_name: '',
    first_name: '',
    tutor_id: null,
  });
  
  const renterToDelete = ref(null);
  
  const errors = ref({
    last_name: '',
    first_name: '',
    tutor_id: '',
  });
  
  const toast = useToast();
  
  onMounted(() => {
    fetchRenters();
    tutorsStore.fetchTutors(); // Charger les tuteurs pour les afficher dans le select
  });
  
  async function saveRenter() {
    try {
      if (isEditing.value) {
        const responseUpdateRenter = await updateRenter(newRenter.value);
        toast.success(responseUpdateRenter.data.message);
      } else {
        const responseAddRenter = await addRenter(newRenter.value);
        toast.success(responseAddRenter.data.message);
      }
      showAddRenterModal.value = false;
      resetForm();
      fetchRenters(); // Mettre à jour la liste des locataires après l'ajout ou la mise à jour
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors.value = err.response.data.errors; // Stocker les erreurs pour chaque champ
        toast.error("Des erreurs de validation ont été détectées. Veuillez vérifier les champs.");
      } else {
        toast.error("Une erreur est survenue depuis le serveur lors de l'enregistrement du locataire. Veuillez contacter votre administrateur.");
      }
    }
  }
  
  function editRenter(renter) {
    newRenter.value = { 
        id: renter.id, 
        last_name: renter.last_name, 
        first_name: renter.first_name, 
        tutor_id: renter.tutor ? renter.tutor.id : null // Présélectionner le tuteur si présent
    };
    isEditing.value = true;
    showAddRenterModal.value = true;
  }
  
  function confirmDeleteRenter(renter) {
    renterToDelete.value = renter;
    showDeleteRenterModal.value = true;
  }
  
  async function performDeleteRenter() {
    try {
      const responseDeleteRenter = await deleteRenter(renterToDelete.value.id);
      toast.success(responseDeleteRenter.data.message);
      showDeleteRenterModal.value = false;
      renterToDelete.value = null;
      fetchRenters();
    } catch (err) {
      toast.error("Une erreur est survenue depuis le serveur lors de la suppression du locataire. Veuillez contacter votre administrateur.");
    }
  }
  
  function resetForm() {
    newRenter.value = { id: null, last_name: '', first_name: '', tutor_id: null };
    errors.value = { last_name: '', first_name: '', tutor_id: '' };
    isEditing.value = false;
  }
  </script>
  