<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800">Spotkania</h2>
    </div>

    <div class="card p-4 flex flex-wrap gap-4 items-end">
      <div>
        <label class="form-label">Status</label>
        <select v-model="filters.status" class="form-input w-auto">
          <option value="">Wszystkie</option>
          <option value="upcoming">Nadchodzące</option>
          <option value="past">Minione</option>
        </select>
      </div>
      <div>
        <label class="form-label">Sortowanie daty</label>
        <select v-model="filters.sort_dir" class="form-input w-auto">
          <option value="asc">Rosnąco</option>
          <option value="desc">Malejąco</option>
        </select>
      </div>
    </div>

    <div v-if="meetings.length" class="space-y-3">
      <MeetingCard
        v-for="meeting in meetings"
        :key="meeting.id"
        :meeting="meeting"
        @click="openModal(meeting)"
      />
    </div>
    <div v-if="loading" class="flex justify-center py-6">
      <span class="text-sm text-gray-400">Ładowanie…</span>
    </div>
    <div v-else-if="!meetings.length" class="text-center text-gray-400 py-16">
      Brak spotkań
    </div>

    <MeetingModal
      v-if="showModal && selectedMeeting"
      v-model="showModal"
      :meeting="selectedMeeting"
      @updated="onUpdated"
      @deleted="onDeleted"
    />
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { api } from '../../api.js'
import MeetingCard from './MeetingCard.vue'
import MeetingModal from './MeetingModal.vue'

const meetings = ref([])
const loading = ref(false)
const selectedMeeting = ref(null)
const showModal = ref(false)

const filters = reactive({ status: 'upcoming', sort_dir: 'asc' })

async function load() {
  loading.value = true
  try {
    meetings.value = await api.get('/meetings', filters)
  } finally {
    loading.value = false
  }
}

function openModal(meeting) {
  selectedMeeting.value = meeting
  showModal.value = true
}

function onUpdated(updated) {
  const idx = meetings.value.findIndex(m => m.id === updated.id)
  if (idx !== -1) meetings.value[idx] = updated
  selectedMeeting.value = updated
}

function onDeleted(id) {
  meetings.value = meetings.value.filter(m => m.id !== id)
  selectedMeeting.value = null
}

watch(filters, load, { deep: true })
onMounted(load)
</script>
