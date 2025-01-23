<template>
  <div v-if="hasInvoices" class="invoice-container">
    <!-- 🔽 Bouton pour afficher/masquer les factures -->
    <button @click="toggleExpand" class="toggle-button">
      <span class="text-lg">📜 Factures</span>
      <span :class="{ 'rotate-180': isExpanded }" class="transition-transform">🔽</span>
    </button>

    <!-- 📑 Liste des factures -->
    <div v-if="isExpanded" class="invoice-list">
      <div v-for="(invoices, status) in invoicesByStatus" :key="status" class="invoice-group">
        <!-- 🏷️ Statut de la facture -->
        <h4 class="invoice-status">
          <span class="status-label">
            {{ statusText(status) }}
          </span>
        </h4>

        <ul class="invoice-items">
          <li 
            v-for="invoice in invoices" 
            :key="invoice.id" 
            class="invoice-item"
          >
            <div class="invoice-details">
              <div class="invoice-subject">
                <span class="text-gray-400">📝</span>
                <span class="text-gray-300">
                  #{{ invoice.id }} - {{ invoice.subject || `Facture #${invoice.id}` }}
                </span>
              </div>

              <div class="invoice-dates">
                📅 {{ formatDateToFR(invoice.billing_start_date) }} → {{ formatDateToFR(invoice.billing_end_date) || "En cours" }}
              </div>
            </div>

            <div class="invoice-actions">
              <span class="invoice-amount">
                {{ formatAmount(invoice.amount) }}
              </span>

              <!-- Bouton Voir PDF -->
              <button @click.stop="openInvoicePdf(invoice)" class="pdf-button">
                📄 PDF
              </button>

              <!-- Bouton Envoyer Email pour les factures en attente -->
              <button 
                v-if="status === 'pending'" 
                @click.stop="openInvoiceEmail(invoice)" 
                class="email-button"
              >
                📧 Email
              </button>

              <!-- ✅ Bouton "Payé" avec chargement individuel -->
              <button 
              v-if="invoice.status === 'issued'" 
              @click="markAsPaid(invoice)" 
              :disabled="loadingInvoices[invoice.id]"
              class="btn-action bg-green-500 disabled:opacity-50 flex items-center"
              >
              <span v-if="loadingInvoices[invoice.id]" class="animate-spin border-4 border-white border-t-transparent rounded-full w-4 h-4 mr-2"></span>
              ✅ Payé
              </button>

            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div v-else class="no-invoices">
    🚫 Aucune facture générée
  </div>

  <!-- Modale de visualisation du PDF -->
  <InvoicePdfModal v-if="selectedInvoice" :invoice="selectedInvoice" @close="selectedInvoice = null" />

  <!-- Modale d'envoi d'email -->
  <InvoiceSendMailModal v-if="selectedInvoiceForEmail" :invoice="selectedInvoiceForEmail" @close="selectedInvoiceForEmail = null" />
</template>

<script setup>
import { ref, computed } from "vue";
import { formatAmount, formatDateToFR } from "@/utils";
import { InvoicePdfModal, InvoiceSendMailModal } from '@/components/invoices/';
import { useInvoicesStore } from '@/stores/invoices';

const invoicesStore = useInvoicesStore();
const { invoicePaid } = invoicesStore;

const props = defineProps({
  invoices: Object, // Factures groupées par statut
});

const isExpanded = ref(false);
const selectedInvoice = ref(null);
const selectedInvoiceForEmail = ref(null);
const loadingInvoices = ref({}); // État de chargement individuel pour chaque facture

// ✅ Fonction pour afficher ou masquer la liste des factures
const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
};

// ✅ Fonction pour ouvrir la modale PDF avec la facture sélectionnée
const openInvoicePdf = (invoice) => {
  selectedInvoice.value = invoice;
};

// ✅ Fonction pour ouvrir la modale Email avec la facture sélectionnée
const openInvoiceEmail = (invoice) => {
  selectedInvoiceForEmail.value = invoice;
};

// ✅ Fonction pour marquer une facture comme payée
async function markAsPaid(invoice) {
  if (loadingInvoices.value[invoice.id]) return; // Empêche l'action si déjà en cours

  loadingInvoices.value = { ...loadingInvoices.value, [invoice.id]: true }; // Active le chargement pour cette facture
  await invoicePaid(invoice);
  loadingInvoices.value = { ...loadingInvoices.value, [invoice.id]: false }; // Désactive après l'opération
}

// ✅ Vérifie si la réservation a des factures
const hasInvoices = computed(() => Object.keys(props.invoices || {}).length > 0);

// ✅ Regroupe et trie les factures par statut
const invoicesByStatus = computed(() => {
  if (!props.invoices) return {};

  const statusPriority = { paid: 1, issued: 2, pending: 3 }; // Ordre des statuts
  const sortedStatuses = Object.keys(props.invoices).sort(
    (a, b) => (statusPriority[a] || 99) - (statusPriority[b] || 99)
  );

  // Générer un nouvel objet trié
  const sortedInvoices = {};
  sortedStatuses.forEach(status => {
    sortedInvoices[status] = props.invoices[status];
  });

  return sortedInvoices;
});


// ✅ Convertit le statut en texte lisible avec emoji
const statusText = (status) => {
  const statuses = {
    pending: "🟡 En attente",
    issued: "🟢 Émise",
    paid: "🔵 Payée",
  };
  return statuses[status] || "⚪ Inconnu";
};
</script>


<style scoped>
/* ✅ Conteneur principal */
.invoice-container {
  @apply mt-4 bg-gray-900 p-4 rounded-lg border border-gray-700;
}

/* ✅ Bouton pour afficher les factures */
.toggle-button {
  @apply w-full flex items-center justify-between bg-gray-700 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition-all;
}

/* ✅ Liste des factures */
.invoice-list {
  @apply mt-3 flex flex-col gap-4;
}

/* ✅ Groupe de factures par statut */
.invoice-group {
  @apply mt-2;
}

/* ✅ Label du statut */
.invoice-status {
  @apply text-xl font-semibold text-gray-200 flex items-center;
}

.status-label {
  @apply bg-gray-500 text-white px-2 py-1 rounded-md text-xs;
}

/* ✅ Liste des éléments de facture */
.invoice-items {
  @apply mt-1 space-y-2;
}

/* ✅ Élément de facture */
.invoice-item {
  @apply flex justify-between items-center bg-gray-800 p-4 rounded-md cursor-pointer transition-all duration-200 hover:bg-gray-700 hover:shadow-md;
}

/* ✅ Détails de la facture */
.invoice-details {
  @apply flex flex-col gap-1;
}

/* ✅ Sujet de la facture */
.invoice-subject {
  @apply flex items-center space-x-2 text-gray-300;
}

/* ✅ Dates de la facture */
.invoice-dates {
  @apply text-sm text-gray-400;
}

/* ✅ Montant de la facture */
.invoice-amount {
  @apply text-indigo-300 font-semibold transition-all duration-200;
}

/* ✅ Actions de la facture */
.invoice-actions {
  @apply flex items-center gap-4;
}

/* ✅ Bouton Voir PDF */
.pdf-button {
  @apply bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-400 transition-all;
}

/* ✅ Bouton Envoyer Email */
.email-button {
  @apply bg-blue-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-blue-400 transition-all;
}

/* ✅ Message d'absence de factures */
.no-invoices {
  @apply mt-4 text-red-400 text-center;
}

/* ✅ Animation de rotation */
.rotate-180 {
  transform: rotate(180deg);
}
</style>
