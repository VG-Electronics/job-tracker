<template>
  <div class="card p-4 flex flex-col gap-3">
    <!-- Rząd 1: Szukaj, Status, Sortowanie -->
    <div class="flex flex-wrap gap-4 items-end">
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
        <label class="form-label">Status</label>
        <select v-model="local.status" class="form-input w-auto">
          <option value="">Wszystkie</option>
          <option v-for="s in OFFER_STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
        </select>
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
    </div>

    <!-- Rząd 2: Wynagrodzenie, Ulubione, Tylko dzisiejsze -->
    <div class="flex flex-wrap gap-4 items-end">
      <div class="flex gap-2">
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
          <label class="form-label">Typ</label>
          <select v-model="local.salary_type" class="form-input w-auto">
            <option value="">Dowolny</option>
            <option v-for="t in SALARY_TYPES" :key="t.value" :value="t.value">{{ t.label }}</option>
          </select>
        </div>
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
      <div>
        <label class="form-label">Tylko dzisiejsze</label>
        <button
          type="button"
          @click="local.today_only = !local.today_only"
          :class="['form-input w-auto flex items-center gap-1.5 transition-colors', local.today_only ? 'bg-blue-50 border-blue-400 text-blue-600' : 'text-gray-400']"
        >
          <span>{{ local.today_only ? '✓' : '○' }}</span>
          <span class="text-sm">{{ local.today_only ? 'Dzisiaj' : 'Wszystkie' }}</span>
        </button>
      </div>
      <div class="relative" ref="hostDropdownRef">
        <label class="form-label">Domena</label>
        <button
          type="button"
          @click="hostDropdownOpen = !hostDropdownOpen"
          :class="['form-input w-48 flex items-center justify-between gap-2 text-left transition-colors', local.hosts.length ? 'bg-blue-50 border-blue-400 text-blue-600' : 'text-gray-400']"
        >
          <span class="truncate">{{ hostFilterLabel }}</span>
          <span class="text-xs shrink-0">▾</span>
        </button>
        <div
          v-if="hostDropdownOpen"
          class="absolute z-20 mt-1 w-56 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg py-1"
        >
          <label
            v-for="host in hostOptions"
            :key="host"
            class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer"
          >
            <input type="checkbox" :value="host" v-model="local.hosts" class="w-4 h-4 rounded">
            <span class="truncate">{{ host }}</span>
          </label>
          <div v-if="!hostOptions.length" class="px-3 py-2 text-xs text-gray-400">Brak zdefiniowanych stron</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref, onMounted, onUnmounted } from 'vue'
import { api } from '../../api.js'
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

const websites = ref([])
const hostDropdownOpen = ref(false)
const hostDropdownRef = ref(null)

function extractHost(url) {
  try {
    return new URL(url).hostname.replace(/^www\./i, '').toLowerCase()
  } catch {
    return null
  }
}

const hostOptions = computed(() => {
  const hosts = websites.value.map(w => extractHost(w.url)).filter(Boolean)
  return [...new Set(hosts)].sort()
})

const hostFilterLabel = computed(() => {
  const count = local.hosts.length
  if (count === 0) return 'Wszystkie'
  if (count === 1) return local.hosts[0]
  return `${count} wybrane`
})

function handleClickOutside(e) {
  if (hostDropdownOpen.value && hostDropdownRef.value && !hostDropdownRef.value.contains(e.target)) {
    hostDropdownOpen.value = false
  }
}

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  websites.value = await api.get('/websites')
})
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

watch(local, () => emit('update:modelValue', { ...local }), { deep: true })
</script>
