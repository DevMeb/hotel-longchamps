import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useRentersStore = defineStore('renters', () => {
  const renters = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchRenters() {
    loading.value = true;
    try {
      const response = await axios.get('api/renters');
      renters.value = response.data.data;

      // For GET method Laravel return code 200 with HTML instead of 405.
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue depuis le serveur lors de la récupération des locataires. Veuillez contacter votre administrateur.");
      }

    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  async function addRenter(renter) {
    try {
      return await axios.post('api/renters', renter);
    } catch (err) {
      throw err;
    }
  }

  async function updateRenter(renter) {
    try {
      return await axios.put(`api/renters/${renter.id}`, renter);
    } catch (err) {
      throw err;
    }
  }

  async function deleteRenter(renterId) {
    try {
      return await axios.delete(`api/renters/${renterId}`);
    } catch (err) {
      throw err;
    }
  }

  return { renters, error, loading, fetchRenters, addRenter, updateRenter, deleteRenter };
});
