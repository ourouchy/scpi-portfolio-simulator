<template>
  <div>
    <div v-for="scpi in scpis" :key="scpi.id" class="mb-4 flex items-center gap-2">
      <span class="w-40">{{ scpi.nom }} ({{ scpi.tauxRendementAnnuel }}%)</span>
      <input type="number" min="0" step="100" :value="modelValue[scpi.id] || ''" @input="onInput(scpi.id, $event.target.value)" placeholder="Montant €" class="border rounded px-2 py-1 w-32" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  scpis: { type: Array, required: true },
  modelValue: { type: Object, required: true },
});
const emit = defineEmits(['update:modelValue']);

function onInput(id, value) {
  const newValue = { ...props.modelValue, [id]: Number(value) };
  emit('update:modelValue', newValue);
}
</script>
