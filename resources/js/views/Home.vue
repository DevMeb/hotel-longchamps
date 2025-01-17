<template>
  <navbar></navbar>
  <div class="bg-gray-900 min-h-screen p-6">
    <h1 class="text-3xl font-bold text-white mb-6">Tableau de Bord</h1>

    <!-- Sélection du mois et de l'année -->
    <div class="flex items-center justify-end mb-6">
      <label for="month-selector" class="text-white mr-4">Sélectionner un mois :</label>
      <input
        type="month"
        id="month-selector"
        v-model="selectedMonth"
        @change="fetchDashboardData"
        class="bg-gray-700 text-white rounded-md px-4 py-2 border border-gray-600"
      />
    </div>

    <!-- Résumé général -->
    <div class="grid grid-cols-3 gap-4 text-white mb-6">
      <div class="bg-blue-600 p-4 rounded shadow-md">
        Total Réservations : {{ dashboard.total_reservations }}
      </div>
      <div class="bg-green-600 p-4 rounded shadow-md">
        Montant Total Facturé : {{ (dashboard.total_actual_amount / 100).toFixed(2) }} €
      </div>
      <div class="bg-yellow-600 p-4 rounded shadow-md">
        Différence Totale : {{ (dashboard.total_difference / 100).toFixed(2) }} €
      </div>
    </div>

    <!-- Détails des réservations et factures -->
    <div class="grid grid-cols-1 gap-6">
      <div 
        v-for="reservation in dashboard.reservations" 
        :key="reservation.id" 
        class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700"
      >
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-indigo-400 flex items-center">
            <span class="mr-2">🏠</span> Chambre {{ reservation.room.name }}
          </h2>
          <span class="text-sm bg-gray-700 text-white px-3 py-1 rounded-full">
            {{ reservation.end_date ? 'Terminée' : 'En cours' }}
          </span>
        </div>

        <div class="mt-2 space-y-2">
          <p class="text-gray-300 flex items-center"><span class="mr-2">👤</span>Locataire : <span class="font-semibold text-white ml-1">{{ reservation.renter.first_name }} {{ reservation.renter.last_name }}</span></p>
          <p class="text-gray-300 flex items-center"><span class="mr-2">📅</span>Période : <span class="ml-1">{{ formatDate(reservation.start_date) }} - {{ reservation.end_date ? formatDate(reservation.end_date) : 'En cours' }}</span></p>
          <p class="text-gray-300 flex items-center"><span class="mr-2">💰</span>Montant prévisionnel : <span class="ml-1 text-yellow-400 font-semibold">{{ (reservation.expected_amount / 100).toFixed(2) }} €</span></p>
          <p class="text-gray-300 flex items-center"><span class="mr-2">💵</span>Montant facturé : <span class="ml-1 text-green-400 font-semibold">{{ (reservation.actual_amount / 100).toFixed(2) }} €</span></p>
          <p class="text-gray-300 flex items-center"><span class="mr-2">⚖️</span>Différence : <span class="ml-1 text-red-400 font-semibold">{{ (reservation.difference / 100).toFixed(2) }} €</span></p>
        </div>

        <!-- Section des factures -->
        <div v-if="Object.keys(reservation.invoices).length > 0" class="mt-4">
          <button 
            @click="toggleInvoices(reservation.id)" 
            class="w-full flex items-center justify-between bg-gray-700 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition-all">
            <span class="text-lg">📜 Factures</span>
            <span :class="{'rotate-180': expandedReservation === reservation.id}" class="transition-transform">🔽</span>
          </button>

          <div 
            v-if="expandedReservation === reservation.id" 
            class="mt-3 bg-gray-900 p-4 rounded-lg border border-gray-700"
          >
            <div 
              v-for="(invoices, status) in reservation.invoices" 
              :key="status" 
              class="mt-2"
            >
              <h4 class="text-md font-semibold text-gray-200 flex items-center">
                <span class="bg-gray-500 text-white px-2 py-1 rounded-md text-xs">{{ statusText(status) }}</span>
              </h4>
              <ul class="mt-1 space-y-2">
                <li 
                  v-for="invoice in invoices" 
                  :key="invoice.id" 
                  class="flex justify-between items-center bg-gray-800 p-3 rounded-md"
                >
                  <span class="text-gray-300">
                    - {{ invoice.subject || `Facture #${invoice.id}` }}
                  </span>
                  <span class="text-indigo-300 font-semibold">{{ (invoice.amount / 100).toFixed(2) }} €</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-else class="mt-4 text-red-400 text-center">🚫 Aucune facture générée</div>
      </div>
    </div>

  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

// Sélection du mois et de l'année
const selectedMonth = ref(new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0'));
const dashboard = ref([]);

const expandedReservation = ref(null);

const toggleInvoices = (reservationId) => {
  expandedReservation.value = expandedReservation.value === reservationId ? null : reservationId;
};

// Charger les données depuis l'API
const fetchDashboardData = async () => {
  try {
    const [year, month] = selectedMonth.value.split('-');
    const response = await axios.get(`/api/dashboard?month=${month}&year=${year}`);
    dashboard.value = response.data;
  } catch (error) {
    console.error('Erreur lors du chargement du tableau de bord', error);
  }
};

// Fonction pour afficher les statuts des factures de manière lisible
const statusText = (status) => {
  const statuses = {
    pending: '🟡 En attente',
    issued: '🟢 Émise',
    paid: '🔵 Payée'
  };
  return statuses[status] || '⚪ Inconnu';
};

// Formater les dates en `DD/MM/YYYY`
const formatDate = (dateString) => {
  if (!dateString) return "En cours";
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR");
};

onMounted(fetchDashboardData);

const goToReservation = (reservationId) => {
  router.push({ name: 'Reservations', query: { id: reservationId } });
};

const goToInvoice = (invoiceId) => {
  router.push({ name: 'Invoices', query: { id: invoiceId } });
};
</script>

<style scoped>
.grid-cols-3 div {
  text-align: center;
  font-weight: bold;
}
</style>
