<template>
  <div class="mt-6">

    <!-- 🎯 Filtres -->
    <ReservationFilters />

    <!-- 🔄 Chargement -->
    <div v-if="loading.fetch" class="flex justify-center my-6">
      <div class="flex items-center space-x-2 bg-gray-800 px-6 py-3 rounded-lg shadow-lg animate-pulse">
        <span class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></span>
        <p class="text-white text-lg font-medium">Chargement des réservations...</p>
      </div>
    </div>

    <!-- ❌ Erreur -->
    <div v-else-if="errors.fetch" class="flex justify-center my-6">
      <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
        <span class="text-xl">❌</span>
        <p class="text-lg font-medium">Une erreur est survenue : {{ errors.fetch }}</p>
      </div>
    </div>

    <!-- 📭 Aucune réservation trouvée -->
    <div v-else-if="filteredReservations.length === 0" class="flex justify-center my-6">
      <div class="bg-gray-800 px-6 py-4 rounded-lg shadow-lg text-center">
        <p class="text-gray-300 text-lg">📭 Aucune réservation trouvée.</p>
        <p class="text-gray-400 text-sm mt-2">Ajoutez-en une pour commencer !</p>
      </div>
    </div>

    <!-- ✅ Liste des réservations -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
      <ReservationListItem v-for="reservation in filteredReservations" :reservation="reservation" :key="reservation.id" />
    </div>
  </div>
</template>

<script setup>
import { ReservationListItem, ReservationFilters } from '@/components/reservations';
import { useReservationsStore } from '@/stores/reservations'
import { storeToRefs } from 'pinia';
import { onMounted } from 'vue';


const reservationsStore = useReservationsStore()

const { fetchReservations, clearErrors } = reservationsStore;
const { filteredReservations, errors, loading } = storeToRefs(reservationsStore)

onMounted(() => {
  fetchReservations()
  clearErrors('fetch')
})


</script>
