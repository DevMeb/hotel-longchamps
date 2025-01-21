<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
      <!-- Overlay pour fermer la modale -->
      <div @click.self="closeModal" class="fixed inset-0"></div>
  
      <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-[70%] transform transition-all animate-fade-in">
        <!-- ✨ Titre -->
        <div class="flex items-center justify-between border-b pb-2">
          <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            {{ invoice.id ? "✏️ Éditer une facture" : "🧾 Ajouter une facture" }}
          </h2>          
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
        </div>
  
        <!-- 📋 Formulaire -->
        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <!-- Informations de la réservation (non modifiables) -->
          <div class="bg-gray-100 p-3 rounded-lg">
            <p class="text-sm text-gray-700"><strong>Réservation #:</strong> {{ invoice.reservation.id }}</p>
            <p class="text-sm text-gray-700">
              <strong>Locataire:</strong> 
              {{ invoice.reservation.renter.last_name.toUpperCase() }} 
              {{ invoice.reservation.renter.first_name }}
            </p>
            <p class="text-sm text-gray-700"><strong>Chambre:</strong> {{ invoice.reservation.room.name }}</p>
          </div>
  
          <!-- Sujet de la facture -->
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
            <input type="text" v-model="invoice.subject" class="input-field" placeholder="Ex: Loyer janvier 2024">
            <p v-if="errors.subject" class="error-message">{{ errors.subject?.join(' ') }}</p>
          </div>

          <div v-if="invoice.reservation.id">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea rows="5" v-model="invoice.description" class="input-field" />
            <p v-if="errors.description" class="error-message">{{ errors.subject?.join(' ') }}</p>
          </div>
  
          <!-- Dates de facturation -->
          <div>
            <label for="billing_start_date" class="block text-sm font-medium text-gray-700">Début de facturation</label>
            <input type="date" v-model="formattedBillingStartDate" class="input-field">
            <p v-if="errors.billing_start_date" class="error-message">{{ errors.billing_start_date?.join(' ') }}</p>
          </div>
  
          <div>
            <label for="billing_end_date" class="block text-sm font-medium text-gray-700">Fin de facturation</label>
            <input type="date" v-model="formattedBillingEndDate" class="input-field">
            <p v-if="errors.billing_end_date" class="error-message">{{ errors.billing_end_date?.join(' ') }}</p>
          </div>
  
          <!-- ⚡️ Boutons d'action -->
          <div class="flex justify-end space-x-3 mt-4">
            <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary flex items-center" :disabled="invoice.id ? loading.update : loading.add">
              <span v-if="invoice.id ? loading.update : loading.add" class="animate-spin mr-2">⏳</span>
              {{ invoice.id ? "Editer" : "Ajouter" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from "vue";
  import { useToast } from "vue-toastification";
  import { useInvoicesStore } from '@/stores/invoices';
  import { formatDateToISO } from '@/utils'
  import { storeToRefs } from "pinia";

  const invoicesStore = useInvoicesStore()
  const { addInvoice, updateInvoice } = invoicesStore
  const { loading } = storeToRefs(invoicesStore);

  const props = defineProps({
    invoice: Object,
  })
  
  const emit = defineEmits(["close"])
  const toast = useToast()
  
  const errors = ref({})

  const formattedBillingStartDate = computed(() => formatDateToISO(props.invoice.billing_start_date))
  const formattedBillingEndDate = computed(() => formatDateToISO(props.invoice.billing_end_date))
  
  // 📌 Envoi du formulaire
  const submitForm = async () => {
    try {
      // 🔄 Créer une copie de l'invoice et convertir les dates au format Y-m-d
      const formattedInvoice = {
        ...props.invoice,
        reservation_id: props.invoice.reservation ? props.invoice.reservation.id : null,
        billing_start_date: formatDateToISO(props.invoice.billing_start_date),
        billing_end_date: formatDateToISO(props.invoice.billing_end_date),
      };

      if (formattedInvoice.reservation_id) {
        await updateInvoice(formattedInvoice);
        toast.success("Facture mise à jour avec succès !");
      } else {
        await addInvoice(formattedInvoice);
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
    }
  };

  // 📌 Fermeture de la modale
  const closeModal = () => {
    emit("close");
  };
</script>
  
<style scoped>
  /* Animation d'apparition de la modale */
  @keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
    }

    .animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
    }
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
  