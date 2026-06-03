<template>
  <div
    class="card p-4 hover:shadow-md hover:border-gray-200 transition-all cursor-pointer"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1.5">
          <select
            :value="offer.status"
            :class="['text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer', STATUS_CLASSES[offer.status]]"
            :disabled="saving"
            @click.stop
            @change.stop="changeStatus($event.target.value)"
          >
            <option v-for="s in OFFER_STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
          </select>
          <span class="text-xs text-gray-400">{{ fmtDate(offer.created_at) }}</span>
        </div>
        <h3 class="font-medium text-gray-900 truncate">{{ offer.title }}</h3>
        <p v-if="offer.company || offer.recruitment_company" class="text-xs text-gray-500 truncate mt-0.5">
          {{ offer.company }}<span v-if="offer.company && offer.recruitment_company"> · </span><span v-if="offer.recruitment_company" class="text-gray-400">{{ offer.recruitment_company }}</span>
        </p>
        <a
          :href="offer.url"
          target="_blank"
          rel="noopener noreferrer"
          class="text-xs text-blue-500 hover:underline truncate block mt-0.5"
          @click.stop
        >{{ offer.url }}</a>
      </div>
      <div class="flex flex-col items-end gap-1.5 shrink-0">
        <button
          type="button"
          :class="['text-xl leading-none transition-colors', offer.is_starred ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300']"
          :disabled="saving"
          @click.stop="toggleStar"
          title="Oznacz jako ulubione"
        >{{ offer.is_starred ? '★' : '☆' }}</button>
        <p v-if="salary" class="text-sm font-semibold text-gray-700">{{ salary }}</p>
        <p v-if="nextMeeting" class="text-xs text-gray-500">
          📅 {{ nextMeeting.title }}<br>
          <span class="text-gray-400">{{ fmtDateTime(nextMeeting.occurs_at) }}</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { api } from '../../api.js'
import { fmtDate, fmtDateTime, fmtSalary, STATUS_CLASSES, OFFER_STATUSES } from '../../utils.js'

const props = defineProps({ offer: Object })
const emit = defineEmits(['click', 'updated'])

const saving = ref(false)

async function changeStatus(newStatus) {
  if (newStatus === props.offer.status) return
  saving.value = true
  try {
    const updated = await api.patch(`/offers/${props.offer.id}`, { status: newStatus })
    emit('updated', { ...props.offer, ...updated })
  } catch {
    alert('Błąd podczas zmiany statusu.')
  } finally {
    saving.value = false
  }
}

async function toggleStar() {
  saving.value = true
  try {
    const updated = await api.patch(`/offers/${props.offer.id}`, { is_starred: !props.offer.is_starred })
    emit('updated', { ...props.offer, ...updated })
  } catch {
    alert('Błąd podczas zmiany ulubionej.')
  } finally {
    saving.value = false
  }
}

const salary = computed(() => fmtSalary(props.offer))

const nextMeeting = computed(() => {
  const meetings = props.offer.meetings ?? []
  const now = new Date()
  const upcoming = meetings
    .filter(m => new Date(m.occurs_at) >= now)
    .sort((a, b) => new Date(a.occurs_at) - new Date(b.occurs_at))
  if (upcoming.length) return upcoming[0]
  return meetings.sort((a, b) => new Date(b.occurs_at) - new Date(a.occurs_at))[0] ?? null
})
</script>
