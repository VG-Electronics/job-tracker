<template>
  <div class="card p-4 flex flex-wrap gap-4 items-end">
    <div class="w-full sm:w-auto flex-1 min-w-48">
      <label class="form-label">Szukaj</label>
      <input
        type="text"
        v-model="local.search"
        placeholder="Tytuł, firma, opis…"
        class="form-input w-full"
      >
    </div>
    <div>
      <label class="form-label">Sortowanie</label>
      <select v-model="sortKey" class="form-input w-auto">
        <option value="date:desc">Data (najnowsze)</option>
        <option value="date:asc">Data (najstarsze)</option>
        <option value="salary:desc">Wynagrodzenie ↓</option>
        <option value="salary:asc">Wynagrodzenie ↑</option>
      </select>
    </div>
    <div>
      <label class="form-label">Status</label>
      <select v-model="local.status" class="form-input w-auto">
        <option value="">Wszystkie</option>
        <option v-for="s in OFFER_STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
      </select>
    </div>
    <div>
      <label class="form-label">Min. wynagrodzenie</label>
      <input
        type="number"
        v-model.number="local.min_salary"
        min="0"
        placeholder="0"
        class="form-input w-28"
      >
    </div>
    <div>
      <label class="form-label">Typ wynagrodzenia</label>
      <select v-model="local.salary_type" class="form-input w-auto">
        <option value="">Dowolny</option>
        <option v-for="t in SALARY_TYPES" :key="t.value" :value="t.value">{{ t.label }}</option>
      </select>
    </div>
    <div>
      <label class="form-label">Ulubione</label>
      <button
        type="button"
        @click="local.starred = !local.starred"
        :class="['form-input w-auto flex items-center gap-1.5 transition-colors', local.starred ? 'bg-yellow-50 border-yellow-400 text-yellow-600' : 'text-gray-400']"
      >
        <span>{{ local.starred ? '★' : '☆' }}</span>
        <span class="text-sm">{{ local.starred ? 'Tylko ulubione' : 'Wszystkie' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import { OFFER_STATUSES, SALARY_TYPES } from '../../utils.js'

const props = defineProps({ modelValue: Object })
const emit = defineEmits(['update:modelValue'])

const local = reactive({ ...props.modelValue })

const sortKey = computed({
  get: () => `${local.sort_by}:${local.sort_dir}`,
  set: v => {
    const [sort_by, sort_dir] = v.split(':')
    local.sort_by = sort_by
    local.sort_dir = sort_dir
  },
})

watch(local, () => emit('update:modelValue', { ...local }), { deep: true })
</script>
