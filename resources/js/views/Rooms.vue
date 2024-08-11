<template>
    <navbar></navbar>
    <div class="bg-gray-900">
      <div class="mx-auto max-w-7xl">
        <div class="bg-gray-900 py-10">
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-white">Chambres</h1>
                <p class="mt-2 text-sm text-gray-300">
                  Une liste de toutes les chambres de votre hôtel, y compris leur nom et loyer.
                </p>
              </div>
              <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button
                  @click="showAddRoomModal = true; resetForm();"
                  type="button"
                  class="block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                >
                  Ajouter une chambre
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
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Loyer (en €)</th>
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
                      <tr v-else-if="rooms?.length === 0 && !loading">
                        <td colspan="5" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center">Aucune chambre trouvée.</td>
                      </tr>
                      <tr v-else v-for="room in rooms" :key="room.id">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">{{ room.name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ room.rent }} €</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ room.created_at }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ room.updated_at }}</td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                          <a @click="editRoom(room)" href="#" class="text-indigo-400 hover:text-indigo-300">Éditer</a>
                          <a @click="confirmDeleteRoom(room)" href="#" class="ml-4 text-red-400 hover:text-red-300">Supprimer</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
  
            <!-- Modal pour ajouter/mettre à jour une chambre -->
            <div v-if="showAddRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">{{ isEditing ? 'Éditer la chambre' : 'Ajouter une chambre' }}</h2>
                <form @submit.prevent="saveRoom">
                  <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom</label>
                    <div :class="{'relative mt-2 rounded-md shadow-sm': true, 'ring-red-300 text-red-900': errors.name}">
                      <input type="text" v-model="newRoom.name" name="name" id="name" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" :aria-invalid="errors.name ? 'true' : 'false'" aria-describedby="name-error" />
                      <div v-if="errors.name" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
                      </div>
                    </div>
                    <p v-if="errors.name" class="mt-2 text-sm text-red-600" id="name-error">{{ errors.name.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="rent" class="block text-sm font-medium leading-6 text-gray-900">Loyer (en €)</label>
                    <div :class="{'relative mt-2 rounded-md shadow-sm': true, 'ring-red-300 text-red-900': errors.rent}">
                      <input type="number" v-model="newRoom.rent" name="rent" id="rent" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" :aria-invalid="errors.rent ? 'true' : 'false'" aria-describedby="rent-error" />
                      <div v-if="errors.rent" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
                      </div>
                    </div>
                    <p v-if="errors.rent" class="mt-2 text-sm text-red-600" id="rent-error">{{ errors.rent.join(' ') }}</p>
                  </div>
                  <div class="flex justify-end mt-4">
                    <button type="button" @click="showAddRoomModal = false; resetForm();" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
                  </div>
                </form>
              </div>
            </div>
  
            <!-- Modal de confirmation de suppression -->
            <div v-if="showDeleteRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Confirmation de suppression</h2>
                <p class="mb-4">Êtes-vous sûr de vouloir supprimer la chambre <strong>{{ roomToDelete?.name }}</strong> ?</p>
                <div class="flex justify-end">
                  <button type="button" @click="showDeleteRoomModal = false" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                  <button @click="performDeleteRoom" type="button" class="px-4 py-2 bg-red-500 text-white rounded-md">Supprimer</button>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Navbar from '@/components/Navbar.vue'
  import { onMounted, ref } from 'vue'
  import { useRoomsStore } from '@/stores/rooms'
  import { storeToRefs } from 'pinia'
  import { useToast } from "vue-toastification"
  import { ExclamationCircleIcon } from '@heroicons/vue/16/solid'
  
  const roomsStore = useRoomsStore()
  const { rooms, error, loading } = storeToRefs(roomsStore)
  const { fetchRooms, addRoom, updateRoom, deleteRoom } = roomsStore
  
  const showAddRoomModal = ref(false)
  const showDeleteRoomModal = ref(false)
  const isEditing = ref(false)
  
  const newRoom = ref({
    id: null,
    name: '',
    rent: ''
  })
  
  const roomToDelete = ref(null)
  
  const errors = ref({
    name: '',
    rent: ''
  })
  
  const toast = useToast()
  
  onMounted(() => {
    fetchRooms()
  })
  
  async function saveRoom() {
    try {
      if (isEditing.value) {
        const responseUpdateRoom = await updateRoom(newRoom.value)
        toast.success(responseUpdateRoom.data.message)
      } else {
        const responseAddRoom = await addRoom(newRoom.value)
        toast.success(responseAddRoom.data.message)
      }
      showAddRoomModal.value = false
      resetForm()
      fetchRooms() // Mettre à jour la liste des chambres après l'ajout ou la mise à jour
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors.value = err.response.data.errors; // Stocker les erreurs pour chaque champ
        toast.error("Des erreurs de validation ont été détectées. Veuillez vérifier les champs.")
      } else {
        toast.error("Une erreur est survenue depuis le serveur lors de l'enregistrement de la chambre. Veuillez contacter votre administrateur.")
      }
    }
  }
  
  function editRoom(room) {
    newRoom.value = { ...room }
    isEditing.value = true
    showAddRoomModal.value = true
  }
  
  function confirmDeleteRoom(room) {
    roomToDelete.value = room
    showDeleteRoomModal.value = true
  }
  
  async function performDeleteRoom() {
    try {
      const responseDeleteRoom = await deleteRoom(roomToDelete.value.id)
      toast.success(responseDeleteRoom.data.message)
      showDeleteRoomModal.value = false
      roomToDelete.value = null
      fetchRooms()
    } catch (err) {
      toast.error("Une erreur est survenue depuis le serveur lors de la suppression de la chambre. Veuillez contacter votre administrateur.")
    }
  }
  
  function resetForm() {
    newRoom.value = { id: null, name: '', rent: '' }
    errors.value = { name: '', rent: '' }
    isEditing.value = false
  }
  </script>
  