<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row gap-4 border border-gray-700">
    <!-- 🔍 Filtrer par nom -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Nom :</label>
      <select v-model="activeFilters.last_name" class="filter-input">
        <option value="">Tous</option>
        <option v-for="name in renterNames" :key="name" :value="name">
          {{ name.toUpperCase() }}
        </option>
      </select>
    </div>

    <!-- 🔍 Filtrer par prénom -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Prénom :</label>
      <select v-model="activeFilters.first_name" class="filter-input">
        <option value="">Tous</option>
        <option v-for="firstName in renterFirstNames" :key="firstName" :value="firstName">
          {{ firstName.toUpperCase() }}
        </option>
      </select>
    </div>

    <!-- 📜 Filtrer par Tuteur -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Tuteur :</label>
      <select v-model="activeFilters.tutor" class="filter-input">
        <option value="">Tous</option>
        <option v-for="tutor in tutorsList" :key="tutor.id" :value="tutor.id">
          {{ tutor.last_name.toUpperCase() }} {{ tutor.first_name }}
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
import { watch, computed, onMounted } from "vue";
import { useRentersStore } from "@/stores/renters";
import { useTutorsStore } from "@/stores/tutors";
import { storeToRefs } from "pinia";

const rentersStore = useRentersStore();
const tutorsStore = useTutorsStore();

const { activeFilters, isAnyFilterActive } = storeToRefs(rentersStore);
const { updateFilters } = rentersStore;
const { renters } = storeToRefs(rentersStore);
const { tutors } = storeToRefs(tutorsStore);

// 📌 Charger les tuteurs au montage du composant
onMounted(() => {
  tutorsStore.fetchTutors();
});

// 📌 Liste unique des noms et prénoms pour les filtres
const renterNames = computed(() => [...new Set(renters.value.map(renter => renter.last_name))]);
const renterFirstNames = computed(() => [...new Set(renters.value.map(renter => renter.first_name))]);
const tutorsList = computed(() => tutors.value);

// 📌 Met à jour les filtres à chaque modification
watch(activeFilters, (newFilters) => {
  updateFilters(newFilters);
}, { deep: true });

// 📌 Réinitialisation des filtres
const resetFilters = () => {
  updateFilters({
    last_name: "",
    first_name: "",
    tutor: "",
  });
};
</script>

<style scoped>
.filter-input {
  @apply p-2 border rounded-md bg-gray-900 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition w-full;
}

.btn-secondary {
  @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
}
</style>
