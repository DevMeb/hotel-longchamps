<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg shadow-lg w-4/5 h-4/5 relative">
        <!-- Bouton de fermeture -->
        <button @click="emit('close')" class="absolute top-3 right-3 text-gray-600 hover:text-gray-800">
          ✖
        </button>
  
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🧾 Facture #{{ invoiceId }}</h2>
  
        <!-- Affichage du PDF -->
        <iframe v-if="pdfUrl" :src="pdfUrl" class="w-full h-full"></iframe>
  
        <div v-else class="flex flex-col items-center justify-center h-full">
          <span class="text-gray-500 text-lg">📄 Chargement du PDF...</span>
          <div class="mt-4 animate-spin w-8 h-8 border-4 border-gray-300 border-t-transparent rounded-full"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  import { useInvoicesStore } from "@/stores/invoices";
  
  const props = defineProps({
    show: Boolean,
    invoiceId: Number,
  });
  
  const emit = defineEmits(["close"]);
  
  const invoicesStore = useInvoicesStore();
  const pdfUrl = ref("");
  
  watch(() => props.invoiceId, async (newInvoiceId) => {
    if (newInvoiceId) {
      try {
        pdfUrl.value = await invoicesStore.getInvoicePdf(newInvoiceId);
      } catch (error) {
        console.error("Erreur lors du chargement du PDF", error);
      }
    }
  }, { immediate: true });
  </script>
  