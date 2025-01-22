<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col sm:flex-row gap-4">
    <!-- 🔍 Filtrer par nom -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Nom :</label>
      <select v-model="activeFilters.last_name" class="filter-input">
        <option value="">Tous</option>
        <option v-for="tutor in tutorNames" :key="tutor.id" :value="tutor.last_name">
          {{ tutor.toUpperCase() }}
        </option>
      </select>
    </div>

    <!-- 🔍 Filtrer par prénom -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Prénom :</label>
      <select v-model="activeFilters.first_name" class="filter-input">
        <option value="">Tous</option>
        <option v-for="tutor in tutorFirstNames" :key="tutor.id" :value="tutor.first_name">
          {{ tutor.toUpperCase() }}
        </option>
      </select>
    </div>

    <!-- 🔍 Filtrer par Email -->
    <div>
      <label class="text-sm text-gray-300 font-semibold">Email :</label>
      <input type="text" v-model="activeFilters.email" placeholder="Rechercher un email..." class="filter-input">
    </div>

    <!-- Bouton de réinitialisation -->
    <button @click="resetFilters" v-if="isAnyFilterActive" class="btn-secondary">
      Réinitialiser
    </button>
  </div>
</template>

<script setup>
import { watch, computed } from "vue";
import { useTutorsStore } from "@/stores/tutors";
import { storeToRefs } from "pinia";

const tutorsStore = useTutorsStore();
const { activeFilters, isAnyFilterActive } = storeToRefs(tutorsStore);
const { updateFilters } = tutorsStore;

// 📌 Liste unique des noms et prénoms pour les filtres
const tutorNames = computed(() => {
  return [...new Set(tutorsStore.tutors.map(tutor => tutor.last_name))];
});

const tutorFirstNames = computed(() => {
  return [...new Set(tutorsStore.tutors.map(tutor => tutor.first_name))];
});
// 📌 Met à jour les filtres à chaque modification
watch(activeFilters, (newFilters) => {
  updateFilters(newFilters);
}, { deep: true });

// 📌 Réinitialisation des filtres
const resetFilters = () => {
  updateFilters({
    last_name: "",
    first_name: "",
    email: "",
  });
};
</script>
