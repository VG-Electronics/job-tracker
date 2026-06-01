<template>
  <div
    class="card p-4 hover:shadow-md hover:border-gray-200 transition-all cursor-pointer"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <h3 class="font-medium text-gray-900">{{ meeting.title }}</h3>
        <p v-if="meeting.offer" class="text-sm text-gray-500 truncate mt-0.5">
          {{ meeting.offer.title }}
        </p>
        <p v-if="meeting.person" class="text-xs text-gray-400 mt-0.5">
          {{ meeting.person.name }}
        </p>
      </div>
      <div class="text-right shrink-0">
        <p class="text-sm font-medium" :class="isPast ? 'text-gray-400' : 'text-blue-600'">
          {{ fmtDateTime(meeting.occurs_at) }}
        </p>
        <a
          v-if="meeting.url"
          :href="meeting.url"
          target="_blank"
          rel="noopener noreferrer"
          class="text-xs text-blue-500 hover:underline mt-0.5 block"
          @click.stop
        >Dołącz</a>
      </div>
    </div>
    <p v-if="meeting.note" class="text-xs text-gray-400 mt-2 line-clamp-2">{{ meeting.note }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { fmtDateTime } from '../../utils.js'

const props = defineProps({ meeting: Object })
defineEmits(['click'])

const isPast = computed(() => new Date(props.meeting.occurs_at) < new Date())
</script>
