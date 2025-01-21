<template>
  <navbar></navbar>
  <div class="bg-gray-900">
    <div class="mx-auto max-w-7xl">
      <div class="bg-gray-900 py-10">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-white">Factures</h1>
              <p class="mt-2 text-sm text-gray-300">
                Liste de toutes les factures, y compris le sujet, les dates, le statut, et les actions.
              </p>
            </div>
          </div>
          <!-- 📋 Liste des factures -->
          <InvoicesList />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { onMounted } from 'vue';
import { useInvoicesStore } from '@/stores/invoices';

import { InvoicesList } from '@/components/invoices/';

const invoicesStore = useInvoicesStore();
const { fetchInvoices } = invoicesStore;

onMounted(() => {
  fetchInvoices();
});
</script>
