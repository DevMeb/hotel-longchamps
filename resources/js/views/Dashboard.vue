<template>
  <Navbar />
  <div class="bg-gray-900 min-h-screen p-6">
    <h1 class="text-3xl font-bold text-white mb-6">Tableau de Bord</h1>

    <!-- 📅 Sélection Mois -->
    <MonthSelector />

    <!-- 📊 Résumé général -->
    <DashboardSummary />

    <!-- 🔄 Gestion du chargement et des erreurs -->
    <div v-if="loading" class="text-center text-white text-lg">Chargement...</div>
    <div v-else-if="error" class="text-center text-red-400">{{ error }}</div>

    <!-- 📌 Liste des réservations -->
    <div v-else class="grid grid-cols-1 gap-6">
      <ReservationItem 
        v-for="reservation in dashboardData?.reservations" 
        :key="reservation.id" 
        :reservation="reservation" 
      />
    </div>
  </div>
</template>

<script setup>
import Navbar from "@/components/Navbar.vue";
import { useDashboardStore } from "@/stores/dashboard";
import { storeToRefs } from "pinia";
import DashboardSummary from "@/components/dashboard/DashboardSummary.vue";
import ReservationItem from "@/components/dashboard/ReservationItem.vue";
import MonthSelector from "@/components/dashboard/MonthSelector.vue";

const dashboardStore = useDashboardStore();
const { dashboardData, loading, error, selectedMonth } = storeToRefs(dashboardStore);
</script>
