<template>
  <div class="bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col space-y-4 hover:shadow-xl transition-all transform hover:scale-[1.02] border border-gray-700">
    <!-- 🏷️ En-tête -->
    <div class="flex justify-between items-center border-b pb-3">
      <h2 class="text-xl font-semibold text-white flex items-center gap-2">
        🏨 Réservation #{{ reservation.id }}
      </h2>
      <span class="text-xs px-3 py-1 rounded-full font-semibold"
        :class="reservation.end_date ? 'bg-green-500 text-white' : 'bg-yellow-500 text-gray-900'">
        {{ reservation.end_date ? 'Terminée' : 'En cours' }}
      </span>
    </div>

    <!-- 📌 Informations générales -->
    <div class="bg-gray-900 p-4 rounded-md space-y-3">
      <p class="text-gray-300 text-sm flex items-center">
        👤 <span class="ml-2 font-semibold text-white">Locataire :</span> 
        <span class="text-indigo-400 ml-1">
          {{ reservation.renter.last_name.toUpperCase() }} {{ reservation.renter.first_name }}
        </span>
      </p>
      <p class="text-gray-300 text-sm flex items-center">
        📍 <span class="ml-2 font-semibold text-white">Chambre :</span> 
        <span class="text-indigo-400 ml-1">{{ reservation.room.name }}</span>
      </p>
    </div>

    <!-- ⏳ Dates clés -->
    <div class="bg-gray-900 p-4 rounded-lg flex flex-col space-y-2">
      <p class="text-gray-300 text-sm flex items-center">
        📅 <span class="ml-2 font-semibold text-white">Début :</span>
        <span class="ml-1 text-indigo-300">{{ reservation.start_date }}</span>
      </p>
      <p class="text-gray-300 text-sm flex items-center">
        📅 <span class="ml-2 font-semibold text-white">Fin :</span>
        <span class="ml-1 text-indigo-300">{{ reservation.end_date || 'En cours' }}</span>
      </p>
    </div>

    <!-- ⏳ Dates clés -->
    <div class="bg-gray-900 p-4 rounded-lg flex flex-col space-y-2">
      <p class="text-gray-300 text-sm flex items-center">
        📅 <span class="ml-2 font-semibold text-white">Crée le :</span>
        <span class="ml-1 text-indigo-300">{{ reservation.created_at }}</span>
      </p>
      <p class="text-gray-300 text-sm flex items-center">
        📅 <span class="ml-2 font-semibold text-white">Mise à jour :</span>
        <span class="ml-1 text-indigo-300">{{ reservation.updated_at }}</span>
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
      <button @click="openInvoiceFormModal" class="btn-action bg-green-500">
        🧾 Facture
      </button>
    </div>
  </div>

  <!-- Modals -->
  <ReservationFormModal v-if="showFormModal" :reservation="reservation" @close="closeFormModal" />
  <ReservationDeleteModal v-if="showDeleteModal" :reservation="reservation" @close="closeDeleteModal" />
  <InvoiceFormModal v-if="showInvoiceFormModal" :reservation="reservation" @close="closeInvoiceFormModal" />
</template>

<script setup>
import { ref } from "vue";
import { ReservationFormModal, ReservationDeleteModal } from "@/components/reservations/";
import { InvoiceFormModal } from '@/components/invoices/'

const props = defineProps({
  reservation: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["edit", "delete", "invoice"]);

const showFormModal = ref(false);
const showDeleteModal = ref(false);
const showInvoiceFormModal = ref(false);


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

function openInvoiceFormModal() {
  showInvoiceFormModal.value = true;
  }
  
function closeInvoiceFormModal() {
  showInvoiceFormModal.value = false;
}
</script>

<style scoped>
/* 🎨 Boutons actions */
.btn-action {
  @apply flex items-center px-4 py-2 text-white text-sm rounded-md transition hover:opacity-80;
}
</style>
