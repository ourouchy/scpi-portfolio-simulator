import { defineStore } from 'pinia';
import { register, login, logout, me } from '../api';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    isAuthenticated: null,
    error: null,
  }),
  actions: {
    async register(email, password) {
      try {
        const res = await register(email, password);
        // Ne pas connecter automatiquement après inscription
        this.error = null;
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.error || 'Erreur lors de l\'inscription';
        throw err;
      }
    },
    async login(email, password) {
      try {
        const res = await login(email, password);
        this.isAuthenticated = true;
        this.user = res.data.user;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.error || 'Erreur de connexion';
        throw err;
      }
    },
    async logout() {
      try {
        await logout();
        this.isAuthenticated = false;
        this.user = null;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.error || 'Erreur de déconnexion';
        throw err;
      }
    },
    async checkAuth() {
      try {
        const res = await me();
        this.isAuthenticated = true;
        this.user = res.data.user;
        this.error = null;
        return true;
      } catch (err) {
        this.isAuthenticated = false;
        this.user = null;
        this.error = null;
        return false;
      }
    },
  },
});
