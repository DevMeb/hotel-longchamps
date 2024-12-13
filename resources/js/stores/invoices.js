// src/stores/invoices.js
import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useInvoicesStore = defineStore('invoices', () => {
  const invoices = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchInvoices() {
    loading.value = true;
    try {
      const response = await axios.get('api/invoices');
      invoices.value = response.data.data;

      // Vérifier si le contenu est du JSON
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue lors de la récupération des factures.");
      }
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  async function addInvoice(invoice) {
    try {
      const response = await axios.post('api/invoices', invoice);
      invoices.value.push(response.data.data); // Ajouter la nouvelle facture localement
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function updateInvoice(invoice) {
    try {
      const response = await axios.put(`api/invoices/${invoice.id}`, invoice);
      const index = invoices.value.findIndex(i => i.id === invoice.id);
      if (index !== -1) {
        invoices.value[index] = response.data.data; // Mettre à jour la facture localement
      }
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function deleteInvoice(invoiceId) {
    try {
      const response = await axios.delete(`api/invoices/${invoiceId}`);
      invoices.value = invoices.value.filter(i => i.id !== invoiceId); // Supprimer localement
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function getInvoicePdf(invoiceId) {
    try {
      const response = await axios.get(`api/invoices/${invoiceId}/pdf`, {
        responseType: 'blob', // Important : indique qu'on attend un fichier binaire
      });
  
      // Créer une URL à partir du Blob pour l'affichage
      const fileURL = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
      return fileURL;
    } catch (err) {
      throw err;
    }
  }

  return { invoices, error, loading, fetchInvoices, addInvoice, updateInvoice, deleteInvoice, getInvoicePdf };
});
