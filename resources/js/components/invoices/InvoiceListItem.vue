<template>
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col">
        <h2 class="text-lg font-semibold text-white">💰 Facture #{{ invoice.id }}</h2>
        <p class="text-gray-400 text-sm mt-1">👤 Locataire : <span class="text-indigo-400">{{ invoice.reservation.renter.first_name }} {{ invoice.reservation.renter.last_name }}</span></p>
        <p class="text-gray-400 text-sm">🏨 Chambre : {{ invoice.reservation.room.name }}</p>
        <p class="text-gray-400 text-sm">📅 Émise le : {{ invoice.issued_at || "Non envoyé" }}</p>
        <p class="text-gray-400 text-sm">📌 Statut : <span :class="getStatusColor(invoice.status)">{{ invoice.status_text }}</span></p>

        <!-- Actions -->
        <div class="mt-4 flex justify-between space-x-2">
            <button @click="showUpdateModal = true" class="px-3 py-1 bg-blue-500 text-white text-sm rounded-md">✏️ Modifier</button>
            <button @click="showDeleteModal = true" class="px-3 py-1 bg-red-500 text-white text-sm rounded-md">🗑️ Supprimer</button>
            <button @click="showSendModal = true" class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md">📩 Envoyer</button>
        </div>
    </div>

    <InvoiceDeleteModal 
        v-if="showDeleteModal"
        :invoice="invoice"
        @close="showDeleteModal = false"
    />

    <InvoiceFormModal 
        v-if="showUpdateModal"
        :invoice="invoice"
        @close="showUpdateModal = false"
    />

    <InvoiceSendMailModal 
        v-if="showSendModal"
        :invoice="invoice"
        @close="showSendModal = false"
    />
</template>


<script setup>
import { ref } from 'vue'

import { 
    InvoiceDeleteModal, 
    InvoiceFormModal,
    InvoiceSendMailModal,
} from '@/components/invoices/'

const props = defineProps({
    invoice: Object,
})

const showDeleteModal = ref(false)
const showUpdateModal = ref(false)
const showSendModal = ref(false)

function getStatusColor(status) {
    return {
        pending: "text-yellow-500",
        issued: "text-blue-500",
        paid: "text-green-500",
    }[status] || "text-gray-400"
};
</script>
