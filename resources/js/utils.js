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
