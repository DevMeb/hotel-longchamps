// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';  // Page d'accueil gérée par Vue
import Login from '@/views/Login.vue';  // Page de connexion
import Tutors from '@/views/Tutors.vue';  // Importez le composant Tutors
import Renters from '@/views/Renters.vue';  // Importez le composant Renters
import Rooms from '@/views/Rooms.vue';  // Importez le composant Rooms

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
  {
    path: '/tutors',
    name: 'Tutors',
    component: Tutors,
    meta: { requiresAuth: true },  // Protéger l'accès par authentification
  },
  {
    path: '/renters',
    name: 'Renters',
    component: Renters,
    meta: { requiresAuth: true },  // Protéger l'accès par authentification
  },
  {
    path: '/rooms',
    name: 'Rooms',
    component: Rooms,
    meta: { requiresAuth: true },  // Protéger l'accès par authentification
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token');
  let isAuthenticated = false;

  if (token) {
    try {
      // Envoyer une requête pour vérifier la validité du token
      const response = await axios.get('/validate-token', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      isAuthenticated = response.data.valid; // Si l'API retourne 'valid: true'
    } catch (error) {
      console.error('Token invalide ou expiré', error);
      localStorage.removeItem('token'); // Supprimer le token invalide
    }
  }

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
