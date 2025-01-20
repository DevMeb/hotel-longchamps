<template>
  <navbar></navbar>
  <div class="bg-gray-900">
    <div class="mx-auto max-w-7xl">
      <div class="bg-gray-900 py-10">
        <div class="px-4 sm:px-6 lg:px-8">

          <!-- 🔹 En-tête -->
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-white">Tuteurs</h1>
              <p class="mt-2 text-sm text-gray-300">
                Une liste de tous les tuteurs dans votre compte, y compris leur nom, prénom, email et téléphone.
              </p>
            </div>
            <div class="mt-4">
              <button @click="openTutorModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-md">Ajouter un tuteur</button>
            </div>
          </div>

          <!-- 🔍 Filtres de recherche -->
          <TutorFilters :selectedFilter="selectedFilter" :searchQuery="searchQuery" @updateFilter="updateFilter" />

          <!-- 📋 Liste des tuteurs -->
          <TutorList :tutors="filteredTutors" :loading="loading" :error="error" @edit="openTutorModal" @delete="confirmDeleteTutor" class="mt-8" />

          <!-- 📝 Modale d'ajout/modification -->
          <TutorFormModal :show="showAddTutorModal" :tutor="selectedTutor" :isEditing="isEditing" @close="closeTutorModal" />

          <!-- ❌ Modale de confirmation de suppression -->
          <TutorDeleteModal :show="showDeleteTutorModal" :tutor="tutorToDelete" @cancel="showDeleteTutorModal = false" @confirm="performDeleteTutor" />

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import Navbar from '@/components/Navbar.vue';
  import { TutorList, TutorFormModal, TutorDeleteModal, TutorFilters } from '@/components/tutors';
  import { onMounted, ref, computed } from 'vue';
  import { useTutorsStore } from '@/stores/tutors';
  import { storeToRefs } from 'pinia';
  import { useToast } from "vue-toastification";

  const tutorsStore = useTutorsStore();
  const { tutors, error, loading } = storeToRefs(tutorsStore);
  const { fetchTutors, deleteTutor } = tutorsStore;

  const showAddTutorModal = ref(false);
  const isEditing = ref(false);
  const selectedTutor = ref(null);

  const showDeleteTutorModal = ref(false);
  const tutorToDelete = ref(null);

  onMounted(() => {
    fetchTutors();
  });

  // 📌 Gestion de la modale d'ajout/modification
  const openTutorModal = (tutor = null) => {
    selectedTutor.value = tutor;
    isEditing.value = !!tutor;
    showAddTutorModal.value = true;
  };

  const closeTutorModal = () => {
    showAddTutorModal.value = false;
    selectedTutor.value = null;
  };

  // 📌 Gestion de la modale de suppression
  const confirmDeleteTutor = (tutor) => {
    tutorToDelete.value = tutor;
    showDeleteTutorModal.value = true;
  };

  const performDeleteTutor = async () => {
    try {
      await deleteTutor(tutorToDelete.value.id);
      useToast().success("Tuteur supprimé avec succès.");
    } catch (err) {
      useToast().error("Une erreur est survenue lors de la suppression.");
    } finally {
      showDeleteTutorModal.value = false;
      tutorToDelete.value = null;
    }
  };

  // 🔍 Gestion des filtres de recherche
  const selectedFilter = ref("last_name");
  const searchQuery = ref("");

  const updateFilter = ({ filter, query }) => {
    selectedFilter.value = filter;
    searchQuery.value = query;
  };

  const filteredTutors = computed(() => {
    return tutors.value.filter(tutor => {
      const value = tutor[selectedFilter.value]?.toString().toLowerCase();
      return value.includes(searchQuery.value.toLowerCase());
    });
  });
</script>
