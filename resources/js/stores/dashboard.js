import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import axios from "axios";

export const useDashboardStore = defineStore("dashboard", () => {
  const dashboardData = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const selectedMonth = ref(new Date().toISOString().slice(0, 7)); // Format YYYY-MM

  // 🔄 Chargement des données
  const fetchDashboardData = async () => {
    try {
      loading.value = true;
      error.value = null;
      const [year, month] = selectedMonth.value.split("-");
      const response = await axios.get(`/api/dashboard?month=${month}&year=${year}`);
      dashboardData.value = response.data;
    } catch (err) {
      error.value = "Erreur lors du chargement des données.";
      console.error(err);
    } finally {
      loading.value = false;
    }
  };

  function updateInvoiceInDashboard(invoiceId, updatedInvoice, previousStatus) {
    if (!dashboardData.value || !dashboardData.value.reservations) return;
  
    // Trouver la réservation concernée
    const reservation = dashboardData.value.reservations.find(res => res.id === updatedInvoice.reservation_id);
    if (!reservation || !reservation.invoices) return;
  
    // Supprimer la facture de son ancien statut (qu'on connaît déjà)
    reservation.invoices[previousStatus] = reservation.invoices[previousStatus].filter(inv => inv.id !== invoiceId);

    // ✅ Supprimer complètement la clé du statut si le tableau est vide
    if (reservation.invoices[previousStatus].length === 0) {
      delete reservation.invoices[previousStatus];
    }

    // Ajouter la facture sous son nouveau statut
    if (!reservation.invoices[updatedInvoice.status]) {
      reservation.invoices[updatedInvoice.status] = [];
    }
    reservation.invoices[updatedInvoice.status].push(updatedInvoice);

    recalculateReservationAmounts(reservation);
    recalculateDashboardTotals();
  } 

  function recalculateReservationAmounts(reservation) {
    if (!reservation) return;
  
    // 🔹 Calcul du montant total facturé (seulement les factures payées)
    const totalFactured = Object.values(reservation.invoices).flat()
      .filter(invoice => invoice.status === "paid")
      .reduce((sum, invoice) => sum + invoice.amount, 0); // ✅ Somme en centimes
  
    // 🔹 Montant attendu (déjà en centimes)
    const totalExpected = reservation.expected_amount;
  
    // 🔹 Mise à jour des valeurs en centimes
    reservation.actual_amount = totalFactured;
    reservation.difference = totalExpected - totalFactured;
  }  

  function recalculateDashboardTotals() {
    if (!dashboardData.value) return;
  
    let totalFactured = 0;
    let totalExpected = 0;
    let totalDifference = 0;
  
    dashboardData.value.reservations.forEach(reservation => {
      totalFactured += reservation.actual_amount;
      totalExpected += reservation.expected_amount;
      totalDifference += reservation.difference;
    });
  
    dashboardData.value.total_actual_amount = totalFactured;
    dashboardData.value.total_expected_amount = totalExpected;
    dashboardData.value.total_difference = totalDifference;
  }

  // 🔍 Recharge les données dès que `selectedMonth` change
  watch(selectedMonth, fetchDashboardData, { immediate: true });

  // 📊 Données calculées
  const totalReservations = computed(() => dashboardData.value?.total_reservations || 0);
  const totalFactured = computed(() => ((dashboardData.value?.total_actual_amount || 0) / 100).toFixed(2));
  const totalExpected = computed(() => ((dashboardData.value?.total_expected_amount || 0) / 100).toFixed(2));
  const totalDifference = computed(() => ((dashboardData.value?.total_difference || 0) / 100).toFixed(2));

  return {
    dashboardData,
    loading,
    error,
    selectedMonth,
    fetchDashboardData,
    totalReservations,
    totalFactured,
    totalDifference,
    totalExpected,
    updateInvoiceInDashboard,
  };
});
