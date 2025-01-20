<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
    <!-- Overlay cliquable pour fermer la modale -->
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
      <!-- ✨ Titre de la modale avec icône -->
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <span v-if="isEditing" class="mr-2">✏️</span>
          <span v-else class="mr-2">➕</span>
          {{ isEditing ? 'Éditer le tuteur' : 'Ajouter un tuteur' }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">
          ✖️
        </button>
      </div>

      <!-- 📋 Formulaire -->
      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <!-- Nom -->
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input ref="firstInput" type="text" v-model="tutorData.last_name"
            class="input-field" :class="{ 'border-red-500': errors.last_name }">
          <p v-if="errors.last_name" class="error-message">{{ errors.last_name?.join(' ') }}</p>
        </div>

        <!-- Prénom -->
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input type="text" v-model="tutorData.first_name"
            class="input-field" :class="{ 'border-red-500': errors.first_name }">
          <p v-if="errors.first_name" class="error-message">{{ errors.first_name?.join(' ') }}</p>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" v-model="tutorData.email"
            class="input-field" :class="{ 'border-red-500': errors.email }">
          <p v-if="errors.email" class="error-message">{{ errors.email?.join(' ') }}</p>
        </div>

        <!-- Téléphone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
          <input type="text" v-model="tutorData.phone"
            class="input-field" :class="{ 'border-red-500': errors.phone }">
          <p v-if="errors.phone" class="error-message">{{ errors.phone?.join(' ') }}</p>
        </div>

        <!-- ⚡️ Boutons d'action -->
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          
          <!-- Bouton désactivé si en cours de soumission -->
          <button type="submit" class="btn-primary flex items-center" :disabled="isSubmitting">
            <span v-if="isSubmitting" class="animate-spin mr-2">⏳</span>
            {{ isEditing ? 'Mettre à jour' : 'Ajouter' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useTutorsStore } from '@/stores/tutors';
import { useToast } from 'vue-toastification';

const props = defineProps({
  show: Boolean,
  tutor: Object,
  isEditing: Boolean
});

const emit = defineEmits(['close']);
const tutorsStore = useTutorsStore();
const { addTutor, updateTutor } = tutorsStore;
const toast = useToast();

const tutorData = ref({ id: null, last_name: '', first_name: '', email: '', phone: '' });
const errors = ref({});
const isSubmitting = ref(false);
const firstInput = ref(null);

// ✅ Mise à jour automatique des données du formulaire
watch(() => props.tutor, (newTutor) => {
  if (newTutor && props.isEditing) {
    tutorData.value = { ...newTutor };
  } else {
    resetForm();
  }
}, { immediate: true, deep: true });

// 🔹 Focus automatique sur le premier champ lors de l'ouverture de la modale
watch(() => props.show, (isOpen) => {
  if (isOpen) {
    setTimeout(() => firstInput.value?.focus(), 100);
  }
});

// ✅ Ferme la modale en appuyant sur `Échap`
onMounted(() => {
  window.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && props.show) {
      closeModal();
    }
  });
});

const submitForm = async () => {
  try {
    isSubmitting.value = true;
    
    if (props.isEditing) {
      await updateTutor(tutorData.value);
      toast.success('Tuteur mis à jour avec succès.');
    } else {
      await addTutor(tutorData.value);
      toast.success('Tuteur ajouté avec succès.');
    }

    closeModal();
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
      toast.error("Des erreurs de validation ont été détectées.");
    } else {
      toast.error("Une erreur est survenue.");
    }
  } finally {
    isSubmitting.value = false;
  }
};

const closeModal = () => {
  resetForm();
  emit('close');
};

function resetForm() {
  tutorData.value = { id: null, last_name: '', first_name: '', email: '', phone: '' };
  errors.value = {};
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
