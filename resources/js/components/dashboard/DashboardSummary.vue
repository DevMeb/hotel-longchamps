<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-white mb-8 mt-8">
    <!-- Total Réservations -->
    <div class="dashboard-card bg-blue-500">
      <div class="icon-container">
        📌
      </div>
      <div class="info">
        <p class="title">Total Réservations</p>
        <p class="value">{{ totalReservations }}</p>
      </div>
    </div>

    <!-- Montant Facturé -->
    <div class="dashboard-card bg-green-500">
      <div class="icon-container">
        💰
      </div>
      <div class="info">
        <p class="title">Montant Facturé</p>
        <p class="value">{{ totalFactured }} €</p>
      </div>
    </div>

    <!-- Montant Attendu -->
    <div class="dashboard-card bg-yellow-500">
      <div class="icon-container">
        📅
      </div>
      <div class="info">
        <p class="title">Montant Attendu</p>
        <p class="value">{{ totalExpected }} €</p>
      </div>
    </div>

    <!-- Différence Totale -->
    <div class="dashboard-card bg-red-500">
      <div class="icon-container">
        ⚖️
      </div>
      <div class="info">
        <p class="title">Différence Totale</p>
        <p class="value">{{ totalDifference }} €</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useDashboardStore } from "@/stores/dashboard";
import { storeToRefs } from "pinia";

const dashboardStore = useDashboardStore();
const { totalReservations, totalFactured, totalDifference, totalExpected } = storeToRefs(dashboardStore);
</script>

<style scoped>
/* ✅ Style des cartes */
.dashboard-card {
  @apply flex items-center space-x-4 p-6 rounded-lg shadow-md transition-all transform hover:scale-105 hover:shadow-xl;
}

/* ✅ Icône dans un conteneur rond */
.icon-container {
  @apply text-4xl bg-white bg-opacity-20 rounded-full p-3;
}

/* ✅ Style du texte */
.info {
  @apply flex flex-col;
}

.title {
  @apply text-sm font-semibold uppercase opacity-80;
}

.value {
  @apply text-2xl font-bold;
}
</style>
