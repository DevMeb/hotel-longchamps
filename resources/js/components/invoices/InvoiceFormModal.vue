<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
    <!-- Overlay pour fermer la modale -->
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-[70%] transform transition-all animate-fade-in">
      <!-- ✨ Titre -->
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          {{ invoiceData.id ? "✏️ Éditer une facture" : "🧾 Ajouter une facture" }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
      </div>

      <!-- 📋 Formulaire -->
      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <!-- Informations de la réservation -->
        <div class="bg-gray-100 p-3 rounded-lg space-y-2">
          <p class="text-sm text-gray-700">
            <strong>Réservation #:</strong> {{ invoiceReservation?.id || "Non assignée" }}
          </p>
          <p class="text-sm text-gray-700">
            <strong>Locataire:</strong> 
            {{ invoiceReservation?.renter?.last_name.toUpperCase() }} 
            {{ invoiceReservation?.renter?.first_name }}
          </p>
          <p class="text-sm text-gray-700">
            <strong>Chambre:</strong> {{ invoiceReservation?.room?.name || "Non assignée" }}
          </p>
        </div>

        <!-- Sujet de la facture -->
        <div>
          <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
          <input 
            type="text" 
            v-model="invoiceData.subject" 
            class="input-field" 
            :class="{ 'border-red-500': errors.validationErrors?.subject }"
            placeholder="Ex: Loyer janvier 2024"
          >
          <p v-if="errors.validationErrors?.subject" class="error-message">{{ errors.validationErrors.subject.join(' ') }}</p>
        </div>

        <!-- Description -->
        <div v-if="invoiceData.id">
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea 
            rows="4" 
            v-model="invoiceData.description" 
            class="input-field"
            :class="{ 'border-red-500': errors.validationErrors?.description }"
          ></textarea>
          <p v-if="errors.validationErrors?.description" class="error-message">{{ errors.validationErrors.description.join(' ') }}</p>
        </div>

        <!-- Dates de facturation -->
        <div>
          <label for="billing_start_date" class="block text-sm font-medium text-gray-700">Début de facturation</label>
          <input 
            type="date" 
            v-model="invoiceData.billing_start_date" 
            class="input-field"
            :class="{ 'border-red-500': errors.validationErrors?.billing_start_date }"
          >
          <p v-if="errors.validationErrors?.billing_start_date" class="error-message">{{ errors.validationErrors.billing_start_date.join(' ') }}</p>
        </div>

        <div>
          <label for="billing_end_date" class="block text-sm font-medium text-gray-700">Fin de facturation</label>
          <input 
            type="date" 
            v-model="invoiceData.billing_end_date" 
            class="input-field"
            :class="{ 'border-red-500': errors.validationErrors?.billing_end_date }"
          >
          <p v-if="errors.validationErrors?.billing_end_date" class="error-message">{{ errors.validationErrors.billing_end_date.join(' ') }}</p>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          <button type="submit" class="btn-primary flex items-center" :disabled="invoiceData.id ? loading.update : loading.add">
            <span v-if="invoiceData.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
            {{ invoiceData.id ? "Mettre à jour" : "Ajouter" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect, computed } from "vue";
import { useInvoicesStore } from '@/stores/invoices';
import { formatDateToISO } from '@/utils';
import { storeToRefs } from "pinia";

const invoicesStore = useInvoicesStore();
const { addInvoice, updateInvoice, clearErrors } = invoicesStore;
const { errors, loading } = storeToRefs(invoicesStore);

const props = defineProps({
  invoice: Object,
  reservation: Object,
});

const invoiceReservation = computed(() => {
  return props.reservation || props.invoice?.reservation || null;
});

const emit = defineEmits(["close"]);

const invoiceData = ref({
  id: null,
  reservation_id: null,
  subject: '',
  description: '',
  billing_start_date: '',
  billing_end_date: '',
  status: 'pending',
});

watchEffect(() => {
  invoiceData.value = props.invoice
    ? {
        id: props.invoice.id,
        reservation_id: props.invoice.reservation?.id || null,
        subject: props.invoice.subject || '',
        description: props.invoice.description || '',
        billing_start_date: formatDateToISO(props.invoice.billing_start_date) || '',
        billing_end_date: formatDateToISO(props.invoice.billing_end_date) || '',
        status: 'pending', // Toujours "pending"
      }
    : {
        id: null,
        reservation_id: props.reservation?.id || null, // Affectation de la réservation transmise en props
        subject: '',
        description: '',
        billing_start_date: '',
        billing_end_date: '',
        status: 'pending', // Toujours "pending"
      };
});

const submitForm = async () => {
  const success = invoiceData.value.id
    ? await updateInvoice(invoiceData.value)
    : await addInvoice(invoiceData.value);

  if (success) {
    closeModal();
  }
};

const closeModal = () => {
  clearErrors('validationErrors');
  emit("close");
};
</script>
