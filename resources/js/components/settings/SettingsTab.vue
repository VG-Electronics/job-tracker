<template>
  <div class="space-y-6">
    <div class="card p-4 space-y-3">
      <h2 class="text-lg font-semibold text-gray-800">Ustawienia AI</h2>
      <div class="space-y-1">
        <label class="text-sm font-medium text-gray-700">Dodatkowa instrukcja AI</label>
        <textarea
          v-model="aiInstruction"
          rows="4"
          class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-y"
          placeholder="Np. Uwzględniaj tylko oferty zdalne. Pomijaj oferty agencji rekrutacyjnych."
        />
        <p class="text-xs text-gray-400">Instrukcja zostanie dodana do promptu AI powyżej sekcji „For each offer extract:".</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="saveAiInstruction" class="btn-primary" :disabled="savingAi">
          {{ savingAi ? 'Zapisywanie…' : 'Zapisz' }}
        </button>
        <span v-if="savedAi" class="text-sm text-green-600">Zapisano</span>
      </div>
    </div>

    <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800">Ustawienia — strony do scrapowania</h2>
      <div class="flex gap-2">
        <button @click="showConfirm = true" class="btn-danger">Usuń wszystkie oferty</button>
        <button @click="openAdd" class="btn-primary">+ Dodaj stronę</button>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="showConfirm = false" />
        <div class="relative z-10 bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 space-y-4">
          <h3 class="text-base font-semibold text-gray-900">Usuń wszystkie oferty</h3>
          <p class="text-sm text-gray-600">
            Ta operacja usunie <strong>wszystkie</strong> oferty, spotkania i osoby kontaktowe.
            Ustawienia stron do scrapowania zostaną zachowane.
            Tej operacji <strong>nie można cofnąć</strong>.
          </p>
          <div class="flex justify-end gap-2 pt-2">
            <button @click="showConfirm = false" class="btn-secondary" :disabled="clearing">Anuluj</button>
            <button @click="clearDatabase" class="btn-danger" :disabled="clearing">
              {{ clearing ? 'Czyszczenie…' : 'Tak, wyczyść' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <div v-if="websites.length" class="card divide-y divide-gray-50">
      <div
        v-for="site in websites"
        :key="site.id"
        class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors"
      >
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 text-sm">{{ site.name }}</p>
          <a
            :href="site.url"
            target="_blank"
            rel="noopener noreferrer"
            class="text-xs text-blue-500 hover:underline truncate block"
            @click.stop
          >{{ site.url }}</a>
        </div>
        <button @click="openEdit(site)" class="btn-secondary ml-3 text-xs py-1">Edytuj</button>
      </div>
    </div>
    <div v-if="loading" class="flex justify-center py-6">
      <span class="text-sm text-gray-400">Ładowanie…</span>
    </div>
    <div v-else-if="!websites.length" class="text-center text-gray-400 py-16">
      Brak stron. Dodaj pierwszą stronę do scrapowania.
    </div>

    <WebsiteModal
      v-if="showModal"
      v-model="showModal"
      :website="selectedSite"
      @saved="onSaved"
      @deleted="onDeleted"
    />
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../../api.js'
import WebsiteModal from './WebsiteModal.vue'

const websites = ref([])
const loading = ref(false)
const selectedSite = ref(null)
const showModal = ref(false)
const showConfirm = ref(false)
const clearing = ref(false)

const aiInstruction = ref('')
const savingAi = ref(false)
const savedAi = ref(false)

async function loadSettings() {
  const data = await api.get('/settings')
  aiInstruction.value = data.ai_additional_instruction ?? ''
}

async function saveAiInstruction() {
  savingAi.value = true
  savedAi.value = false
  try {
    await api.patch('/settings', { ai_additional_instruction: aiInstruction.value || null })
    savedAi.value = true
    setTimeout(() => { savedAi.value = false }, 2000)
  } finally {
    savingAi.value = false
  }
}

async function load() {
  loading.value = true
  try {
    websites.value = await api.get('/websites')
  } finally {
    loading.value = false
  }
}

function openAdd() {
  selectedSite.value = null
  showModal.value = true
}

function openEdit(site) {
  selectedSite.value = site
  showModal.value = true
}

function onSaved(result) {
  const idx = websites.value.findIndex(s => s.id === result.id)
  if (idx !== -1) {
    websites.value[idx] = result
  } else {
    websites.value.push(result)
  }
}

function onDeleted(id) {
  websites.value = websites.value.filter(s => s.id !== id)
}

async function clearDatabase() {
  clearing.value = true
  try {
    await api.delete('/database')
    showConfirm.value = false
  } finally {
    clearing.value = false
  }
}

onMounted(() => {
  load()
  loadSettings()
})
</script>
