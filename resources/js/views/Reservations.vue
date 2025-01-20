<template>
  <navbar></navbar>
  <div class="bg-gray-900">
    <div class="mx-auto max-w-7xl">
      <div class="bg-gray-900 py-10">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-white">Réservations</h1>
              <p class="mt-2 text-sm text-gray-300">
                Liste de toutes les réservations, y compris les locataires, chambres et dates.
              </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
              <button
                @click="openAddReservationModal"
                type="button"
                class="block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-400"
              >
                Ajouter une réservation
              </button>
            </div>
          </div>

          <!-- 🎯 Filtre des réservations -->
          <ReservationFilters 
            :selectedFilter="selectedFilter"
            :searchQuery="searchQuery"
            @updateFilter="handleFilterChange"
          />

          <!-- 📋 Liste des réservations -->
          <ReservationsList
            :reservations="filteredReservations"
            :loading="loading"
            :error="error"
            @edit="editReservation"
            @delete="confirmDeleteReservation"
            @createInvoice="handleAddInvoice"
          />

          <!-- 📝 Modale d'ajout/modification -->
          <ReservationFormModal
            :show="showAddReservationModal"
            :reservation="selectedReservation"
            :isEditing="isEditing"
            @close="closeReservationModal"
          />

          <!-- ❌ Modale de confirmation de suppression -->
          <ReservationDeleteModal
            :show="showDeleteReservationModal"
            :reservation="reservationToDelete"
            @cancel="showDeleteReservationModal = false"
            @delete="performDeleteReservation"
          />

          <!-- 🧾 Modale de création de facture --> 
          <InvoiceFormModal
            :show="showAddInvoiceModal"
            :invoice="newInvoice"
            @close="showAddInvoiceModal = false"
            @createInvoice="handleAddInvoice"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { onMounted, ref, computed } from 'vue';
import { useReservationsStore } from '@/stores/reservations';
import { useRentersStore } from '@/stores/renters';
import { useRoomsStore } from '@/stores/rooms';
import { storeToRefs } from 'pinia';
import { useToast } from 'vue-toastification';

import { ReservationsList, ReservationFormModal, ReservationDeleteModal, ReservationFilters } from '@/components/reservations/'

import { InvoiceFormModal } from '@/components/invoices/';

const reservationsStore = useReservationsStore();
const { reservations, error, loading } = storeToRefs(reservationsStore);
const { fetchReservations, deleteReservation } = reservationsStore;

const rentersStore = useRentersStore();
const roomsStore = useRoomsStore();

const showAddReservationModal = ref(false);
const showDeleteReservationModal = ref(false);
const showAddInvoiceModal = ref(false);
const isEditing = ref(false);

const selectedReservation = ref(null);
const reservationToDelete = ref(null);
const toast = useToast();

const selectedFilter = ref("renter");
const searchQuery = ref("");

// 📌 Filtrer dynamiquement les réservations
const filteredReservations = computed(() => {
  return reservations.value.filter(reservation => {
    if (!searchQuery.value) return true; // Si aucun filtre appliqué, afficher tout

    if (selectedFilter.value === "id") {
      // 🔍 Comparer l'ID de la réservation
      return reservation.id == parseInt(searchQuery.value);
    }

    if (selectedFilter.value === "renter") {
      // 🔍 Comparer l'ID du locataire sélectionné
      return reservation.renter?.id === parseInt(searchQuery.value);
    } 
    
    if (selectedFilter.value === "room") {
      // 🔍 Comparer l'ID de la chambre sélectionnée
      return reservation.room?.id === parseInt(searchQuery.value);
    }

    if (selectedFilter.value === "month_year") {
      // 🎯 Filtrer les réservations selon le mois et l'année sélectionnés
      const selectedDate = new Date(searchQuery.value);
      const reservationStart = new Date(reservation.start_date);
      const reservationEnd = reservation.end_date ? new Date(reservation.end_date) : null;

      return (
        (reservationStart.getMonth() === selectedDate.getMonth() &&
         reservationStart.getFullYear() === selectedDate.getFullYear()) ||
        (reservationEnd && reservationEnd.getMonth() === selectedDate.getMonth() &&
         reservationEnd.getFullYear() === selectedDate.getFullYear()) ||
        (reservationStart <= selectedDate && (!reservationEnd || reservationEnd >= selectedDate))
      );
    }

    return true;
  });
});

onMounted(() => {
  fetchReservations();
  rentersStore.fetchRenters();
  roomsStore.fetchRooms();
});

// 📌 Gérer la mise à jour des filtres
const handleFilterChange = ({ filter, query }) => {
  selectedFilter.value = filter;
  searchQuery.value = query;
};

// 📌 Ouvre la modale d'ajout/modification
const openAddReservationModal = () => {
  selectedReservation.value = null;
  isEditing.value = false;
  showAddReservationModal.value = true;
};

// 📌 Fermeture de la modale
const closeReservationModal = () => {
  showAddReservationModal.value = false;
  selectedReservation.value = null;
};

// 📌 Édition d'une réservation
const editReservation = (reservation) => {
  selectedReservation.value = { ...reservation };
  isEditing.value = true;
  showAddReservationModal.value = true;
};

// ❌ Suppression d'une réservation
const confirmDeleteReservation = (reservation) => {
  reservationToDelete.value = reservation;
  showDeleteReservationModal.value = true;
};

// ✅ Suppression locale sans recharger l'API
const performDeleteReservation = async () => {
  try {
    await deleteReservation(reservationToDelete.value.id);
    reservations.value = reservations.value.filter(r => r.id !== reservationToDelete.value.id);
    toast.success("Réservation supprimée avec succès.");
    showDeleteReservationModal.value = false;
    reservationToDelete.value = null;
  } catch (err) {
    toast.error("Une erreur est survenue lors de la suppression.");
  }
};

// 🧾 Gestion de la création de facture
const newInvoice = ref({
  reservation_id: null,
  renter_name: '',
  room_name: '',
  subject: '',
  billing_start_date: '',
  billing_end_date: '',
  status: ''
});

const handleAddInvoice = (reservation) => {
  newInvoice.value = {
    reservation_id: reservation.id,
    renter_name: `${reservation.renter.last_name.toUpperCase()} ${reservation.renter.first_name}`,
    room_name: reservation.room.name,
    subject: reservation.subject,
    billing_start_date: reservation.billing_start_date,
    billing_end_date: reservation.billing_end_date || '',
    status: 'pending',
  };
  showAddInvoiceModal.value = true;
};
</script>
