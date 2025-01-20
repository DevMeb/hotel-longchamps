<template>
  <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 bg-gray-800 p-4 rounded-lg shadow-lg mt-4">
    <!-- 🔍 Label de filtrage -->
    <label class="text-sm text-gray-300 font-semibold">Filtrer par :</label>

    <!-- 🎛 Sélection du filtre -->
    <select v-model="localFilter" @change="updateFilters" class="filter-dropdown">
      <option value="id">Identifiant</option>
      <option value="renter">Locataire</option>
      <option value="room">Chambre</option>
      <option value="month_year">Mois et Année</option>
    </select>

    <!-- 📌 Sélection dynamique en fonction du filtre -->
    <!-- 📌 Filtre Identifiant -->
    <div v-if="localFilter === 'id'" class="relative w-64">
      <input 
        type="number" 
        v-model="localQuery" 
        @input="updateFilters" 
        class="filter-input" 
        placeholder="Entrez un ID..." 
      />
    </div>

    <!-- 📌 Filtre Locataire -->
    <div v-else-if="localFilter === 'renter'" class="relative w-64">
      <select v-model="localQuery" @change="updateFilters" class="filter-input">
        <option value="">Tous les locataires</option>
        <option v-for="renter in renters" :value="renter.id" :key="renter.id">
          {{ renter.last_name.toUpperCase() }} {{ renter.first_name }}
        </option>
      </select>
    </div>

    <!-- 📌 Filtre Chambre -->
    <div v-else-if="localFilter === 'room'" class="relative w-64">
      <select v-model="localQuery" @change="updateFilters" class="filter-input">
        <option value="">Toutes les chambres</option>
        <option v-for="room in rooms" :value="room.id" :key="room.id">
          {{ room.name }}
        </option>
      </select>
    </div>

    <!-- 📅 Filtre Mois et Année -->
    <div v-else-if="localFilter === 'month_year'" class="flex space-x-2">
      <input type="month" v-model="localQuery" @input="updateFilters" class="filter-input" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useRentersStore } from "@/stores/renters";
import { useRoomsStore } from "@/stores/rooms";
import { storeToRefs } from "pinia";

const props = defineProps({ selectedFilter: String, searchQuery: String });
const emit = defineEmits(["updateFilter"]);
const route = useRoute();

const rentersStore = useRentersStore();
const roomsStore = useRoomsStore();
const { renters } = storeToRefs(rentersStore);
const { rooms } = storeToRefs(roomsStore);

const localFilter = ref(props.selectedFilter || "renter");
const localQuery = ref(props.searchQuery || "");

// 🔍 Vérifier s'il y a un ID dans l'URL et pré-remplir le filtre Identifiant
onMounted(() => {
  if (route.query.id) {
    localFilter.value = "id"; // Sélectionne le filtre Identifiant
    localQuery.value = parseInt(route.query.id); // Remplit l'ID dans l'input
    updateFilters(); // Applique automatiquement le filtre
  }
});

// Émettre les mises à jour des filtres
const updateFilters = () => {
  let queryValue = localQuery.value;

  // Convertir en nombre si nécessaire (pour les IDs)
  if (["id", "renter", "room"].includes(localFilter.value)) {
    queryValue = queryValue ? parseInt(queryValue) : "";
  }

  emit("updateFilter", { filter: localFilter.value, query: queryValue });
};

// Synchronisation avec les props
watch(() => props.selectedFilter, (newFilter) => {
  localFilter.value = newFilter;
});

watch(() => props.searchQuery, (newQuery) => {
  localQuery.value = newQuery;
});
</script>

<style scoped>
.filter-dropdown { @apply p-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition; }
.filter-input { @apply p-2 border rounded-md bg-gray-900 text-white w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition; }
</style>
