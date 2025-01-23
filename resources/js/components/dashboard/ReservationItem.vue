<template>
  <div class="reservation-card">
    <!-- 🏷️ En-tête avec le numéro de la réservation -->
    <div class="header">
      <h2 class="reservation-id">🛎️ Réservation #{{ reservation.id }}</h2>
      <StatusBadge :status="reservation.end_date ? 'completed' : 'ongoing'" />
    </div>

    <!-- 📌 Section Locataire & Chambre -->
    <div class="section">
      <div class="section-title">🏠 Chambre & Locataire</div>
      <div class="section-content">
        <p class="text-lg font-semibold text-indigo-400">{{ reservation.room.name }}</p>
        <p class="text-white">{{ reservation.renter.first_name }} {{ reservation.renter.last_name }}</p>
      </div>
    </div>

    <!-- 📅 Section Dates -->
    <div class="section">
      <div class="section-title">📅 Période de Réservation</div>
      <div class="section-content">
        <p class="text-white">{{ formatDateToFR(reservation.start_date) }} → {{ reservation.end_date ? formatDateToFR(reservation.end_date) : "En cours" }}</p>
      </div>
    </div>

    <!-- 💰 Section Montants -->
    <div class="section">
      <div class="section-title">💰 Informations Financières</div>
      <div class="finance-grid">
        <div class="finance-box">
          <p class="label">💰 Montant prévu</p>
          <p class="value text-yellow-400">{{ formatAmount(reservation.expected_amount) }}</p>
        </div>
        <div class="finance-box">
          <p class="label">💵 Montant facturé</p>
          <p class="value text-green-400">{{ formatAmount(reservation.actual_amount) }}</p>
        </div>
        <div class="finance-box">
          <p class="label">⚖️ Différence</p>
          <p class="value text-red-400">{{ formatAmount(reservation.difference) }}</p>
        </div>
      </div>
    </div>

    <!-- 📜 Factures -->
    <InvoiceList :invoices="reservation.invoices" />
  </div>
</template>

<script setup>
import InvoiceList from "@/components/dashboard/InvoiceList.vue";
import StatusBadge from "@/components/dashboard/StatusBadge.vue";
import { formatDateToFR, formatAmount } from "@/utils";

defineProps({
  reservation: Object,
});
</script>

<style scoped>
/* ✅ Style général de la carte */
.reservation-card {
  @apply bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700 transition-all 
         duration-200 hover:bg-gray-700 cursor-pointer hover:shadow-xl flex flex-col gap-4;
}

/* ✅ En-tête avec le numéro de réservation */
.header {
  @apply flex justify-between items-center;
}

.reservation-id {
  @apply text-lg font-bold text-gray-300 uppercase tracking-wide;
}

/* ✅ Sections */
.section {
  @apply bg-gray-900 p-4 rounded-md flex flex-col gap-2;
}

.section-title {
  @apply text-xs uppercase font-bold text-gray-400;
}

.section-content {
  @apply text-white font-medium;
}

/* ✅ Montants financiers */
.finance-grid {
  @apply grid grid-cols-3 gap-4;
}

.finance-box {
  @apply flex flex-col;
}

.label {
  @apply text-gray-400 text-xs uppercase font-semibold;
}

.value {
  @apply text-lg font-semibold;
}
</style>
