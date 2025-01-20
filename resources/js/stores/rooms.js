import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useRoomsStore = defineStore('rooms', () => {
  const rooms = ref([]);
  const error = ref(null);
  const loading = ref(false);

  // 🛑 Récupération des chambres depuis l'API (appel unique)
  async function fetchRooms() {
    loading.value = true;
    try {
      const response = await axios.get('api/rooms');
      rooms.value = response.data.data;
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  // ✅ Ajout d'une chambre **sans recharger l'API**
  async function addRoom(room) {
    try {
      const response = await axios.post('api/rooms', room);
      rooms.value.push(response.data.data); // Ajout local immédiat
      return response;
    } catch (err) {
      throw err;
    }
  }

  // ✅ Mise à jour d'une chambre **localement**
  async function updateRoom(updatedRoom) {
    try {
      const response = await axios.put(`api/rooms/${updatedRoom.id}`, updatedRoom);
      const index = rooms.value.findIndex(room => room.id === updatedRoom.id);
      if (index !== -1) {
        rooms.value[index] = response.data.data; // Mise à jour locale
      }
      return response;
    } catch (err) {
      throw err;
    }
  }

  // ✅ Suppression d'une chambre **localement**
  async function deleteRoom(roomId) {
    try {
      await axios.delete(`api/rooms/${roomId}`);
      rooms.value = rooms.value.filter(room => room.id !== roomId); // Suppression locale immédiate
    } catch (err) {
      throw err;
    }
  }

  return { rooms, error, loading, fetchRooms, addRoom, updateRoom, deleteRoom };
});
