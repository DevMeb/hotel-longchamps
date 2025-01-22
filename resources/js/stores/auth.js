import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useStorage } from "@vueuse/core";
import axios from "axios";
import { useToast } from "vue-toastification";

export const useAuthStore = defineStore("auth", () => {
  const token = useStorage("token", null);
  const user = ref(null);
  const error = ref(null);
  const loading = ref(false);
  const isCheckingAuth = ref(false);
  const router = useRouter();
  const toast = useToast();

  const isAuthenticated = computed(() => !!token.value);

  /** ✅ Vérifie si l'utilisateur est connecté */
  async function checkAuth() {
    if (!token.value) return false;

    try {
      const response = await axios.get("api/validate-token", {
        headers: { Authorization: `Bearer ${token.value}` },
      });
      user.value = response.data.user;
      return response.data.valid;
    } catch (err) {
      logout(); // Supprime le token si la validation échoue
      return false;
    }
  }

  /** ✅ Connexion utilisateur */
  async function login(name, password) {
    loading.value = true;
    error.value = null;

    try {
      await axios.get("/sanctum/csrf-cookie");
      const response = await axios.post("api/login", { name, password });

      token.value = response.data.data.token;
      axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;

      toast.success("Connexion réussie !");
      router.push("/");
    } catch (err) {
      error.value = "Nom d’utilisateur ou mot de passe incorrect.";
      toast.error("Échec de la connexion.");
    } finally {
      loading.value = false;
    }
  }

  /** ✅ Déconnexion utilisateur */
  function logout() {
    token.value = null;
    user.value = null;
    delete axios.defaults.headers.common["Authorization"];

    toast.info("Déconnexion réussie.");
    router.push("/login");
  }

  return {
    token,
    user,
    error,
    loading,
    isCheckingAuth,
    isAuthenticated,
    checkAuth,
    login,
    logout,
  };
});
