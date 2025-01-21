<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
    <!-- Overlay cliquable pour fermer la modale -->
    <div @click.self="closeModal" class="fixed inset-0"></div>

    <div class="bg-white p-6 rounded-xl shadow-xl w-[90%] sm:w-96 transform transition-all animate-fade-in">
      <!-- ✨ Titre de la modale -->
      <div class="flex items-center justify-between border-b pb-2">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          📩 Envoyer la facture par email
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition">✖️</button>
      </div>

      <!-- 📋 Formulaire -->
      <form @submit.prevent="submitForm" class="mt-4 space-y-4">
        <!-- Champ Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Adresse(s) email(s) <span class="text-gray-500">(séparées par ";")</span>
          </label>
          <input 
            type="text" 
            v-model="emails" 
            class="input-field" 
            placeholder="ex: user1@example.com; user2@example.com"
          />
          <p v-if="errors.emailAddress" class="error-message">{{ errors.emailAddress }}</p>
        </div>

        <!-- ⚡️ Boutons d'action -->
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
          
          <!-- Bouton désactivé si en cours de soumission -->
          <button type="submit" class="btn-primary flex items-center" :disabled="loading.sendEmail">
            <span v-if="loading.sendEmail" class="animate-spin mr-2">⏳</span>
            Envoyer
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import { useInvoicesStore } from '@/stores/invoices';
import { validateEmail } from '@/utils';
import { storeToRefs } from 'pinia';

const props = defineProps({
  show: Boolean,
  invoice: Object, // L'objet facture contenant l'email du tuteur
});

const emit = defineEmits(["close", "sent"]);

const invoicesStore = useInvoicesStore();
const { sendEmail } = invoicesStore;
const { loading } = storeToRefs(invoicesStore);

const toast = useToast();

const emails = ref(props.invoice.reservation.renter?.tutor.email);
const errors = ref({});


// 📌 Validation et envoi des emails
const submitForm = async () => {
  errors.value = {};

  // Découper les emails en supprimant les espaces autour
  let emailsArray = emails.value.split(";").map(email => email.trim());

  // Vérifier si chaque email est valide
  const invalidEmails = emailsArray.filter(email => !validateEmail(email));

  if (invalidEmails.length > 0) {
    errors.value.emailAddress = `Adresse(s) email invalide(s) : ${invalidEmails.join(", ")}`;
    return;
  }

  try {
    await sendEmail(props.invoice.id, emailsArray.join(";")); // Envoi sous format "email1@example.com;email2@example.com"
    toast.success("Facture envoyée avec succès !");
    emit("sent"); // 🔄 Émettre un événement après l'envoi
    closeModal();
  } catch (err) {
    toast.error("Erreur lors de l'envoi de l'email.");
  }
};

// 📌 Fermeture de la modale
const closeModal = () => {
  errors.value = {};
  emit('close');
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

/* Styles des champs de saisie */
.input-field {
  @apply block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2;
}

/* Messages d'erreur */
.error-message {
  @apply mt-2 text-sm text-red-600;
}

/* Bouton principal */
.btn-primary {
  @apply px-4 py-2 bg-indigo-500 text-white rounded-md font-semibold hover:bg-indigo-400 transition disabled:opacity-50 disabled:cursor-not-allowed;
}

/* Bouton secondaire */
.btn-secondary {
  @apply px-4 py-2 bg-gray-500 text-white rounded-md font-semibold hover:bg-gray-400 transition;
}
</style>
