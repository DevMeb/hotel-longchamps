<template>
    <div class="bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col space-y-4 hover:shadow-2xl transition-all transform hover:scale-[1.03] border border-gray-700">
      <!-- 🏷️ Titre de la chambre -->
      <div class="flex justify-between items-center border-b pb-3">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
          🏠 {{ room.name.toUpperCase() }}
        </h2>
      </div>
  
      <!-- 📌 Informations générales -->
      <div class="bg-gray-900 p-4 rounded-lg space-y-3">
        <p class="text-gray-300 text-sm flex items-center">
          💰 <span class="ml-2 font-semibold text-white">Loyer :</span>
          <span class="text-indigo-400 ml-1">{{ room.rent }} € </span>
        </p>
      </div>
  
      <!-- ⏳ Dates clés -->
      <div class="bg-gray-900 p-4 rounded-lg flex flex-col space-y-2">
        <p class="text-gray-300 text-sm flex items-center">
          🕒 <span class="ml-2 font-semibold text-white">Créé le :</span>
          <span class="ml-1 text-indigo-300">{{ room.created_at }}</span>
        </p>
        <p class="text-gray-300 text-sm flex items-center">
          🔄 <span class="ml-2 font-semibold text-white">Mis à jour le :</span>
          <span class="ml-1 text-indigo-300">{{ room.updated_at }}</span>
        </p>
      </div>
  
      <!-- 🎯 Actions -->
      <div class="flex justify-center gap-3 mt-4">
        <button @click="openFormModal" class="btn-action bg-blue-800 hover:bg-blue-500">
          ✏️ Modifier
        </button>
        <button @click="openDeleteModal" class="btn-action bg-red-800 hover:bg-red-500">
          🗑️ Supprimer
        </button>
      </div>
    </div>
  
    <!-- Modals -->
    <RoomFormModal v-if="showFormModal" :room="room" @close="closeFormModal" />
    <RoomDeleteModal v-if="showDeleteModal" :room="room" @close="closeDeleteModal" />
  </template>
  
<script setup>
  import { ref } from "vue";
  import { RoomFormModal, RoomDeleteModal } from "@/components/rooms/";
  
  const props = defineProps({
    room: {
      type: Object,
      required: true,
    },
  });
  
  const showFormModal = ref(false);
  const showDeleteModal = ref(false);
  
  function openFormModal() {
    showFormModal.value = true;
  }
  
  function closeFormModal() {
    showFormModal.value = false;
  }
  
  function openDeleteModal() {
    showDeleteModal.value = true;
  }
  
  function closeDeleteModal() {
    showDeleteModal.value = false;
  }
</script>
  