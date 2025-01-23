<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img class="mx-auto h-40 w-auto" :src="logoUrl" alt="Hotel Longchamps" />
      <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-white">
        Connectez-vous à votre compte
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="handleLogin" class="space-y-6">
        <!-- Nom d'utilisateur -->
        <div>
          <label for="user" class="block text-sm font-medium leading-6 text-white">Nom d'utilisateur</label>
          <div class="mt-2">
            <input
              id="user"
              name="user"
              type="text"
              v-model="name"
              autocomplete="username"
              required
              class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
            />
          </div>
        </div>

        <!-- Mot de passe -->
        <div>
          <label for="password" class="block text-sm font-medium leading-6 text-white">Mot de passe</label>
          <div class="mt-2">
            <input
              id="password"
              name="password"
              type="password"
              v-model="password"
              autocomplete="current-password"
              required
              class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
            />
          </div>
        </div>

        <!-- Bouton de connexion -->
        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="authStore.loading"
          >
            <span v-if="authStore.loading" class="animate-spin mr-2">⏳</span>
            Se connecter
          </button>
        </div>

        <!-- Message d'erreur -->
        <p v-if="authStore.error" class="text-red-500 text-sm">{{ authStore.error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import logoUrl from "@/../images/hotel-logo.png";

const name = ref("");
const password = ref("");
const authStore = useAuthStore();

const handleLogin = async () => {
  authStore.error = null; // Réinitialise les erreurs avant la tentative de connexion
  try {
    await authStore.login(name.value, password.value);
  } catch (error) {
    console.error("Erreur de connexion :", error);
  }
};
</script>
