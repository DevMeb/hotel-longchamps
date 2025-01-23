<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row gap-4">
    <!-- 🔍 Filtrer par locataire -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Locataire :</label>
      <select v-model="activeFilters.renter" class="filter-input">
        <option value="">Tous</option>
        <option v-for="renter in renters" :key="renter.id" :value="renter.id">
          {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
        </option>
      </select>
    </div>

    <!-- 🔍 Filtrer par chambre -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Chambre :</label>
      <select v-model="activeFilters.room" class="filter-input">
        <option value="">Toutes</option>
        <option v-for="room in rooms" :key="room.id" :value="room.id">
          {{ room.name }}
        </option>
      </select>
    </div>

    <!-- 🔄 Filtrer par statut -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Statut :</label>
      <select v-model="activeFilters.status" class="filter-input">
        <option value="">Tous</option>
        <option value="ongoing">En cours</option>
        <option value="completed">Terminé</option>
      </select>
    </div>

    <!-- 📅 Filtrer par mois et année -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Mois & Année :</label>
      <input type="month" v-model="activeFilters.month_year" class="filter-input">
    </div>

    <!-- 🔄 Bouton de réinitialisation -->
    <button @click="resetFilters" v-if="isAnyFilterActive" class="btn-secondary">
      Réinitialiser
    </button>
  </div>
</template>

<script setup>
import { watch, onMounted } from "vue";
import { useReservationsStore } from "@/stores/reservations";
import { useRentersStore } from "@/stores/renters";
import { useRoomsStore } from "@/stores/rooms";
import { storeToRefs } from "pinia";

const reservationsStore = useReservationsStore();
const rentersStore = useRentersStore();
const roomsStore = useRoomsStore();

const { activeFilters, isAnyFilterActive } = storeToRefs(reservationsStore);
const { updateFilters } = reservationsStore;
const { renters } = storeToRefs(rentersStore);
const { rooms } = storeToRefs(roomsStore);

// 📌 Met à jour les filtres à chaque modification
watch(activeFilters, (newFilters) => {
  updateFilters(newFilters);
}, { deep: true });

const resetFilters = () => {
  updateFilters({
    renter: "",
    room: "",
    status: "",
    month_year: "",
  });
};

onMounted(() => {
  rentersStore.fetchRenters();
  roomsStore.fetchRooms();
});
</script>
