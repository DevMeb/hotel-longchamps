import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useRentersStore = defineStore('renters', () => {
  const renters = ref([]);
  const error = ref(null);
  const loading = ref(false);

  /**
   * Récupère tous les locataires
   */
  async function fetchRenters() {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get('/api/renters');
      renters.value = response.data.data; // 🔹 Ajoute uniquement si le serveur retourne des données valides
    } catch (err) {
      error.value = err.response?.data?.message || "Erreur lors de la récupération des locataires.";
    } finally {
      loading.value = false;
    }
  }

  /**
   * Ajoute un nouveau locataire et met à jour localement la liste
   */
  async function addRenter(renter) {
    try {
      const response = await axios.post('/api/renters', renter);
      renters.value.push(response.data.data); // ✅ Mise à jour locale
      return response;
    } catch (err) {
      throw err;
    }
  }

  /**
   * Met à jour un locataire existant et modifie la liste locale
   */
  async function updateRenter(renter) {
    try {
      const response = await axios.put(`/api/renters/${renter.id}`, renter);
      const index = renters.value.findIndex(r => r.id === renter.id);
      if (index !== -1) {
        renters.value[index] = response.data.data; // ✅ Mise à jour locale
      }
      return response;
    } catch (err) {
      throw err;
    }
  }

  /**
   * Supprime un locataire et met à jour la liste locale
   */
  async function deleteRenter(renterId) {
    try {
      await axios.delete(`/api/renters/${renterId}`);
      renters.value = renters.value.filter(r => r.id !== renterId); // ✅ Suppression locale
    } catch (err) {
      throw err;
    }
  }

  return { 
    renters, 
    error, 
    loading, 
    fetchRenters, 
    addRenter, 
    updateRenter, 
    deleteRenter 
  };
});
