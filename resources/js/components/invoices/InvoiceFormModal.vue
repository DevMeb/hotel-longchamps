<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
      <!-- Overlay pour fermer la modale -->
      <div @click.self="closeModal" class="fixed inset-0"></div>
  
      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
        <!-- ✨ Titre -->
        <div class="flex items-center justify-between border-b pb-2">
          <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            🧾 Ajouter une facture
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
        </div>
  
        <!-- 📋 Formulaire -->
        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <!-- Informations de la réservation (non modifiables) -->
          <div class="bg-gray-100 p-3 rounded-lg">
            <p class="text-sm text-gray-700"><strong>Réservation #:</strong> {{ invoiceData.reservation_id }}</p>
            <p class="text-sm text-gray-700"><strong>Locataire:</strong> {{ invoiceData.renter_name }}</p>
            <p class="text-sm text-gray-700"><strong>Chambre:</strong> {{ invoiceData.room_name }}</p>
          </div>
  
          <!-- Sujet de la facture -->
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
            <input type="text" v-model="invoiceData.subject" class="input-field" placeholder="Ex: Loyer janvier 2024">
            <p v-if="errors.subject" class="error-message">{{ errors.subject?.join(' ') }}</p>
          </div>
  
          <!-- Dates de facturation -->
          <div>
            <label for="billing_start_date" class="block text-sm font-medium text-gray-700">Début de facturation</label>
            <input type="date" v-model="invoiceData.billing_start_date" class="input-field">
            <p v-if="errors.billing_start_date" class="error-message">{{ errors.billing_start_date?.join(' ') }}</p>
          </div>
  
          <div>
            <label for="billing_end_date" class="block text-sm font-medium text-gray-700">Fin de facturation</label>
            <input type="date" v-model="invoiceData.billing_end_date" class="input-field">
            <p v-if="errors.billing_end_date" class="error-message">{{ errors.billing_end_date?.join(' ') }}</p>
          </div>
  
          <!-- ⚡️ Boutons d'action -->
          <div class="flex justify-end space-x-3 mt-4">
            <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary flex items-center" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="animate-spin mr-2">⏳</span>
              Ajouter
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, defineProps, defineEmits } from "vue";
  import { useToast } from "vue-toastification";
  import { useInvoicesStore } from '@/stores/invoices';
  
  const invoicesStore = useInvoicesStore()
  const { addInvoice } = invoicesStore

  const props = defineProps({
    show: Boolean,
    invoice: Object,
  });
  
  const emit = defineEmits(["close"]);
  const toast = useToast();
  
  const invoiceData = ref({
    reservation_id: null,
    renter_name: '',
    room_name: '',
    subject: '',
    billing_start_date: '',
    billing_end_date: '',
  });
  
  const errors = ref({});
  const isSubmitting = ref(false);
  
  // 🎯 Synchroniser les données de l'invoice reçue
  watch(() => props.invoice, (newInvoice) => {
    if (newInvoice) {
        invoiceData.value = {
        reservation_id: newInvoice.reservation_id,
        renter_name: newInvoice.renter_name,
        room_name: newInvoice.room_name,
        subject: newInvoice.subject || '',
        billing_start_date: newInvoice.billing_start_date || '',
        billing_end_date: newInvoice.billing_end_date || '',
        status: newInvoice.status,
        };
    }
  }, { immediate: true }); // 🔥 Ajout de `deep: true`

  
  // 📌 Envoi du formulaire
  const submitForm = async () => {
    try {
      isSubmitting.value = true;
      let response;
        if (invoiceData.value.id) {
        // 🔄 Mise à jour de la facture existante
        response = await updateInvoice(invoiceData.value);
        toast.success("Facture mise à jour avec succès !");
        } else {
        // ➕ Ajout d'une nouvelle facture
        response = await addInvoice(invoiceData.value);
        toast.success("Facture ajoutée avec succès !");
        }
        closeModal();
    } catch (err) {
      if (err.response?.data?.errors) {
        errors.value = err.response.data.errors;
        toast.error("Des erreurs de validation ont été détectées.");
      } else {
        toast.error(err);
      }
    } finally {
      isSubmitting.value = false;
    }
  };
  
  // 📌 Fermeture de la modale
  const closeModal = () => {
    emit("close");
  };
  </script>
  
  <style scoped>
  .input-field {
    @apply block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2;
  }
  .error-message {
    @apply mt-2 text-sm text-red-600;
  }
  .btn-primary {
    @apply px-4 py-2 bg-indigo-500 text-white rounded-md font-semibold hover:bg-indigo-400 transition disabled:opacity-50 disabled:cursor-not-allowed;
  }
  .btn-secondary {
    @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
  }
  </style>
  