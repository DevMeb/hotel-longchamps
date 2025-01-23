<template>
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col space-y-4 hover:shadow-xl transition-all transform hover:scale-[1.02] border border-gray-700">
      <!-- 🏷️ En-tête -->
      <div class="flex justify-between items-center border-b pb-3">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
          👤 {{ tutor.last_name.toUpperCase() }} {{ tutor.first_name }}
        </h2>
      </div>
  
      <!-- 📌 Informations générales -->
      <div class="bg-gray-900 p-4 rounded-md space-y-3">
        <p class="text-gray-300 text-sm flex items-center">
          ✉️ <span class="ml-2 font-semibold text-white">Email :</span>
          <span class="text-indigo-400 ml-1">{{ tutor.email }}</span>
        </p>
        <p class="text-gray-300 text-sm flex items-center">
          📞 <span class="ml-2 font-semibold text-white">Téléphone :</span>
          <span class="text-indigo-400 ml-1">{{ tutor.phone || "Non renseigné" }}</span>
        </p>
      </div>
  
      <!-- ⏳ Dates clés -->
      <div class="bg-gray-900 p-4 rounded-lg flex flex-col space-y-2">
        <p class="text-gray-300 text-sm flex items-center">
          🕒 <span class="ml-2 font-semibold text-white">Créé le :</span>
          <span class="ml-1 text-indigo-300">{{ tutor.created_at }}</span>
        </p>
        <p class="text-gray-300 text-sm flex items-center">
          🔄 <span class="ml-2 font-semibold text-white">Mis à jour le :</span>
          <span class="ml-1 text-indigo-300">{{ tutor.updated_at }}</span>
        </p>
      </div>
  
      <!-- 🎯 Actions -->
      <div class="flex justify-center gap-3 mt-4">
        <button @click="openFormModal" class="btn-action bg-blue-500">
          ✏️ Modifier
        </button>
        <button @click="openDeleteModal" class="btn-action bg-red-500">
          🗑️ Supprimer
        </button>
      </div>
    </div>
  
    <!-- Modals -->
    <TutorFormModal v-if="showFormModal" :tutor="tutor" @close="closeFormModal" />
    <TutorDeleteModal v-if="showDeleteModal" :tutor="tutor" @close="closeDeleteModal" />
</template>
  
<script setup>
  import { ref } from "vue";
  import { TutorFormModal, TutorDeleteModal } from "@/components/tutors/";
  
  const props = defineProps({
    tutor: {
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
  