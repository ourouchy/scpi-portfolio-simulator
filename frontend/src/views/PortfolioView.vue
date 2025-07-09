<template>
  <div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Simulateur de portefeuille SCPI</h2>
    <div v-if="!isAuthenticated" class="text-red-600 mb-4">
      Vous devez être connecté pour accéder à cette page.
      <router-link to="/login" class="text-blue-600 underline ml-2">Se connecter</router-link>
    </div>
    <div v-else>
      <form @submit.prevent="onSimulate">
        <div v-if="loadingScpis" class="mb-4">Chargement des SCPI...</div>
        <div v-else>
          <div v-for="scpi in scpis" :key="scpi.id" class="mb-4 flex items-center gap-2">
            <span class="w-40">{{ scpi.nom }} ({{ scpi.tauxRendementAnnuel }}%)</span>
            <input type="number" min="0" step="100" v-model.number="portefeuille[scpi.id]" placeholder="Montant €" class="border rounded px-2 py-1 w-32" />
          </div>
        </div>
        <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" :disabled="loadingSimu">Simuler</button>
      </form>
      <div v-if="error" class="text-red-600 mt-4">{{ error }}</div>
      <div v-if="result" class="mt-6 bg-gray-50 p-4 rounded">
        <h3 class="font-bold mb-2">Résultats</h3>
        <p><b>Montant total :</b> {{ result.montantTotal }} €</p>
        <p><b>Rendement moyen :</b> {{ result.rendementMoyen }} %</p>
        <p><b>Revenu annuel :</b> {{ result.revenuAnnuel }} €</p>
        <p><b>Revenu mensuel :</b> {{ result.revenuMensuel }} €</p>
        <div class="mt-2">
          <b>Détail :</b>
          <ul class="list-disc ml-6">
            <li v-for="d in result.details" :key="d.scpiId">
              {{ scpiName(d.scpiId) }} : {{ d.montant }} € à {{ d.rendement }}% → {{ d.revenuAnnuel }} €/an
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '../store/userStore';
import { useScpiStore } from '../store/scpiStore';
import api from '../axios';

const userStore = useUserStore();
const scpiStore = useScpiStore();
const router = useRouter();

const isAuthenticated = computed(() => userStore.isAuthenticated);
const scpis = computed(() => scpiStore.scpis);
const loadingScpis = computed(() => scpiStore.loading);
const portefeuille = ref({});
const result = ref(null);
const error = ref(null);
const loadingSimu = ref(false);

onMounted(async () => {
  if (scpiStore.scpis.length === 0) {
    await scpiStore.fetchScpis();
  }
});

const scpiName = (id) => {
  const s = scpis.value.find(s => s.id === id);
  return s ? s.nom : id;
};

const onSimulate = async () => {
  error.value = null;
  result.value = null;
  loadingSimu.value = true;
  try {
    const portefeuilleArray = Object.entries(portefeuille.value)
      .filter(([_, montant]) => montant > 0)
      .map(([scpiId, montant]) => ({ scpiId: Number(scpiId), montant: Number(montant) }));
    if (portefeuilleArray.length === 0) {
      error.value = 'Veuillez saisir au moins un montant.';
      loadingSimu.value = false;
      return;
    }
    const res = await api.post('/portfolio', { portefeuille: portefeuilleArray });
    result.value = res.data;
  } catch (e) {
    error.value = e.response?.data?.error || 'Erreur lors de la simulation';
  } finally {
    loadingSimu.value = false;
  }
};
</script>
