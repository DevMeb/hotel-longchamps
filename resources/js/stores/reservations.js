// src/stores/reservations.js
import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useReservationsStore = defineStore('reservations', () => {
  const reservations = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchReservations() {
    loading.value = true;
    try {
      const response = await axios.get('/reservations');
      reservations.value = response.data.data;

      // Vérifier si le contenu est du JSON
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue lors de la récupération des réservations.");
      }
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  async function addReservation(reservation) {
    try {
      const response = await axios.post('/reservations', reservation);
      reservations.value.push(response.data.data); // Ajouter la nouvelle réservation localement
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function updateReservation(reservation) {
    try {
      const response = await axios.put(`/reservations/${reservation.id}`, reservation);
      const index = reservations.value.findIndex(r => r.id === reservation.id);
      if (index !== -1) {
        reservations.value[index] = response.data.data; // Mettre à jour la réservation localement
      }
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function deleteReservation(reservationId) {
    try {
      const response = await axios.delete(`/reservations/${reservationId}`);
      reservations.value = reservations.value.filter(r => r.id !== reservationId); // Supprimer localement
      return response;
    } catch (err) {
      throw err;
    }
  }

  return { reservations, error, loading, fetchReservations, addReservation, updateReservation, deleteReservation };
});
