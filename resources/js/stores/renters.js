import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { notify } from '@/utils';
import { useStorage } from "@vueuse/core";

export const useRentersStore = defineStore('renters', () => {
  const renters = ref([]);
  const errors = ref({});
  const loading = ref({});

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage("renter-filters", {
    last_name: "",
    first_name: "",
    tutor: "",
  });

  // 📌 Mise à jour des filtres
  function updateFilters(filters) {
    activeFilters.value = filters;
  }

  // 📌 Vérification si des filtres sont actifs
  const isAnyFilterActive = computed(() => {
    return Object.values(activeFilters.value).some(value => value !== "");
  });

  // 📌 Application des filtres aux locataires
  const filteredRenters = ref([]);

  watch([renters, activeFilters], () => {
    filteredRenters.value = renters.value.filter(renter => {
      const { last_name, first_name, tutor } = activeFilters.value;

      if (last_name && renter.last_name !== last_name) return false;
      if (first_name && renter.first_name !== first_name) return false;
      if (tutor && (!renter.tutor || renter.tutor.id !== parseInt(tutor))) return false;

      return true;
    });
  }, { deep: true, immediate: true });

  function clearErrors(operation) {
    if (operation) {
      errors.value[operation] = null;
    } else {
      errors.value = {};
    }
  }

  function setLoading(operation, state) {
    loading.value[operation] = state;
  }

  async function apiCall({ operation, request, onSuccess }) {
    clearErrors(operation);
    setLoading(operation, true);

    try {
      const response = await request();
      if (onSuccess) onSuccess(response);
      return response;
    } catch (err) {
      if (err.response?.status === 422) {
        errors.value.validationErrors = err.response.data.errors; // Erreurs de validation
      } else {
        errors.value[operation] = err.response?.data?.message || "Une erreur est survenue.";
        notify('error', errors.value[operation]);
      }
    } finally {
      setLoading(operation, false);
    }
  }

  async function fetchRenters() {
    return apiCall({
      operation: 'fetch',
      request: () => axios.get('/api/renters'),
      onSuccess: (response) => {
        renters.value = response.data.data;
      },
    });
  }

  async function addRenter(renter) {
    return apiCall({
      operation: 'add',
      request: () => axios.post('/api/renters', renter),
      onSuccess: (response) => {
        renters.value.push(response.data.data);
        notify('success', response.data.message);
      },
    });
  }

  async function updateRenter(renter) {
    return apiCall({
      operation: 'update',
      request: () => axios.put(`/api/renters/${renter.id}`, renter),
      onSuccess: (response) => {
        const index = renters.value.findIndex(r => r.id === renter.id);
        if (index !== -1) {
          renters.value[index] = response.data.data;
        }
        notify('success', response.data.message);
      },
    });
  }

  async function deleteRenter(renterId) {
    return apiCall({
      operation: 'delete',
      request: () => axios.delete(`/api/renters/${renterId}`),
      onSuccess: (response) => {
        renters.value = renters.value.filter(r => r.id !== renterId);
        notify('success', response.data.message);
      },
    });
  }

  return {
    renters,
    errors,
    loading,
    fetchRenters,
    addRenter,
    updateRenter,
    deleteRenter,
    clearErrors,
    activeFilters,
    updateFilters,
    filteredRenters,
    isAnyFilterActive,
  };
});
