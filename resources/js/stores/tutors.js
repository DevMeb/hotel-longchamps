import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useTutorsStore = defineStore('tutors', () => {
  const tutors = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchTutors() {
    loading.value = true;
    error.value = null;
    tutors.value = [];

    try {
      const response = await axios.get('/api/tutors');
      tutors.value = response.data.data;
    } catch (err) {
      error.value = err.response?.data?.message || `Erreur ${err.response?.status}: ${err.response?.statusText}`;
    } finally {
      loading.value = false;
    }
  }

  async function addTutor(tutor) {
    try {
      const response = await axios.post('/api/tutors', tutor);
      tutors.value.push(response.data.data); // ✅ Ajout local
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function updateTutor(tutor) {
    try {
      const response = await axios.put(`/api/tutors/${tutor.id}`, tutor);
      const index = tutors.value.findIndex(t => t.id === tutor.id);
      if (index !== -1) {
        tutors.value[index] = response.data.data; // ✅ Mise à jour locale
      }
      return response;
    } catch (err) {
      throw err;
    }
  }

  async function deleteTutor(tutorId) {
    try {
      await axios.delete(`/api/tutors/${tutorId}`);
      tutors.value = tutors.value.filter(t => t.id !== tutorId); // ✅ Suppression locale
    } catch (err) {
      throw err;
    }
  }

  return { tutors, error, loading, fetchTutors, addTutor, updateTutor, deleteTutor };
});
