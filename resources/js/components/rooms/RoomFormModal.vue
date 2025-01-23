<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50">
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <span v-if="roomData.id" class="mr-2">✏️</span>
          <span v-else class="mr-2">➕</span>
          {{ roomData.id ? 'Éditer la chambre' : 'Ajouter une chambre' }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
      </div>

      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input ref="firstInput" type="text" v-model="roomData.name"
            class="input-field" :class="{ 'border-red-500': errors.validationErrors?.name?.length > 0 }">
          <p v-if="errors.validationErrors?.name" class="error-message">
            {{ errors.validationErrors.name.join(', ') }}
          </p>        
        </div>
      
        <div>
          <label for="rent" class="block text-sm font-medium text-gray-700">Loyer (en €)</label>
          <input type="text" v-model="roomData.rent"
            class="input-field" :class="{ 'border-red-500': errors.validationErrors?.rent?.length > 0 }">
          <p v-if="errors.validationErrors?.rent" class="error-message">
            {{ errors.validationErrors.rent.join(', ') }}
          </p>        
        </div>
      
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
      
          <button type="submit" class="btn-primary flex items-center" :disabled="roomData.id ? loading.update : loading.add">
            <span v-if="roomData.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
            {{ roomData.id ? 'Mettre à jour' : 'Ajouter' }}
          </button>
        </div>
      </form>
      
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import { useRoomsStore } from '@/stores/rooms';
import { storeToRefs } from 'pinia';

const props = defineProps({
  room: Object,
});

const emit = defineEmits(['close']);
const roomStore = useRoomsStore();
const { addRoom, updateRoom, clearErrors } = roomStore
const { errors, loading } = storeToRefs(roomStore);

const roomData = ref({
  id: null,
  name: '',
  rent: '',
});

// Initialise les données au montage ou lorsqu'elles changent
watchEffect(() => {
  roomData.value = props.room
    ? { ...props.room }
    : { id: null, name: '', rent: '' };
});

const submitForm = async () => {
  const success = roomData.value.id
    ? await updateRoom(roomData.value)
    : await addRoom(roomData.value);

  if (success) {
    closeModal();
  }
};

const closeModal = () => {
  clearErrors('validationErrors'); // Réinitialiser uniquement les erreurs de validation
  emit("close");
};
</script>