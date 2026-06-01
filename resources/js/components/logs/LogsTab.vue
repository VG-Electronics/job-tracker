<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-gray-900">Logi pobierania ofert</h2>
      <button
        @click="load"
        :disabled="loading"
        class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
      >Odśwież</button>
    </div>

    <div v-if="loading" class="text-gray-500 text-sm">Ładowanie...</div>

    <div v-else-if="logs.length === 0" class="text-gray-500 text-sm py-8 text-center">
      Brak logów. Uruchom pobieranie ofert, aby wygenerować logi.
    </div>

    <div v-else class="space-y-2">
      <button
        v-for="log in logs"
        :key="log"
        @click="openLog(log)"
        class="w-full text-left px-4 py-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors"
      >
        <div class="flex items-center justify-between">
          <span class="font-medium text-gray-900">{{ formatFilename(log) }}</span>
          <span class="text-xs text-gray-400 font-mono">{{ log }}</span>
        </div>
      </button>
    </div>

    <BaseModal v-model="modalOpen" :title="selectedLog ? formatFilename(selectedLog) : ''" wide>
      <div v-if="loadingContent" class="text-gray-500 text-sm py-4 text-center">Ładowanie...</div>
      <pre v-else class="text-xs font-mono text-gray-800 whitespace-pre-wrap leading-relaxed">{{ modalContent }}</pre>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../../api.js'
import BaseModal from '../shared/BaseModal.vue'

const logs = ref([])
const loading = ref(false)
const modalOpen = ref(false)
const selectedLog = ref(null)
const modalContent = ref('')
const loadingContent = ref(false)

async function load() {
  loading.value = true
  try {
    logs.value = await api.get('/logs')
  } finally {
    loading.value = false
  }
}

async function openLog(filename) {
  selectedLog.value = filename
  modalContent.value = ''
  modalOpen.value = true
  loadingContent.value = true
  try {
    const data = await api.get(`/logs/${filename}`)
    modalContent.value = data.content
  } finally {
    loadingContent.value = false
  }
}

function formatFilename(filename) {
  const match = filename.match(/^(\d{4})-(\d{2})-(\d{2})_(\d{2})-(\d{2})-(\d{2})\.log$/)
  if (!match) return filename
  const [, year, month, day, hour, min, sec] = match
  return `${day}.${month}.${year} ${hour}:${min}:${sec}`
}

onMounted(load)
</script>
