<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800">Oferty pracy</h2>
      <button @click="fetchNew" :disabled="fetching" class="btn-primary">
        {{ fetching ? 'Pobieranie…' : 'Odśwież oferty' }}
      </button>
    </div>

    <OffersFilters :model-value="filters" @update:model-value="val => Object.assign(filters, val)" />

    <div v-if="offers.length" class="space-y-3">
      <OfferCard
        v-for="offer in offers"
        :key="offer.id"
        :offer="offer"
        @click="openModal(offer)"
        @updated="onOfferUpdated"
      />
    </div>

    <div v-if="loading" class="flex justify-center py-6">
      <span class="text-sm text-gray-400">Ładowanie…</span>
    </div>
    <div v-else-if="!offers.length" class="text-center text-gray-400 py-16">
      Brak ofert pasujących do kryteriów
    </div>

    <div ref="sentinel" class="h-1" />

    <OfferModal
      v-if="selectedOffer"
      v-model="showModal"
      :offer="selectedOffer"
      @updated="onOfferUpdated"
    />
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue'
import { api } from '../../api.js'
import OffersFilters from './OffersFilters.vue'
import OfferCard from './OfferCard.vue'
import OfferModal from './OfferModal.vue'

const offers = ref([])
const loading = ref(false)
const currentPage = ref(0)
const lastPage = ref(1)
const fetching = ref(false)
const selectedOffer = ref(null)
const showModal = ref(false)
const sentinel = ref(null)

const filters = reactive({
  sort_by: 'date',
  sort_dir: 'desc',
  status: '',
  min_salary: '',
  salary_type: '',
  search: '',
  starred: false,
})

async function load(reset = false) {
  if (loading.value) return
  if (!reset && currentPage.value >= lastPage.value) return
  loading.value = true
  const page = reset ? 1 : currentPage.value + 1
  try {
    const res = await api.get('/offers', { ...filters, page })
    offers.value = reset ? res.data : [...offers.value, ...res.data]
    currentPage.value = res.current_page
    lastPage.value = res.last_page
  } finally {
    loading.value = false
  }
}

async function fetchNew() {
  fetching.value = true
  try {
    const { count } = await api.post('/offers/fetch')
    if (count > 0) await load(true)
  } catch (e) {
    alert('Błąd podczas pobierania ofert.')
  } finally {
    fetching.value = false
  }
}

function openModal(offer) {
  selectedOffer.value = offer
  showModal.value = true
}

function onOfferUpdated(updated) {
  const idx = offers.value.findIndex(o => o.id === updated.id)
  if (idx === -1) return
  if (filters.status && updated.status !== filters.status) {
    offers.value.splice(idx, 1)
  } else if (filters.starred && !updated.is_starred) {
    offers.value.splice(idx, 1)
  } else {
    offers.value[idx] = updated
    selectedOffer.value = updated
  }
}

watch(filters, () => load(true), { deep: true })

let observer
onMounted(() => {
  load(true)
  observer = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) load(false)
  })
  observer.observe(sentinel.value)
})

onUnmounted(() => observer?.disconnect())
</script>
