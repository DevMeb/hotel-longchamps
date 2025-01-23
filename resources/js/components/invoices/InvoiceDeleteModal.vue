<template>
  <div v-if="invoice" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Overlay cliquable pour fermer la modale -->
    <div @click.self="close" class="absolute inset-0"></div>

    <!-- Boîte de dialogue -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-[30rem] animate-fade-in transform transition-transform scale-95">
      <!-- En-tête -->
      <div class="flex items-center space-x-3">
        <span class="text-red-600 text-2xl">⚠️</span>
        <h2 class="text-lg font-semibold text-red-600">Confirmation de suppression</h2>
      </div>

      <!-- Contenu -->
      <p class="text-gray-700 mt-3">
        Êtes-vous sûr de vouloir supprimer la facture
        <strong class="text-gray-900">#{{ invoice.id }}</strong> ?
      </p>
      <p class="text-sm text-gray-500 mt-2">Cette action est <span class="font-semibold text-red-500">irréversible</span>.</p>

      <!-- Actions -->
      <div class="flex justify-end mt-6 space-x-3">
        <button 
          @click="close"
          class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100 transition"
        >
          Annuler
        </button>
        <button 
          @click="confirmDelete"
          class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition flex items-center"
        >
          <span class="mr-2">🗑️</span> Supprimer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useInvoicesStore } from '@/stores/invoices';
import { useToast } from 'vue-toastification';

const props = defineProps({
  invoice: Object,
});

const emit = defineEmits(["close"]);

const invoicesStore = useInvoicesStore();
const { deleteInvoice } = invoicesStore;

const toast = useToast();

function close() {
  emit('close');
}

async function confirmDelete() {
  try {
    await deleteInvoice(props.invoice.id);
    toast.success("Facture supprimée avec succès.");
    close();
  } catch (err) {
    toast.error("Une erreur est survenue lors de la suppression.");
  }
}
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
  .input-field {
    @apply block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2;
  }
  .error-message {
    @apply mt-2 text-sm text-red-600;
  }
  .btn-primary {
    @apply px-4 py-2 bg-indigo-500 text-white rounded-md font-semibold hover:bg-indigo-400 transition disabled:opacity-50 disabled:cursor-not-allowed;
  }
  .btn-secondary {
    @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
  }
</style>
