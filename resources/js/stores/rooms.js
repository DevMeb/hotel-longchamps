import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { notify } from '@/utils';
import { useStorage } from "@vueuse/core";

export const useRoomsStore = defineStore('rooms', () => {
  const rooms = ref([]);
  const errors = ref({}); 
  const loading = ref({});

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage("room-filters", {
    name: "",
    rent: "",
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
  const filteredRooms = ref([]);

  watch([rooms, activeFilters], () => {
    filteredRooms.value = rooms.value.filter(room => {
      const { name, rent } = activeFilters.value;

      if (name && room.name !== name) return false;
      if (rent && room.rent !== rent) return false;

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
      console.error(err)
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

  async function fetchRooms() {
    return apiCall({
      operation: 'fetch',
      request: () => axios.get('/api/rooms'),
      onSuccess: (response) => {
        rooms.value = response.data.data;
      },
    });
  }

  async function addRoom(tutor) {
    return apiCall({
      operation: 'add',
      request: () => axios.post('/api/rooms', tutor),
      onSuccess: (response) => {
        rooms.value.push(response.data.data);
        notify('success', response.data.message);
      },
    });
  }

  async function updateRoom(room) {
    return apiCall({
      operation: 'update',
      request: () => axios.put(`/api/rooms/${room.id}`, room),
      onSuccess: (response) => {
        const index = rooms.value.findIndex(r => r.id === room.id);
        if (index !== -1) {
          rooms.value[index] = response.data.data;
        }
        notify('success', response.data.message);
      },
    });
  }
 
  async function deleteRoom(roomId) {
    return apiCall({
      operation: 'delete',
      request: () => axios.delete(`/api/rooms/${roomId}`),
      onSuccess: (response) => {
        rooms.value = rooms.value.filter(r => r.id !== roomId);
        notify('success', response.data.message);
      },
    });
  }

  return {
    rooms,
    errors,
    loading,
    fetchRooms,
    addRoom,
    updateRoom,
    deleteRoom,
    clearErrors,
    activeFilters,
    updateFilters,
    filteredRooms,
    isAnyFilterActive,
  };
});
