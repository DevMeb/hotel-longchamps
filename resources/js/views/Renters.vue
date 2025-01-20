<template>
  <navbar></navbar>
  <div class="bg-gray-900">
    <div class="mx-auto max-w-7xl">
      <div class="bg-gray-900 py-10">
        <div class="px-4 sm:px-6 lg:px-8">

          <!-- 🔹 En-tête -->
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-white">Locataires</h1>
              <p class="mt-2 text-sm text-gray-300">
                Une liste de tous les locataires dans votre compte, y compris leur nom, prénom et tuteur.
              </p>
            </div>
            <div class="mt-4">
              <button @click="openRenterModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-md">
                Ajouter un locataire
              </button>
            </div>
          </div>

          <!-- 🔍 Filtres de recherche -->
          <RentersFilters :selectedFilter="selectedFilter" :searchQuery="searchQuery" @updateFilter="updateFilter" />

          <!-- 📋 Liste des locataires -->
          <RentersList 
            :renters="filteredRenters" 
            :loading="loading" 
            :error="error" 
            @edit="openRenterModal" 
            @delete="confirmDeleteRenter"
            class="mt-8"
          />

          <!-- 📝 Modale d'ajout/modification -->
          <RenterFormModal 
            :show="showAddRenterModal" 
            :renter="selectedRenter" 
            :isEditing="isEditing"
            @close="closeRenterModal"
          />

          <!-- ❌ Modale de confirmation de suppression -->
          <RenterDeleteModal 
            :show="showDeleteRenterModal" 
            :renter="renterToDelete" 
            @cancel="showDeleteRenterModal = false"
            @confirm="performDeleteRenter"
          />

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { RentersList, RenterFormModal, RenterDeleteModal, RentersFilters } from '@/components/renters';
import { onMounted, ref, computed } from 'vue';
import { useRentersStore } from '@/stores/renters';
import { storeToRefs } from 'pinia';
import { useToast } from "vue-toastification";

const rentersStore = useRentersStore();
const { renters, error, loading } = storeToRefs(rentersStore);
const { fetchRenters, deleteRenter } = rentersStore;

const showAddRenterModal = ref(false);
const isEditing = ref(false);
const selectedRenter = ref(null);

const showDeleteRenterModal = ref(false);
const renterToDelete = ref(null);

onMounted(() => {
  fetchRenters();
});

// 📌 Gestion de la modale d'ajout/modification
const openRenterModal = (renter = null) => {
  selectedRenter.value = renter;
  isEditing.value = !!renter;
  showAddRenterModal.value = true;
};

// 📌 Fermeture de la modale
const closeRenterModal = () => {
  showAddRenterModal.value = false;
  selectedRenter.value = null;
};

// 📌 Gestion de la modale de suppression
const confirmDeleteRenter = (renter) => {
  renterToDelete.value = renter;
  showDeleteRenterModal.value = true;
};

// 📌 Suppression sans rappeler l’API
const performDeleteRenter = async () => {
  try {
    await deleteRenter(renterToDelete.value.id);
    useToast().success("Locataire supprimé avec succès.");
    
    // Mise à jour instantanée des données locales
    renters.value = renters.value.filter(r => r.id !== renterToDelete.value.id);
  } catch (err) {
    useToast().error("Une erreur est survenue lors de la suppression.");
  } finally {
    showDeleteRenterModal.value = false;
    renterToDelete.value = null;
  }
};

// 🔍 Gestion des filtres de recherche
const selectedFilter = ref("last_name");
const searchQuery = ref("");

const updateFilter = ({ filter, query }) => {
  selectedFilter.value = filter;
  searchQuery.value = query;
};

// 🔍 Liste filtrée des locataires
const filteredRenters = computed(() => {
  return renters.value.filter(renter => {
    if (selectedFilter.value === "tutor") {
      // 📌 Si un tuteur est sélectionné dans la liste déroulante
      if (searchQuery.value) {
        return renter.tutor?.id === parseInt(searchQuery.value); // Filtrer par ID du tuteur sélectionné
      }
      return true; // Aucun tuteur sélectionné = Afficher tous les locataires
    } 

    // 📌 Filtrage standard sur les autres champs
    let value = renter[selectedFilter.value] 
      ? renter[selectedFilter.value].toString().toLowerCase().trim() 
      : "";

    return value.includes(searchQuery.value.toLowerCase().trim());
  });
});

</script>
