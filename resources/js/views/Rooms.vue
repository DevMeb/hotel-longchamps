<template>
  <navbar></navbar>
  <div class="bg-gray-900 min-h-screen">
    <div class="mx-auto max-w-7xl py-10 px-4 sm:px-6 lg:px-8">
      <!-- 🏨 En-tête -->
      <div class="sm:flex sm:items-center justify-between">
        <div class="sm:flex-auto">
          <h1 class="text-lg font-semibold leading-6 text-white">Chambres</h1>
          <p class="mt-2 text-sm text-gray-300">
            Gérez les chambres de votre hôtel avec leurs noms et loyers.
          </p>
        </div>
        <button @click="openRoomModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-md">➕ Ajouter une chambre</button>
      </div>

      <!-- 🔎 Filtres de recherche -->
      <RoomFilters :selectedFilter="selectedFilter" :searchQuery="searchQuery" @updateFilter="updateFilter" />

      <!-- 📋 Liste des chambres -->
      <RoomList :rooms="filteredRooms" :loading="loading" :error="error" @edit="openRoomModal" @delete="confirmDeleteRoom" />

      <!-- 📝 Modale d'ajout/modification -->
      <RoomFormModal :show="showAddRoomModal" :room="selectedRoom" :isEditing="isEditing" @close="closeRoomModal" />

      <!-- ❌ Modale de suppression -->
      <RoomDeleteModal :show="showDeleteRoomModal" :room="roomToDelete" @cancel="showDeleteRoomModal = false" @confirm="performDeleteRoom" />
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { RoomList, RoomFormModal, RoomDeleteModal, RoomFilters } from '@/components/rooms/';
import { onMounted, ref, computed } from 'vue';
import { useRoomsStore } from '@/stores/rooms';
import { storeToRefs } from 'pinia';
import { useToast } from "vue-toastification";

const roomsStore = useRoomsStore();
const { rooms, error, loading } = storeToRefs(roomsStore);
const { fetchRooms, addRoom, updateRoom, deleteRoom } = roomsStore;

const showAddRoomModal = ref(false);
const isEditing = ref(false);
const selectedRoom = ref(null);

const showDeleteRoomModal = ref(false);
const roomToDelete = ref(null);

onMounted(() => {
  fetchRooms();
});

// 📌 Gestion de la modale d'ajout/modification
const openRoomModal = (room = null) => {
  selectedRoom.value = room;
  isEditing.value = !!room;
  showAddRoomModal.value = true;
};

const closeRoomModal = () => {
  showAddRoomModal.value = false;
  selectedRoom.value = null;
};

// 📌 Gestion de la modale de suppression
const confirmDeleteRoom = (room) => {
  roomToDelete.value = room;
  showDeleteRoomModal.value = true;
};

const performDeleteRoom = async () => {
  try {
    await deleteRoom(roomToDelete.value.id);
    useToast().success("Chambre supprimée avec succès.");
    // Mise à jour locale sans recharger l'API
    rooms.value = rooms.value.filter(r => r.id !== roomToDelete.value.id);
  } catch (err) {
    useToast().error("Une erreur est survenue lors de la suppression.");
  } finally {
    showDeleteRoomModal.value = false;
    roomToDelete.value = null;
  }
};

// 🔍 Gestion des filtres de recherche
const selectedFilter = ref("name");
const searchQuery = ref("");

const updateFilter = ({ filter, query }) => {
  selectedFilter.value = filter;
  searchQuery.value = query;
};

const filteredRooms = computed(() => {
  return rooms.value.filter(room => {
    const value = room[selectedFilter.value]?.toString().toLowerCase() || "";
    return value.includes(searchQuery.value.toLowerCase());
  });
});
</script>
