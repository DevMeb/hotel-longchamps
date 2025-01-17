<template>
  <navbar></navbar>
  <div class="min-h-screen bg-gray-800 p-4">
    <div class="max-w-7xl mx-auto mt-6">
      <h1 class="text-3xl font-bold text-white mb-6">Tableau de bord</h1>

      <!-- Sélecteur de mois -->
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

      <!-- Statistiques principales -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <div
          v-for="(stat, index) in stats"
          :key="index"
          :class="stat.color"
          class="text-white p-6 rounded-lg shadow-md text-center"
        >
          <h2 class="text-4xl font-bold">{{ stat.value }}</h2>
          <p class="text-gray-200 mt-2">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Section dynamique -->
      <template v-for="(section, index) in dashboardSections" :key="index">
        <div v-if="section.data.length > 0" class="bg-gray-700 p-6 rounded-lg shadow-md mt-6">
          <h2 class="text-2xl font-bold text-white mb-4">{{ section.title }}</h2>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 border border-gray-700 text-sm text-left text-gray-200">
              <thead class="bg-gray-600 text-gray-300">
                <tr>
                  <th
                    v-for="(col, colIndex) in section.columns"
                    :key="colIndex"
                    class="py-3 px-4 border-b"
                  >
                    {{ col.label }}
                  </th>
                  <th class="py-3 px-4 border-b">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in section.data" :key="item.id" class="hover:bg-gray-700">
                  <td
                    v-for="(col, colIndex) in section.columns"
                    :key="colIndex"
                    class="py-3 px-4 border-b"
                  >
                    <!-- Formattage des dates -->
                    <template v-if="col.field === 'issued_at' || col.field === 'paid_at' || col.field === 'start_date' || col.field === 'end_date'">
                      {{ formatDateTime(item[col.field]) }}
                    </template>
                    <!-- Tags pour les statuts -->
                    <template v-else-if="col.field === 'status'">
                      <span :class="statusClass(item[col.field])" class="px-2 py-1 rounded-md text-xs font-semibold">
                        {{ statusText(item[col.field]) }}
                      </span>
                    </template>
                    <!-- Données normales -->
                    <template v-else>
                      {{ item[col.field] }}
                    </template>
                  </td>
                  <td class="py-3 px-4 border-b">
                    <button
                      v-for="action in section.actions"
                      :key="action.label"
                      :class="action.buttonClass"
                      @click="action.handler(item)"
                      class="mr-2 py-2 px-4 rounded-md flex items-center gap-2"
                    >
                      <span>{{ action.label }}</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import Navbar from '@/components/Navbar.vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const stats = ref([]);
const dashboardSections = ref([]);

const selectedMonth = ref(new Date().toISOString().slice(0, 7)); // Initialise au mois courant


/** Formate les dates au format d/m/Y H:i:s */
const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

/** Génère une classe CSS pour le statut */
const statusClass = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-500 text-white';
    case 'issued':
      return 'bg-blue-500 text-white';
    case 'pending':
      return 'bg-yellow-500 text-black';
    default:
      return 'bg-gray-500 text-white';
  }
};

/** Affiche un texte clair pour le statut */
const statusText = (status) => {
  switch (status) {
    case 'paid':
      return 'Envoyé et Payée';
    case 'issued':
      return 'Envoyée et en attente de paiement';
    case 'pending':
      return 'En attente d\'envoi';
    default:
      return 'Inconnu';
  }
};

