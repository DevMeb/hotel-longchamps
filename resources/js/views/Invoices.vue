<template>
    <navbar></navbar>
    <div class="bg-gray-900">
      <div class="mx-auto max-w-7xl">
        <div class="bg-gray-900 py-10">
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-white">Factures</h1>
                <p class="mt-2 text-sm text-gray-300">
                  Liste de toutes les factures, y compris le sujet, les dates, le statut, et les actions.
                </p>
              </div>
            </div>
  
            <div class="mt-8 flow-root">
              <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                      <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Id</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Chambre</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Locataire</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Sujet</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date de début de facturation</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date de fin de facturation</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date d'émission</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Date de paiement</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Statut</th>
                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-white">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                      <tr v-if="loading">
                        <td colspan="10" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center">Chargement...</td>
                      </tr>
                      <tr v-if="error && !loading">
                        <td colspan="10" class="whitespace-nowrap py-4 text-sm text-red-500 text-center">{{ error }}</td>
                      </tr>
                      <tr v-else-if="invoices.length === 0 && !loading">
                        <td colspan="10" class="whitespace-nowrap py-4 text-sm text-gray-300 text-center m-auto">Aucune facture trouvée.</td>
                      </tr>
                      <tr v-else v-for="invoice in invoices" :key="invoice.id">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">
                          {{ invoice.id }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">
                          {{ invoice.reservation.room.name }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">
                          {{ invoice.reservation.renter.first_name  }} {{ invoice.reservation.renter.last_name }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-white sm:pl-0">
                          {{ invoice.subject }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ invoice.billing_start_date }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ invoice.billing_end_date }}</td>

                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ invoice.issued_at || 'Non envoyé' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ invoice.paid_at || 'Non payée' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                          <span
                            :class="{
                              'text-yellow-500': invoice.status === 'pending',
                              'text-blue-500': invoice.status === 'issued',
                              'text-green-500': invoice.status === 'paid'
                            }"
                          >
                            {{ invoice.status_text }}
                          </span>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                          <button @click="displayInvoicePdf(invoice.id)" class="text-indigo-400 hover:text-indigo-300">Voir</button>
                          <button @click="editInvoice(invoice)" class="ml-4 text-blue-400 hover:text-blue-300">Éditer</button>
                          <button @click="confirmDeleteInvoice(invoice)" class="ml-4 text-red-400 hover:text-red-300">Supprimer</button>
                          <button @click="sendInvoicePdf(invoice)" class="ml-4 text-yellow-400 hover:text-v-300">Envoyer par mail</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
  
            <!-- Modal pour mettre à jour une facture -->
            <div v-if="showUpdateInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
                <h2 class="text-lg font-semibold mb-4">Éditer la facture</h2>
                <form @submit.prevent="modifyInvoice">
                  <div>
                    <label for="subject" class="block text-sm font-medium leading-6 text-gray-900">Sujet</label>
                    <input type="text" v-model="updateInvoiceForm.subject" id="subject" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.subject" class="mt-2 text-sm text-red-600">{{ errors.subject.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Sujet</label>
                    <textarea 
                      v-model="updateInvoiceForm.description" 
                      id="description" 
                      rows="4" 
                      class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                    </textarea>
                    <p v-if="errors.description" class="mt-2 text-sm text-red-600">{{ errors.description.join(' ') }}</p>
                  </div>
                  
                  <div>
                    <label for="billing_start_date" class="block text-sm font-medium leading-6 text-gray-900">Date de début</label>
                    <input type="date" v-model="updateInvoiceForm.billing_start_date" id="billing_start_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.billing_start_date" class="mt-2 text-sm text-red-600">{{ errors.billing_start_date.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="billing_end_date" class="block text-sm font-medium leading-6 text-gray-900">Date de fin</label>
                    <input type="date" v-model="updateInvoiceForm.billing_end_date" id="billing_end_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" />
                    <p v-if="errors.billing_end_date" class="mt-2 text-sm text-red-600">{{ errors.billing_end_date.join(' ') }}</p>
                  </div>
                  <div>
                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Statut</label>
                    <select v-model="updateInvoiceForm.status" id="status" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                      <option value="pending">En attente</option>
                      <option value="issued">Émise</option>
                      <option value="paid">Payée</option>
                    </select>
                    <p v-if="errors.status" class="mt-2 text-sm text-red-600">{{ errors.status.join(' ') }}</p>
                  </div>
                  <div class="flex justify-end mt-4">
                    <button type="button" @click="showUpdateInvoiceModal = false;" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">Mettre à jour</button>
                  </div>
                </form>
              </div>
            </div>
  
            <!-- Modal de confirmation de suppression -->
            <div v-if="showDeleteInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Confirmation de suppression</h2>
                <p class="mb-4">Êtes-vous sûr de vouloir supprimer cette facture ?</p>
                <div class="flex justify-end">
                  <button type="button" @click="showDeleteInvoiceModal = false" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">Annuler</button>
                  <button @click="performDeleteInvoice" type="button" class="px-4 py-2 bg-red-500 text-white rounded-md">Supprimer</button>
                </div>
              </div>
            </div>

            <!-- Modal d'envoi d'email -->
            <div v-if="showSendInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4">Envoyer la facture par email</h2>
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Adresse(s) email</label>
                  <input
                    v-model="emailAddresses"
                    id="email"
                    type="text"
                    placeholder="Ajouter une ou plusieurs adresses email"
                    class="w-full mt-2 p-2 border border-gray-300 rounded-md"
                  />
                  <p v-if="errors.emailAddresses" class="mt-2 text-sm text-red-600">{{ errors.emailAddresses }}</p>
                  <p class="text-sm text-gray-500 mt-1">Séparez plusieurs adresses email par une virgule.</p>
                </div>
                <div class="flex justify-end mt-6">
                  <button type="button" @click="showSendInvoiceModal = false;" class="mr-4 px-4 py-2 bg-gray-500 text-white rounded-md">
                    Annuler
                  </button>
                  <button :disabled="loadingEmail" @click="confirmSendEmail" type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md flex items-center justify-center">
                    <svg v-if="loadingEmail" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Envoyer</span>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Navbar from '@/components/Navbar.vue';
  import { useInvoicesStore } from '@/stores/invoices';
  import { storeToRefs } from 'pinia';
  import { ref, onMounted } from 'vue';
  import { useToast } from 'vue-toastification';
  
  const invoicesStore = useInvoicesStore()
  const { invoices, error, loading, loadingEmail } = storeToRefs(invoicesStore);
  const { fetchInvoices, updateInvoice, deleteInvoice, getInvoicePdf, sendEmail } = invoicesStore;
  const toast = useToast();
  
  const showUpdateInvoiceModal = ref(false);
  const showDeleteInvoiceModal = ref(false);
  const showSendInvoiceModal = ref(false);

  // Emails à envoyer
  const emailAddresses = ref('');
  const invoiceToSendByMail = ref({})

  const updateInvoiceForm = ref({
    id: null,
    reservation_id: null,
    description: '',
    subject: '',
    billing_start_date: '',
    billing_end_date: '',
    status: '',
  });

  const invoiceToDelete = ref(null);
  
  const errors = ref({});
  
  onMounted(() => {
    fetchInvoices();
  });

  // Méthode pour afficher la modal
  function sendInvoicePdf(invoice) {
    invoiceToSendByMail.value = invoice
    // Simuler la récupération de l'email du tuteur
    const tutorEmail = invoice?.reservation?.renter?.tutor?.email;
    
    // Préremplir le champ email
    emailAddresses.value = tutorEmail || '';
    showSendInvoiceModal.value = true;
  }

  // Méthode pour valider les emails
  function validateEmails(emails) {
    const emailList = emails.split(',').map(email => email.trim());
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return emailList.every(email => emailRegex.test(email));
  }

  // Méthode pour confirmer l'envoi des emails
  async function confirmSendEmail() {
    if (!validateEmails(emailAddresses.value)) {
      errors.value.emailAddresses = 'Veuillez saisir des adresses email valides.'
      return
    }
    
    try {
      const response = await sendEmail(invoiceToSendByMail.value.id, emailAddresses.value)
      toast.success(response.data.message);
      showSendInvoiceModal.value = false;
    } catch (err) {
      toast.error("Une erreur est survenue lors de l'envoi de l'email");
    }
  }

  async function displayInvoicePdf(invoiceId) {
    try {
      const pdfUrl = await getInvoicePdf(invoiceId);
      
      window.open(pdfUrl, '_blank');
      
    } catch (err) {
      toast.error("Une erreur est survenue lors de l'affichage de la facture");
    }
  }
  
  async function modifyInvoice() {
    try {
      const responseUpdateInvoice = await updateInvoice(updateInvoiceForm.value)
      toast.success(responseUpdateInvoice.data.message);
      showUpdateInvoiceModal.value = false
    } catch(err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors.value = err.response.data.errors; // Stocker les erreurs pour chaque champ
        toast.error("Des erreurs de validation ont été détectées. Veuillez vérifier les champs.");
      } else {
        toast.error("Une erreur est survenue depuis le serveur lors de l'enregistrement de la réservation. Veuillez contacter votre administrateur.");
      }
    }
  }

  function formatDateForForm(dateString) {
    const [day, month, year] = dateString.split('/');
    return `${year}-${month}-${day}`;
  }
  
  function editInvoice(invoice) {
    updateInvoiceForm.value = {
      id: invoice.id,
      reservation_id: invoice.reservation.id,
      description: invoice.description,
      subject: invoice.subject,
      billing_start_date: formatDateForForm(invoice.billing_start_date),
      billing_end_date: formatDateForForm(invoice.billing_end_date),
      status: invoice.status,
    };
    showUpdateInvoiceModal.value = true;
  }
  
  function confirmDeleteInvoice(invoice) {
    invoiceToDelete.value = invoice;
    showDeleteInvoiceModal.value = true;
  }
  
  function performDeleteInvoice() {
    deleteInvoice(invoiceToDelete.value.id)
      .then(() => {
        toast.success('Facture supprimée.');
        fetchInvoices();
        showDeleteInvoiceModal.value = false;
      })
      .catch(() => {
        toast.error("Erreur lors de la suppression de la facture.");
      });
  }
  
</script>
  