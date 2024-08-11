// src/stores/auth.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null);
  const user = ref(null);
  const error = ref(null);
  const router = useRouter();
  const toast = useToast();

  const isAuthenticated = computed(() => !!token.value);

  const login = async (name, password) => {
    try {
      await axios.get('/sanctum/csrf-cookie');
      const response = await axios.post('/login', { name, password });
      token.value = response.data.data.token;
      localStorage.setItem('token', token.value);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      toast.success('Connexion réussie !'); // Notification de connexion réussie
      router.push('/');
    } catch (err) {
      error.value = 'Nom d’utilisateur ou mot de passe incorrect.';
      toast.error('Échec de la connexion.'); // Notification d'échec de connexion
    }
  };

  const logout = () => {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    toast.info('Déconnexion réussie.'); // Notification de déconnexion réussie
    router.push('/login');
  };

  return {
    token,
    user,
    error,
    isAuthenticated,
    login,
    logout,
  };
});
