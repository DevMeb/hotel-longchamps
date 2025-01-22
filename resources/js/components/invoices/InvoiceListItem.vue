<template>
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col space-y-4">
        <!-- En-tête -->
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                💰 Facture #{{ invoice.id }}
            </h2>
            <span :class="getStatusBadge(invoice.status)">
                {{ invoice.status_text }}
            </span>
        </div>

        <!-- 🏠 Informations générales -->
        <div class="bg-gray-900 p-4 rounded-md space-y-2">
            <p class="text-gray-300 text-sm">
                👤 <span class="font-semibold text-white">Locataire :</span> 
                <span class="text-indigo-400 font-bold">{{ invoice.reservation.renter.first_name }} {{ invoice.reservation.renter.last_name }}</span>
            </p>
            <p class="text-gray-300 text-sm">
                🏨 <span class="font-semibold text-white">Chambre :</span> {{ invoice.reservation.room.name }}
            </p>           
        </div>

        <!-- 📅 Détails de la facturation -->
        <div class="bg-gray-900 p-4 rounded-md space-y-2">
            <p class="text-gray-300 text-sm">
                📌 <span class="font-semibold text-white">Sujet :</span> {{ invoice.subject || "Non renseigné" }}
            </p>

            <p class="text-gray-300 text-sm">
                💰 <span class="font-semibold text-white">Montant :</span> {{ invoice.reservation.room.rent }} €
            </p>            
        </div>

        <!-- ⏳ Statuts et dates clés -->
        <div class="bg-gray-900 p-4 rounded-md grid grid-cols-2 gap-4">
            <p class="text-gray-300 text-sm">
                🕒 <span class="font-semibold text-white">Créée le :</span> {{ invoice.created_at }}
            </p>
            <p class="text-gray-300 text-sm">
                🔄 <span class="font-semibold text-white">Mise à jour le :</span> {{ invoice.updated_at }}
            </p>
            <p class="text-gray-300 text-sm">
                💰 <span class="font-semibold text-white">Payée le :</span> {{ invoice.paid_at || "Non payée" }}
            </p>
            <p class="text-gray-300 text-sm">
                📩 <span class="font-semibold text-white">Émise le :</span> {{ invoice.issued_at || "Non envoyé" }}
            </p>
        </div>

        <!-- 🔘 Actions -->
        <div class="flex flex-wrap justify-center gap-3 mt-4">
            <button @click="showPdfModal = true" class="btn-action bg-gray-600">
                📄 Voir
            </button>

            <button v-if="invoice.status === 'pending'" @click="showFormModal = true" class="btn-action bg-blue-500">
                ✏️ Modifier
            </button>

            <button v-if="invoice.status === 'pending'" @click="showDeleteModal = true" class="btn-action bg-red-500">
                🗑️ Supprimer
            </button>

            <button v-if="invoice.status === 'pending'" @click="showSendModal = true" class="btn-action bg-yellow-500">
                📩 Envoyer
            </button>

            <button v-if="invoice.status === 'issued'" @click="markAsPaid(invoice)" :disabled="loading.paid"
                class="btn-action bg-green-500 disabled:opacity-50">
                <span v-if="loading.paid" class="animate-spin border-4 border-white border-t-transparent rounded-full w-4 h-4"></span>
                ✅ Payé
            </button>
        </div>
    </div>

    <!-- Modals -->
    <InvoicePdfModal v-if="showPdfModal" :invoice="invoice" @close="showPdfModal = false" />
    <InvoiceFormModal v-if="showFormModal" :invoice="invoice" @close="showFormModal = false" />
    <InvoiceDeleteModal v-if="showDeleteModal" :invoice="invoice" @close="showDeleteModal = false" />
    <InvoiceSendMailModal v-if="showSendModal" :invoice="invoice" @close="showSendModal = false" />
</template>

<script setup>
import { ref } from 'vue'
import { useInvoicesStore } from '@/stores/invoices'
import { useToast } from 'vue-toastification'
import { storeToRefs } from 'pinia'
import { 
    InvoicePdfModal,
    InvoiceFormModal,
    InvoiceDeleteModal, 
    InvoiceSendMailModal,
} from '@/components/invoices/'

const props = defineProps({
    invoice: Object,
})

const invoicesStore = useInvoicesStore()
const { invoicePaid } = invoicesStore
const { loading } = storeToRefs(invoicesStore)

const showDeleteModal = ref(false)
const showFormModal = ref(false)
const showSendModal = ref(false)
const showPdfModal = ref(false)

// 📌 Styles des badges de statut
function getStatusBadge(status) {
    return {
        pending: "bg-yellow-500 text-white px-3 py-1 rounded-full text-xs",
        issued: "bg-blue-500 text-white px-3 py-1 rounded-full text-xs",
        paid: "bg-green-500 text-white px-3 py-1 rounded-full text-xs",
    }[status] || "bg-gray-500 text-white px-3 py-1 rounded-full text-xs"
}

// 📌 Marquer la facture comme payée avec chargement
async function markAsPaid(invoice) {
    await invoicePaid(invoice)
}
</script>

<style scoped>
/* 🎨 Boutons d'action stylisés */
.btn-action {
    @apply px-4 py-2 text-white text-sm rounded-md flex items-center gap-1 hover:opacity-80 transition;
}
</style>
