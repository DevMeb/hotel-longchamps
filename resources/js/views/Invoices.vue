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

          <!-- 🎯 Filtres des factures -->
          <InvoiceFilters
            :selectedFilter="selectedFilter"
            :searchQuery="searchQuery"
            @updateFilter="handleFilterChange"
          />

          <!-- 📋 Liste des factures -->
          <InvoicesList
            @edit="editInvoice"
            @delete="confirmDeleteInvoice"
            @send="sendInvoiceByEmail"
            @markPaid="markInvoiceAsPaid"
            @view="displayInvoicePdf"
          />

          <!-- ❌ Modale de confirmation de suppression -->
          <InvoiceDeleteModal
            :show="showDeleteInvoiceModal"
            :invoice="invoiceToDelete"
            @close="showDeleteInvoiceModal = false"
          />

          <!-- 📄 Modale pour afficher le PDF -->
          <InvoicePdfModal
            :show="showPdfModal"
            :pdfUrl="pdfUrl"
            @close="showPdfModal = false"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { ref, computed, onMounted } from 'vue';
import { useInvoicesStore } from '@/stores/invoices';
import { storeToRefs } from 'pinia';
import { useToast } from 'vue-toastification';

import { InvoicesList, InvoiceFormModal, InvoiceDeleteModal, InvoiceFilters, InvoicePdfModal } from '@/components/invoices/';

const invoicesStore = useInvoicesStore();
const { invoices, error, loading } = storeToRefs(invoicesStore);
const { fetchInvoices, invoicePaid, getInvoicePdf } = invoicesStore;

const showFormInvoiceModal = ref(false);
const showDeleteInvoiceModal = ref(false);
const showPdfModal = ref(false);
const isEditing = ref(false);

const selectedInvoice = ref(null);
const invoiceToDelete = ref(null);
const pdfUrl = ref("");

const toast = useToast();

const selectedFilter = ref("status");
const searchQuery = ref("");

// 📌 Filtrer dynamiquement les factures
const filteredInvoices = computed(() => {
  return invoices.value.filter(invoice => {
    if (!searchQuery.value) return true;

    if (selectedFilter.value === "id") {
      return invoice.id == parseInt(searchQuery.value);
    }

    if (selectedFilter.value === "renter") {
      return invoice.reservation?.renter?.id === parseInt(searchQuery.value);
    } 

    if (selectedFilter.value === "status") {
      return invoice.status === searchQuery.value;
    }

    if (selectedFilter.value === "month_year") {
      const selectedDate = new Date(searchQuery.value);
      const invoiceDate = new Date(invoice.billing_start_date);
      return invoiceDate.getMonth() === selectedDate.getMonth() && invoiceDate.getFullYear() === selectedDate.getFullYear();
    }

    return true;
  });
});

onMounted(() => {
  fetchInvoices();
});

// 📌 Gérer la mise à jour des filtres
const handleFilterChange = ({ filter, query }) => {
  selectedFilter.value = filter;
  searchQuery.value = query;
};

// 📌 Édition d'une facture
const editInvoice = (invoice) => {
  selectedInvoice.value = {
    id: invoice.id,
    reservation_id: invoice.reservation.id,
    renter_name: `${invoice.reservation.renter.last_name.toUpperCase()} ${invoice.reservation.renter.first_name}`,
    room_name: invoice.reservation.room.name,
    subject: invoice.subject,
    billing_start_date: formatDateDMYtoYMD(invoice.billing_start_date) || '',
    billing_end_date: formatDateDMYtoYMD(invoice.billing_end_date) || '',
    description: invoice.description || '',
    status: 'pending',
  };

  isEditing.value = true;
  showFormInvoiceModal.value = true;
};

// ❌ Suppression d'une facture
const confirmDeleteInvoice = (invoice) => {
  invoiceToDelete.value = invoice;
  showDeleteInvoiceModal.value = true;
};

// 📨 Envoyer la facture par email
const sendInvoiceByEmail = (invoice) => {
  toast.info(`📧 Envoi de la facture ${invoice.id} par email en cours...`);
  // Ici, appel à l'API pour envoyer la facture
};

// ✅ Marquer une facture comme payée
const markInvoiceAsPaid = async (invoice) => {
  try {
    await invoicePaid(invoice.id);
    toast.success("Facture marquée comme payée !");
    fetchInvoices();
  } catch (err) {
    toast.error("Erreur lors de la mise à jour du statut.");
  }
};

// 📄 Voir la facture PDF
const displayInvoicePdf = async (invoiceId) => {
  try {
    pdfUrl.value = await getInvoicePdf(invoiceId);
    showPdfModal.value = true;
  } catch (err) {
    toast.error("Erreur lors de l'affichage de la facture.");
  }
};
</script>

<style scoped>
.filter-dropdown {
  @apply p-2 border rounded-md bg-gray-700 text-white focus:ring-2 focus:ring-indigo-500 transition;
}
</style>
