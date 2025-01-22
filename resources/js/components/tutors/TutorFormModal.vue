<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50">
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <span v-if="tutorData.id" class="mr-2">✏️</span>
          <span v-else class="mr-2">➕</span>
          {{ tutorData.id ? 'Éditer le tuteur' : 'Ajouter un tuteur' }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
      </div>

      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input ref="firstInput" type="text" v-model="tutorData.last_name"
            class="input-field" :class="{ 'border-red-500': errors.last_name }">
            <p v-if="errors.validationErrors?.last_name" class="error-message">
              {{ errors.validationErrors.last_name.join(', ') }}
            </p>        
        </div>

        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input type="text" v-model="tutorData.first_name"
            class="input-field" :class="{ 'border-red-500': errors.first_name }">
            <p v-if="errors.validationErrors?.first_name" class="error-message">
              {{ errors.validationErrors.first_name.join(', ') }}
            </p>        
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" v-model="tutorData.email"
            class="input-field" :class="{ 'border-red-500': errors.email }">
            <p v-if="errors.validationErrors?.email" class="error-message">
              {{ errors.validationErrors.email.join(', ') }}
            </p>
        </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
          <input type="text" v-model="tutorData.phone"
            class="input-field" :class="{ 'border-red-500': errors.phone }">
            <p v-if="errors.validationErrors?.phone" class="error-message">
              {{ errors.validationErrors.phone.join(', ') }}
            </p>
        </div>

        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          
          <button type="submit" class="btn-primary flex items-center" :disabled="tutorData.id ? loading.update : loading.add">
            <span v-if="tutorData.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
            {{ tutorData.id ? 'Mettre à jour' : 'Ajouter' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import { useTutorsStore } from '@/stores/tutors';
import { storeToRefs } from 'pinia';

const props = defineProps({
  tutor: Object,
});

const emit = defineEmits(['close']);
const tutorsStore = useTutorsStore();
const { addTutor, updateTutor, clearErrors } = tutorsStore
const { errors, loading } = storeToRefs(tutorsStore);

const tutorData = ref({
  id: null,
  last_name: '',
  first_name: '',
  email: '',
  phone: '',
});

// Initialise les données au montage ou lorsqu'elles changent
watchEffect(() => {
  tutorData.value = props.tutor
    ? { ...props.tutor }
    : { id: null, last_name: '', first_name: '', email: '', phone: '' };
});

const submitForm = async () => {
  const success = tutorData.value.id
    ? await updateTutor(tutorData.value)
    : await addTutor(tutorData.value);

  if (success) {
    closeModal();
  }
};

const closeModal = () => {
  clearErrors('validationErrors'); // Réinitialiser uniquement les erreurs de validation
  emit("close");
};
</script>