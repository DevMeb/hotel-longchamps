<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50">
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <span v-if="renterData.id" class="mr-2">✏️</span>
          <span v-else class="mr-2">➕</span>
          {{ renterData.id ? 'Éditer le locataire' : 'Ajouter un locataire' }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
      </div>

      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <!-- Nom -->
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input ref="firstInput" type="text" v-model="renterData.last_name"
            class="input-field" :class="{ 'border-red-500': errors.validationErrors?.last_name }">
          <p v-if="errors.validationErrors?.last_name" class="error-message">
            {{ errors.validationErrors.last_name.join(', ') }}
          </p>
        </div>

        <!-- Prénom -->
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input type="text" v-model="renterData.first_name"
            class="input-field" :class="{ 'border-red-500': errors.validationErrors?.first_name }">
          <p v-if="errors.validationErrors?.first_name" class="error-message">
            {{ errors.validationErrors.first_name.join(', ') }}
          </p>
        </div>

        <!-- Sélection du Tuteur -->
        <div>
          <label for="tutor_id" class="block text-sm font-medium text-gray-700">Tuteur</label>
          <select v-model="renterData.tutor_id" class="input-field">
            <option value="">Sélectionner un tuteur (optionnel)</option>
            <option v-for="tutor in tutors" :value="tutor.id" :key="tutor.id">
              {{ tutor.last_name.toUpperCase() }} {{ tutor.first_name }}
            </option>
          </select>
          <p v-if="errors.validationErrors?.tutor_id" class="error-message">
            {{ errors.validationErrors.tutor_id.join(', ') }}
          </p>
        </div>

        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          <button type="submit" class="btn-primary flex items-center" :disabled="renterData.id ? loading.update : loading.add">
            <span v-if="renterData.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
            {{ renterData.id ? 'Mettre à jour' : 'Ajouter' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect, onMounted } from 'vue';
import { useRentersStore } from '@/stores/renters';
import { useTutorsStore } from '@/stores/tutors';
import { storeToRefs } from 'pinia';

const props = defineProps({
  renter: Object,
});

const emit = defineEmits(['close']);

const rentersStore = useRentersStore();
const { addRenter, updateRenter, clearErrors } = rentersStore;
const { errors, loading } = storeToRefs(rentersStore);

const tutorsStore = useTutorsStore();
const { fetchTutors } = tutorsStore;
const { tutors } = storeToRefs(tutorsStore);

// Charge la liste des tuteurs au montage
onMounted(() => fetchTutors());

// Données locales pour éviter les mutations directes des props
const renterData = ref({
  id: null,
  last_name: '',
  first_name: '',
  tutor_id: ''
});

// Synchronisation des données avec les props
watchEffect(() => {
  renterData.value = props.renter
    ? { ...props.renter, tutor_id: props.renter.tutor?.id || '' }
    : { id: null, last_name: '', first_name: '', tutor_id: '' };
});

// Soumission du formulaire
const submitForm = async () => {
    const success = renterData.value.id
    ? await updateRenter(renterData.value)
    : await addRenter(renterData.value);

    if (success) {
      closeModal();
    }
};

// Fermeture de la modale et réinitialisation des erreurs
const closeModal = () => {
  clearErrors('validationErrors'); // Réinitialiser uniquement les erreurs de validation
  emit("close");
};
</script>

<style scoped>
/* Animation d'apparition de la modale */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}

/* Styles des champs de saisie */
.input-field {
  @apply block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
}

/* Messages d'erreur */
.error-message {
  @apply mt-2 text-sm text-red-600;
}

/* Bouton principal */
.btn-primary {
  @apply px-4 py-2 bg-indigo-500 text-white rounded-md font-semibold hover:bg-indigo-400 transition disabled:opacity-50 disabled:cursor-not-allowed;
}

/* Bouton secondaire */
.btn-secondary {
  @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
}
</style>
