<template>
  <div class="mt-6">

    <!-- 🎯 Filtres -->
    <TutorFilters />

    <div v-if="loading.fetch" class="flex justify-center my-6">
      <div class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></div>
      <p class="text-white text-lg font-medium ml-2">Chargement des tuteurs...</p>
    </div>

    <div v-else-if="errors.fetch" class="flex justify-center my-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
      <span class="text-xl">❌</span>
      <p class="text-lg font-medium ml-2">{{ errors.fetch }}</p>
    </div>

    <div v-else-if="filteredTutors.length === 0" class="flex justify-center my-6 bg-gray-800 px-6 py-4 rounded-lg shadow-lg">
      <p class="text-gray-300 text-lg">📭 Aucun tuteur trouvé.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
      <TutorListItem v-for="tutor in filteredTutors" :tutor="tutor" :key="tutor.id" />
    </div>
  </div>
</template>
    
<script setup>
import { useTutorsStore } from "@/stores/tutors";
import { storeToRefs } from "pinia";
import { onMounted } from "vue";
import { TutorListItem, TutorFilters } from "@/components/tutors/";

const tutorsStore = useTutorsStore();
const { fetchTutors, clearErrors } = tutorsStore;
const { filteredTutors, errors, loading } = storeToRefs(tutorsStore);

onMounted(() => {
  fetchTutors();
  clearErrors("fetch");
});
</script>
