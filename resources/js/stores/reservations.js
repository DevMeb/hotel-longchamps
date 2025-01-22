import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { notify } from '@/utils';
import { useStorage } from "@vueuse/core";

export const useReservationsStore = defineStore('reservations', () => {
  const reservations = ref([]);
  const errors = ref({}); 
  const loading = ref({});

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage("reservation-filters", {
    renter: "",
    room: "",
    month_year: "",
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
  const filteredReservations = ref([]);

  watch([reservations, activeFilters], () => {
    filteredReservations.value = reservations.value.filter(reservation => {
      const { renter, room, month_year, status } = activeFilters.value;
  
      // 📌 Filtrage par locataire
      if (renter && reservation.renter.id !== parseInt(renter)) return false;
  
      // 📌 Filtrage par chambre
      if (room && reservation.room.id !== parseInt(room)) return false;
  
      // 📌 Filtrage par mois et année
      if (month_year) {
        const selectedDate = new Date(month_year);
        const selectedMonth = selectedDate.getMonth();
        const selectedYear = selectedDate.getFullYear();
  
        const reservationStartDate = new Date(reservation.start_date);
        const reservationEndDate = reservation.end_date ? new Date(reservation.end_date) : null;
  
        const startMonth = reservationStartDate.getMonth();
        const startYear = reservationStartDate.getFullYear();
  
        // Vérifier si la réservation est en cours à ce mois-là
        const isOngoingDuringSelectedMonth = 
          (startYear < selectedYear || (startYear === selectedYear && startMonth <= selectedMonth)) &&
          (!reservationEndDate || reservationEndDate >= selectedDate);
  
        if (!isOngoingDuringSelectedMonth) return false;
      }
  
      // 📌 Filtrage par statut :
      // - "ongoing" = réservations sans date de fin
      // - "completed" = réservations avec date de fin
      if (status === "ongoing" && reservation.end_date) return false;
      if (status === "completed" && !reservation.end_date) return false;
  
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

  async function fetchReservations() {
    return apiCall({
      operation: 'fetch',
      request: () => axios.get('/api/reservations'),
      onSuccess: (response) => {
        reservations.value = response.data.data;
      },
    });
  }

  async function addReservation(reservation) {
    return apiCall({
      operation: 'add',
      request: () => axios.post('/api/reservations', reservation),
      onSuccess: (response) => {
        reservations.value.push(response.data.data);
        notify('success', response.data.message);
      },
    });
  }

  async function updateReservation(reservation) {
    return apiCall({
      operation: 'update',
      request: () => axios.put(`/api/reservations/${reservation.id}`, reservation),
      onSuccess: (response) => {
        const index = reservations.value.findIndex(t => t.id === reservation.id);
        if (index !== -1) {
          reservations.value[index] = response.data.data;
        }
        notify('success', response.data.message);
      },
    });
  }

  async function deleteReservation(reservationId) {
    return apiCall({
      operation: 'delete',
      request: () => axios.delete(`/api/reservations/${reservationId}`),
      onSuccess: (response) => {
        reservations.value = reservations.value.filter(r => r.id !== reservationId);
        notify('success', response.data.message);
      },
    });
  }

  return {
    reservations,
    errors,
    loading,
    fetchReservations,
    addReservation,
    updateReservation,
    deleteReservation,
    clearErrors,
    activeFilters,
    updateFilters,
    filteredReservations,
    isAnyFilterActive,
  };
});
