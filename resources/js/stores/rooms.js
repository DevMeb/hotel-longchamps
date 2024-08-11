// src/stores/rooms.js
import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useRoomsStore = defineStore('rooms', () => {
  const rooms = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchRooms() {
    loading.value = true;
    try {
      const response = await axios.get('/rooms');
      rooms.value = response.data.data;

      // Pour les méthodes GET, Laravel renvoie un code 200 avec du HTML au lieu d'un JSON si l'endpoint est incorrect.
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue depuis le serveur lors de la récupération des chambres. Veuillez contacter votre administrateur.");
      }

    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  async function addRoom(room) {
    try {
      return await axios.post('/rooms', room);
    } catch (err) {
      throw err;
    }
  }

  async function updateRoom(room) {
    try {
      return await axios.put(`/rooms/${room.id}`, room);
    } catch (err) {
      throw err;
    }
  }

  async function deleteRoom(roomId) {
    try {
      return await axios.delete(`/rooms/${roomId}`);
    } catch (err) {
      throw err;
    }
  }

  return { rooms, error, loading, fetchRooms, addRoom, updateRoom, deleteRoom };
});
