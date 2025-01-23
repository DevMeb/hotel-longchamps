<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row gap-4">
    <div>
      <label class="text-sm text-gray-300 font-semibold">Locataire :</label>
      <select v-model="activeFilters.renter" class="filter-input">
        <option value="">Tous</option>
        <option v-for="renter in renters" :key="renter.id" :value="renter.id">
          {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
        </option>
      </select>
    </div>

    <div>
      <label class="text-sm text-gray-300 font-semibold">Chambre :</label>
      <select v-model="activeFilters.room" class="filter-input">
        <option value="">Toutes</option>
        <option v-for="room in rooms" :key="room.id" :value="room.id">
          {{ room.name }}
        </option>
      </select>
    </div>

    <div>
      <label class="text-sm text-gray-300 font-semibold">Statut :</label>
      <select v-model="activeFilters.status" class="filter-input">
        <option value="">Tous</option>
        <option value="pending">En attente</option>
        <option value="issued">Envoyée</option>
        <option value="paid">Payée</option>
      </select>
    </div>

    <div>
      <label class="text-sm text-gray-300 font-semibold">Mois & Année :</label>
      <input type="month" v-model="activeFilters.month_year" class="filter-input">
    </div>

    <button @click="resetFilters" v-if="isAnyFilterActive" class="btn-secondary">
      Réinitialiser
    </button>
  </div>
</template>

<script setup>
import { watch, onMounted } from "vue";
import { useInvoicesStore } from "@/stores/invoices";
import { useRentersStore } from "@/stores/renters";
import { useRoomsStore } from "@/stores/rooms";
import { storeToRefs } from "pinia";

const invoicesStore = useInvoicesStore();
const rentersStore = useRentersStore();
const roomsStore = useRoomsStore();

const { activeFilters, isAnyFilterActive } = storeToRefs(invoicesStore);
const { updateFilters } = invoicesStore;
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

<style scoped>
.filter-input {
  @apply p-2 border rounded-md bg-gray-900 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition w-full;
}
.btn-secondary {
  @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
}
</style>
