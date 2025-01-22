<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row gap-4">
    <!-- 🔍 Filtrer par nom -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Nom :</label>
      <select v-model="activeFilters.name" class="filter-input">
        <option value="">Toutes</option>
        <option v-for="room in roomNames" :key="room" :value="room">
          {{ room.toUpperCase() }}
        </option>
      </select>
    </div>

    <!-- 🔍 Filtrer par loyer -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Loyer :</label>
      <select v-model="activeFilters.rent" class="filter-input">
        <option value="">Tous</option>
        <option v-for="rent in roomRents" :key="rent" :value="rent">
          {{ rent }} €
        </option>
      </select>
    </div>

    <!-- Bouton de réinitialisation -->
    <button @click="resetFilters" v-if="isAnyFilterActive" class="btn-secondary">
      Réinitialiser
    </button>
  </div>
</template>

<script setup>
import { watch, computed } from "vue";
import { useRoomsStore } from "@/stores/rooms";
import { storeToRefs } from "pinia";

const roomsStore = useRoomsStore();
const { activeFilters, isAnyFilterActive } = storeToRefs(roomsStore);
const { updateFilters } = roomsStore;

// 📌 Liste unique des noms de chambre
const roomNames = computed(() => {
  return [...new Set(roomsStore.rooms.map(room => room.name))];
});

// 📌 Liste unique des loyers
const roomRents = computed(() => {
  return [...new Set(roomsStore.rooms.map(room => room.rent))].sort((a, b) => a - b);
});

// 📌 Met à jour les filtres à chaque modification
watch(activeFilters, (newFilters) => {
  updateFilters(newFilters);
}, { deep: true });

// 📌 Réinitialisation des filtres
const resetFilters = () => {
  updateFilters({
    name: "",
    rent: "",
  });
};
</script>
