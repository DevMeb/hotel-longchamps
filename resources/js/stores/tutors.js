import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useTutorsStore = defineStore('tutors', () => {
  const tutors = ref([]);
  const error = ref(null);
  const loading = ref(false);

  async function fetchTutors() {
    loading.value = true;
    try {
      const response = await axios.get('api/tutors');
      tutors.value = response.data.data;

      //For GET method Laravel return code 200 with HTML instead 405.
      if (response.headers['content-type'] !== 'application/json') {
        throw new Error("Une erreur est survenue depuis le serveur lors de la récupération des tuteurs. Veuillez contactez votre administrateur.");
      }

    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false;
    }
  }

  async function addTutor(tutor) {
    try {
      return await axios.post('api/tutors', tutor);
    } catch (err) {
      throw err;
    }
  }

  async function updateTutor(tutor) {
    try {
      return await axios.put(`api/tutors/${tutor.id}`, tutor);
    } catch (err) {
      throw err;
    }
  }

  async function deleteTutor(tutorId) {
    try {
      return await axios.delete(`api/tutors/${tutorId}`);
    } catch (err) {
      throw err;
    }
  }

  return { tutors, error, loading, fetchTutors, addTutor, updateTutor, deleteTutor };
});
