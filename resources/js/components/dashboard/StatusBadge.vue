<template>
    <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="badgeClass">
      {{ statusText }}
    </span>
  </template>
  
  <script setup>
  import { computed } from "vue";
  
  const props = defineProps({
    status: String, // Valeurs possibles : "ongoing", "completed", "pending", "issued", "paid"
  });
  
  // Génère la couleur du badge en fonction du statut
  const badgeClass = computed(() => {
    const statusColors = {
      ongoing: "bg-yellow-500 text-white",
      completed: "bg-gray-500 text-white",
      pending: "bg-yellow-500 text-white",
      issued: "bg-green-500 text-white",
      paid: "bg-blue-500 text-white",
    };
    return statusColors[props.status] || "bg-gray-300 text-gray-800";
  });
  
  // Texte lisible pour les statuts
  const statusText = computed(() => {
    const statuses = {
      ongoing: "En cours",
      completed: "Terminée",
      pending: "En attente",
      issued: "Émise",
      paid: "Payée",
    };
    return statuses[props.status] || "Inconnu";
  });
  </script>
  