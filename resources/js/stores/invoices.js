// src/stores/invoices.js
import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { notify } from '@/utils';
import { useStorage } from '@vueuse/core';

export const useInvoicesStore = defineStore('invoices', () => {
  const invoices = ref([]);
  const errors = ref({}); 
  const loading = ref({});

  // 📌 Persistance des filtres avec localStorage
  const activeFilters = useStorage('invoice-filters', {
    renter: "",
    room: "",
    status: "",
    month_year: "",
  });

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

  function clearErrors(operation) {
    if (operation) {
      errors.value[operation] = null;
    } else {
      errors.value = {};
    }
  }

  function setLoading(operation, state) {
    loading.value[operation] = state;
  }

  async function apiCall({ operation, request, onSuccess }) {
      clearErrors(operation);
      setLoading(operation, true);
  
      try {
        const response = await request();
        if (onSuccess) onSuccess(response);
        return response;
      } catch (err) {
        if (err.response?.status === 422) {
          errors.value.validationErrors = err.response.data.errors; // Erreurs de validation
        } else {
          errors.value[operation] = err.response?.data?.message || "Une erreur est survenue.";
          notify('error', errors.value[operation]);
        }
      } finally {
        setLoading(operation, false);
      }
  }

  async function fetchInvoices() {
    return apiCall({
      operation: 'fetch',
      request: () => axios.get('/api/invoices'),
      onSuccess: (response) => {
        invoices.value = response.data.data;
      },
    });
  }
  
  async function addInvoice(invoice) {
    return apiCall({
      operation: 'add',
      request: () => axios.post('/api/invoices', invoice),
      onSuccess: (response) => {
        invoices.value.push(response.data.data);
        notify('success', response.data.message);
      },
    });
  }
  
  async function updateInvoice(invoice) {
    return apiCall({
      operation: 'update',
      request: () => axios.put(`/api/invoices/${invoice.id}`, invoice),
      onSuccess: (response) => {
        const index = invoices.value.findIndex(i => i.id === invoice.id);
        if (index !== -1) {
          invoices.value[index] = response.data.data;
        }
        notify('success', response.data.message);
      },
    });
  }
  
  async function deleteInvoice(invoiceId) {
    return apiCall({
      operation: 'delete',
      request: () => axios.delete(`/api/invoices/${invoiceId}`),
      onSuccess: (response) => {
        invoices.value = invoices.value.filter(i => i.id !== invoiceId);
        notify('success', response.data.message);
      },
    });
  }

  async function invoicePaid(invoice) {
    return apiCall({
      operation: 'paid',
      request: () => axios.patch(`/api/invoices/${invoice.id}/paid`),
      onSuccess: (response) => {
        const index = invoices.value.findIndex(i => i.id === invoice.id);
        if (index !== -1) {
          invoices.value[index] = response.data.data;
        }
        notify('success', 'Facture marquée comme payée.');
      },
    });
  }
  
  const pdfCache = new Map(); // Cache pour stocker les URLs Blob générées

  async function getInvoicePdf(invoiceId) {
    if (pdfCache.has(invoiceId)) {
      return pdfCache.get(invoiceId); // 🔥 Retourne directement le PDF depuis le cache
    }

    try {
      const response = await apiCall({
        operation: 'pdf',
        request: () => axios.get(`/api/invoices/${invoiceId}/pdf`, { responseType: 'blob' }),
      });

      if (!response || !response.data) {
        throw new Error("Réponse invalide lors de la récupération du PDF.");
      }

      // 📌 Génération de l'URL Blob
      const blobUrl = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
      pdfCache.set(invoiceId, blobUrl); // 🏷️ Ajout au cache pour réutilisation

      return blobUrl;
    } catch (err) {
      console.error(`Erreur lors de la récupération du PDF de la facture ${invoiceId} :`, err);
      throw new Error("Impossible de récupérer le PDF.");
    }
  }

  
  async function sendEmail(invoiceId, emails) {
    return apiCall({
      operation: 'sendEmail',
      request: () => axios.post(`/api/invoices/${invoiceId}/send-email`, { emails }),
      onSuccess: (response) => {
        const index = invoices.value.findIndex(i => i.id === invoiceId);
        if (index !== -1) {
          invoices.value[index] = response.data.data;
        }
        notify('success', 'Facture envoyée avec succès.');
      },
    });
  }

  return { 
    invoices, 
    errors, 
    loading, 
    fetchInvoices,
    addInvoice, 
    updateInvoice, 
    deleteInvoice, 
    getInvoicePdf, 
    sendEmail, 
    invoicePaid,
    clearErrors,
    activeFilters,
    updateFilters,
    filteredInvoices,
    isAnyFilterActive,
  };
});
