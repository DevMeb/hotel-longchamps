<template>
  <navbar></navbar>
  <div class="bg-gray-900">
    <div class="mx-auto max-w-7xl">
      <div class="bg-gray-900 py-10">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-base font-semibold leading-6 text-white">Réservations</h1>
              <p class="mt-2 text-sm text-gray-300">
                Liste de toutes les réservations, y compris les locataires, chambres et dates.
              </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
              <button
                @click="showFormModal = true"
                type="button"
                class="block rounded-md bg-indigo-500 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-400"
              >
                Ajouter une réservation
              </button>
            </div>
          </div>

          <ReservationsList />

          <ReservationFormModal v-if="showFormModal" @close="showFormModal = false"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import { ref } from 'vue';
import { ReservationsList, ReservationFormModal } from '@/components/reservations/'

const showFormModal = ref(false)
</script>
