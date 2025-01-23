export function formatDateToISO(dateString) {
  if (!dateString) return "";

  const [day, month, year] = dateString.split('/');
  
  // Vérification que la date est valide
  if (!day || !month || !year) return "";

  return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
}

export function formatDateToFR(dateString) {
  if (!dateString) return "";

  const [year, month, day] = dateString.split('-');

  // Vérification que la date est valide
  if (!year || !month || !day) return "";

  return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
}

// 📌 Validation des emails
export function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

import { useToast } from 'vue-toastification';

const toast = useToast();

export function notify(type, message) {
  if (type === 'success') {
    toast.success(message);
  } else if (type === 'error') {
    toast.error(message);
  } else if (type === 'info') {
    toast.info(message);
  } else if (type === 'warning') {
    toast.warning(message);
  } 
}

export function formatAmount(amount) {
  return (amount / 100).toFixed(2) + " €";
};