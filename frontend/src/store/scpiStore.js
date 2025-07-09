import { defineStore } from 'pinia';
import api from '../axios';

export const useScpiStore = defineStore('scpi', {
  state: () => ({
    scpis: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchScpis() {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/scpis');
        this.scpis = res.data;
      } catch (err) {
        this.error = err.response?.data?.error || 'Erreur lors du chargement des SCPI';
      } finally {
        this.loading = false;
      }
    },
  },
});
