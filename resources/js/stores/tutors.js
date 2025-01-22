import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { notify } from '@/utils';
import { useStorage } from "@vueuse/core";

export const useTutorsStore = defineStore('tutors', () => {
  const tutors = ref([]);
  const errors = ref({}); 
  const loading = ref({});

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage("tutor-filters", {
    last_name: "",
    first_name: "",
    email: "",
  });

  // 📌 Mise à jour des filtres
  function updateFilters(filters) {
    activeFilters.value = filters;
  }

  // 📌 Vérification si des filtres sont actifs
  const isAnyFilterActive = computed(() => {
    return Object.values(activeFilters.value).some(value => value !== "");
  });

  // 📌 Application des filtres aux tuteurs
  const filteredTutors = ref([]);

  watch([tutors, activeFilters], () => {
    filteredTutors.value = tutors.value.filter(tutor => {
      const { last_name, first_name, email } = activeFilters.value;

      if (last_name && tutor.last_name !== last_name) return false;
      if (first_name && tutor.first_name !== first_name) return false;
      if (email && !tutor.email.toLowerCase().includes(email.toLowerCase())) return false;

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

  async function fetchTutors() {
    return apiCall({
      operation: 'fetch',
      request: () => axios.get('/api/tutors'),
      onSuccess: (response) => {
        tutors.value = response.data.data;
      },
    });
  }

  async function addTutor(tutor) {
    return apiCall({
      operation: 'add',
      request: () => axios.post('/api/tutors', tutor),
      onSuccess: (response) => {
        tutors.value.push(response.data.data);
        notify('success', response.data.message);
      },
    });
  }

  async function updateTutor(tutor) {
    return apiCall({
      operation: 'update',
      request: () => axios.put(`/api/tutors/${tutor.id}`, tutor),
      onSuccess: (response) => {
        const index = tutors.value.findIndex(t => t.id === tutor.id);
        if (index !== -1) {
          tutors.value[index] = response.data.data;
        }
        notify('success', response.data.message);
      },
    });
  }

  async function deleteTutor(tutorId) {
    return apiCall({
      operation: 'delete',
      request: () => axios.delete(`/api/tutors/${tutorId}`),
      onSuccess: (response) => {
        tutors.value = tutors.value.filter(t => t.id !== tutorId);
        notify('success', response.data.message);
      },
    });
  }

  return {
    tutors,
    errors,
    loading,
    fetchTutors,
    addTutor,
    updateTutor,
    deleteTutor,
    clearErrors,
    activeFilters,
    updateFilters,
    filteredTutors,
    isAnyFilterActive,
  };
});
