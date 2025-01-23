import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes"; // 📌 Import des routes séparées
import { useAuthStore } from "@/stores/auth"; // 📌 Gestion centralisée de l'auth

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 📌 Middleware de navigation
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = await authStore.checkAuth(); // ✅ Vérification via Pinia

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: "Login" });
  } else if (to.meta.guestOnly && isAuthenticated) {
    next({ name: "Home" });
  } else {
    next();
  }
});

export default router;
