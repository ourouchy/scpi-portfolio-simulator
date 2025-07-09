<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow mb-8">
      <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <router-link to="/portfolio" class="font-bold text-xl text-blue-700">SCPI Portfolio Simulator</router-link>
        <div>
          <template v-if="isAuthenticated">
            <span class="mr-4">{{ user?.email }}</span>
            <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded">Déconnexion</button>
          </template>
          <template v-else>
            <router-link to="/login" class="mr-4 text-blue-600">Connexion</router-link>
            <router-link to="/register" class="text-blue-600">Inscription</router-link>
          </template>
        </div>
      </div>
    </nav>
    <router-view />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from './store/userStore';
const userStore = useUserStore();
const router = useRouter();
const isAuthenticated = computed(() => userStore.isAuthenticated);
const user = computed(() => userStore.user);
const logout = async () => {
  await userStore.logout();
  router.push('/login');
};
</script>
