<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Connexion</h2>
    <form @submit.prevent="onSubmit">
      <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input v-model="email" type="email" class="w-full border rounded px-3 py-2" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1">Mot de passe</label>
        <input v-model="password" type="password" class="w-full border rounded px-3 py-2" required />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Se connecter</button>
      <p v-if="error" class="text-red-600 mt-2">{{ error }}</p>
    </form>
    <router-link to="/register" class="block mt-4 text-blue-600">Créer un compte</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '../store/userStore';

const email = ref('');
const password = ref('');
const error = ref(null);
const router = useRouter();
const userStore = useUserStore();

const onSubmit = async () => {
  error.value = null;
  try {
    await userStore.login(email.value, password.value);
    router.push('/portfolio');
  } catch (e) {
    error.value = userStore.error;
  }
};
</script>
