<template>
  <navbar></navbar>
  <div class="bg-gray-900 min-h-screen">
    <div class="mx-auto max-w-7xl py-10 px-4 sm:px-6 lg:px-8">
      <!-- 🏨 En-tête -->
      <div class="sm:flex sm:items-center justify-between">
        <div class="sm:flex-auto">
          <h1 class="text-lg font-semibold leading-6 text-white">Chambres</h1>
          <p class="mt-2 text-sm text-gray-300">
            Gérez les chambres de votre hôtel avec leurs noms et loyers.
          </p>
        </div>
        <button @click="showFormModal = true" class="bg-indigo-500 text-white px-4 py-2 rounded-md">➕ Ajouter une chambre</button>
      </div>

      <!-- 📋 Liste des chambres -->
      <RoomList />

      <RoomFormModal v-if="showFormModal" @close="showFormModal = false" />
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { RoomList, RoomFormModal } from '@/components/rooms/';
import { ref } from 'vue';

const showFormModal = ref(false);
</script>
