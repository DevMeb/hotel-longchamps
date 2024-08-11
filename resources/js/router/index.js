// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';  // Page d'accueil gérée par Vue
import Login from '@/views/Login.vue';  // Page de connexion

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: true },  // La route Home nécessite l'authentification
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guestOnly: true },  // La route Login doit être accessible uniquement aux non-authentifiés
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token'); // Vérifiez si l'utilisateur est authentifié

  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    // Si la route nécessite l'authentification et que l'utilisateur n'est pas authentifié, rediriger vers /login
    next({ name: 'Login' });
  } else if (to.matched.some(record => record.meta.guestOnly) && isAuthenticated) {
    // Si la route est réservée aux invités (non-authentifiés) et que l'utilisateur est authentifié, rediriger vers /
    next({ name: 'Home' });
  } else {
    // Sinon, continuer la navigation
    next();
  }
});

export default router;
