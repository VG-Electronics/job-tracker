<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800">Osoby</h2>
    </div>

    <div v-if="persons.length" class="space-y-3">
      <PersonCard
        v-for="person in persons"
        :key="person.id"
        :person="person"
        @click="openModal(person)"
      />
    </div>
    <div v-if="loading" class="flex justify-center py-6">
      <span class="text-sm text-gray-400">Ładowanie…</span>
    </div>
    <div v-else-if="!persons.length" class="text-center text-gray-400 py-16">
      Brak osób
    </div>

    <PersonModal
      v-if="showModal && selectedPerson"
      v-model="showModal"
      :person="selectedPerson"
      @updated="onUpdated"
      @deleted="onDeleted"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../../api.js'
import PersonCard from './PersonCard.vue'
import PersonModal from './PersonModal.vue'

const persons = ref([])
const loading = ref(false)
const selectedPerson = ref(null)
const showModal = ref(false)

async function load() {
  loading.value = true
  try {
    persons.value = await api.get('/persons')
  } finally {
    loading.value = false
  }
}

function openModal(person) {
  selectedPerson.value = person
  showModal.value = true
}

function onUpdated(updated) {
  const idx = persons.value.findIndex(p => p.id === updated.id)
  if (idx !== -1) persons.value[idx] = updated
  selectedPerson.value = updated
}

function onDeleted(id) {
  persons.value = persons.value.filter(p => p.id !== id)
  selectedPerson.value = null
}

onMounted(load)
</script>
