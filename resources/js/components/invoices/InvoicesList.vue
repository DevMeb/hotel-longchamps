<template>
    <div class="mt-6">
      <!-- 🔄 Chargement -->
      <div v-if="loading" class="flex justify-center my-6">
        <div class="animate-spin inline-block w-6 h-6 border-4 border-white border-t-transparent rounded-full"></div>
        <p class="text-white text-lg font-medium ml-2">Chargement...</p>
      </div>
  
      <!-- ❌ Erreur -->
      <div v-else-if="error" class="flex justify-center my-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <span class="text-xl">❌</span>
        <p class="text-lg font-medium ml-2">{{ error }}</p>
      </div>
  
      <!-- 📭 Aucune facture trouvée -->
      <div v-else-if="invoices.length === 0" class="flex justify-center my-6 bg-gray-800 px-6 py-4 rounded-lg shadow-lg">
        <p class="text-gray-300 text-lg">📭 Aucune facture trouvée.</p>
      </div>
  
      <!-- ✅ Liste des factures -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <InvoiceListItem 
          v-for="invoice in invoices"
          :invoice="invoice"
          :key="invoice.id"  
        />
      </div>
    </div>
</template>
  
<script setup>
  import { InvoiceListItem } from '@/components/invoices/';
  import { useInvoicesStore } from '@/stores/invoices';
  import { storeToRefs } from 'pinia';
  import { onMounted } from 'vue';

  const invoicesStore = useInvoicesStore();
  const { invoices, error, loading } = storeToRefs(invoicesStore);
  const { fetchInvoices } = invoicesStore;

  onMounted(() => {
    fetchInvoices()
  })
</script>
  