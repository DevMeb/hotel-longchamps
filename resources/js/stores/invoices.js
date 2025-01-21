// src/stores/invoices.js
import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { useStorage } from '@vueuse/core';

export const useInvoicesStore = defineStore('invoices', () => {
  const invoices = ref([]);
  const error = ref(null);
  const loading = ref({
    fetch: false,
    update: false,
    add: false,
    sendEmail: false,
    delete: false,
    pdf: false,
    paid: false,
  });

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage('invoice-filters', {
    renter: "",
    room: "",
    status: "",
    month_year: "",
  });

  async function fetchInvoices(id = null) {
    loading.value.fetch = true;
    const params = id ? { id: id } : {};
    try {
      const response = await axios.get('api/invoices', { params });
      invoices.value = response.data.data;

      // Vérifier si le contenu est du JSON
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue lors de la récupération des factures.");
      }
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value.fetch = false;
    }
  }

  async function addInvoice(invoice) {
    try {
      loading.value.add = true;
      const response = await axios.post('api/invoices', invoice);
      invoices.value.push(response.data.data); // Ajouter la nouvelle facture localement
      return response;
    } catch (err) {
      throw err;
    } finally {
      loading.value.add = false;
    }
  }

  async function updateInvoice(invoice) {
    try {
      loading.value.update = true;
      const response = await axios.put(`api/invoices/${invoice.id}`, invoice);
      const index = invoices.value.findIndex(i => i.id === invoice.id);
      if (index !== -1) {
        invoices.value[index] = response.data.data; // Mettre à jour la facture localement
      }
      return response;
    } catch (err) {
      throw err;
    } finally {
      loading.value.update = false
    }
  }

  async function deleteInvoice(invoiceId) {
    try {
      loading.value.delete = true;
      const response = await axios.delete(`api/invoices/${invoiceId}`);
      invoices.value = invoices.value.filter(i => i.id !== invoiceId); // Supprimer localement
      return response;
    } catch (err) {
      throw err;
    } finally {
      loading.value.delete = false;
    }
  }

  async function invoicePaid(invoice) {
    try {
      loading.value.paid = true;
      const response = await axios.patch(`api/invoices/${invoice.id}/paid`);
      const index = invoices.value.findIndex(i => i.id === invoice.id);
      if (index !== -1) {
        invoices.value[index] = response.data.data; // Mettre à jour la facture localement
      }
      return response.data.data;
    } catch (err) {
      throw err;
    } finally {
      loading.value.paid = false;
    }
  }

  async function getInvoicePdf(invoiceId) {
    try {
      loading.value.pdf = true;
      const response = await axios.get(`api/invoices/${invoiceId}/pdf`, {
        responseType: 'blob', // Important : indique qu'on attend un fichier binaire
      });
  
      return URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    } catch (err) {
      throw err;
    } finally {
      loading.value.pdf = false;
    }
  }

  async function sendEmail(invoiceId, emails) {
    try {
      loading.value.sendEmail = true
      const response = await axios.post(`/api/invoices/${invoiceId}/send-email`, {
        emails: emails,
      });

      const index = invoices.value.findIndex(i => i.id === invoiceId);
      if (index !== -1) {
        invoices.value[index] = response.data.data; // Mettre à jour la facture localement
      }
    } catch (err) {
      throw err
    } finally {
      loading.value.sendEmail = false;
    }
  }

  function updateFilters(filters) {
    activeFilters.value = filters;
  }

  // 📌 Détection des filtres actifs
  const isAnyFilterActive = computed(() => {
    return Object.values(activeFilters.value).some(value => value !== "");
  });

  // 📌 Optimisation du filtrage avec watch
  const filteredInvoices = ref([]);

  watch([invoices, activeFilters], () => {
    filteredInvoices.value = invoices.value.filter(invoice => {
      const { renter, room, status, month_year } = activeFilters.value;

      if (renter && invoice.reservation.renter.id !== parseInt(renter)) return false;
      if (room && invoice.reservation.room.id !== parseInt(room)) return false;
      if (status && invoice.status !== status) return false;

      if (month_year) {
        const selectedDate = new Date(month_year);
        const invoiceStartDate = new Date(invoice.billing_start_date);
        const invoiceEndDate = invoice.billing_end_date ? new Date(invoice.billing_end_date) : null;

        if (
          invoiceStartDate.getMonth() !== selectedDate.getMonth() ||
          invoiceStartDate.getFullYear() !== selectedDate.getFullYear()
        ) {
          if (!invoiceEndDate || invoiceEndDate.getMonth() !== selectedDate.getMonth() || invoiceEndDate.getFullYear() !== selectedDate.getFullYear()) {
            return false;
          }
        }
      }

      return true;
    });
  }, { deep: true, immediate: true });

  return { 
    invoices, 
    error, 
    loading, 
    fetchInvoices,
    addInvoice, 
    updateInvoice, 
    deleteInvoice, 
    getInvoicePdf, 
    sendEmail, 
    invoicePaid,
    activeFilters,
    updateFilters,
    filteredInvoices,
    isAnyFilterActive,
  };
});