const fetchDashboardData = async () => {
  try {
    const [year, month] = selectedMonth.value.split('-');
    const response = await axios.get('/api/dashboard-data', {
      params: { year, month },
    });

    stats.value = [
      { label: 'Réservations du mois courant', value: response.data.reservationCount, color: 'bg-gradient-to-r from-blue-500 to-blue-600' },
      { label: 'Factures générées ce mois', value: response.data.invoiceCount, color: 'bg-gradient-to-r from-indigo-500 to-indigo-600' },
      { label: 'CA potentiel à facturer', value: new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(response.data.potentialRevenue), color: 'bg-gradient-to-r from-green-500 to-green-600' },
      { label: 'CA facturé réellement', value: new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(response.data.actualRevenue), color: 'bg-gradient-to-r from-teal-500 to-teal-600' },
      { label: 'Différence entre CA généré et potentiel', value: new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(response.data.revenueDifference), color: 'bg-gradient-to-r from-red-500 to-red-600' },
    ];

    dashboardSections.value = [
      {
        title: 'Factures envoyées et payées',
        data: response.data.sentPaidInvoices,
        columns: [
          { label: 'Facture ID', field: 'id' },
          { label: 'Sujet', field: 'subject' },
          { label: 'Statut', field: 'status' },
          { label: 'Date d\'envoi', field: 'issued_at' },
          { label: 'Date de paiement', field: 'paid_at' },
          { label: 'Montant (€)', field: 'amount' },
        ],
        actions: [
          { label: 'Voir la réservation', buttonClass: 'bg-indigo-600 hover:bg-indigo-700 text-white', handler: (item) => router.push({ name: 'Reservations', query: { id: item.reservation_id } }) },
          { label: 'Voir la facture', buttonClass: 'bg-blue-600 hover:bg-blue-700 text-white', handler: (item) => router.push({ name: 'Invoices', query: { id: item.id } }) },
        ],
      },
      {
        title: 'Réservations sans facture générée',
        data: response.data.reservationsWithoutInvoices,
        columns: [
          { label: 'Réservation ID', field: 'id' },
          { label: 'Date de début', field: 'start_date' },
          { label: 'Date de fin', field: 'end_date' },
          { label: 'Locataire', field: 'renter_name' },
          { label: 'Chambre', field: 'room_name' },
          { label: 'Montant (€)', field: 'amount' },
        ],
        actions: [
          { label: 'Voir la réservation', buttonClass: 'bg-indigo-600 hover:bg-indigo-700 text-white', handler: (item) => router.push({ name: 'Reservations', query: { id: item.id } }) },
        ],
      },
      {
        title: 'Factures envoyées mais non payées',
        data: response.data.sentUnpaidInvoices,
        columns: [
          { label: 'Facture ID', field: 'id' },
          { label: 'Réservation ID', field: 'reservation_id' },
          { label: 'Sujet', field: 'subject' },
          { label: 'Statut', field: 'status' },
          { label: 'Date d\'envoi', field: 'issued_at' },
          { label: 'Montant (€)', field: 'amount' },
        ],
        actions: [
          { label: 'Voir la facture', buttonClass: 'bg-blue-600 hover:bg-blue-700 text-white', handler: (item) => router.push({ name: 'Invoices', query: { id: item.id } }) },
          { label: 'Voir la réservation', buttonClass: 'bg-indigo-600 hover:bg-indigo-700 text-white', handler: (item) => router.push({ name: 'Reservations', query: { id: item.reservation_id } }) },
        ],
      },
      {
        title: 'Factures non envoyées',
        data: response.data.unsentInvoices,
        columns: [
          { label: 'Facture ID', field: 'id' },
          { label: 'Réservation ID', field: 'reservation_id' },
          { label: 'Sujet', field: 'subject' },
          { label: 'Statut', field: 'status' },
          { label: 'Date d\'envoi', field: 'issued_at' },
          { label: 'Montant (€)', field: 'amount' },
        ],
        actions: [
          { label: 'Voir la facture', buttonClass: 'bg-blue-600 hover:bg-blue-700 text-white', handler: (item) => router.push({ name: 'Invoices', query: { id: item.id } }) },
          { label: 'Voir la réservation', buttonClass: 'bg-indigo-600 hover:bg-indigo-700 text-white', handler: (item) => router.push({ name: 'Reservations', query: { id: item.reservation_id } }) },
        ],
      },
    ];
  } catch (error) {
    console.error('Erreur lors du chargement des données du dashboard :', error);
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script>
