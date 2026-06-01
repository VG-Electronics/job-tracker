<template>
  <div
    class="card p-4 hover:shadow-md hover:border-gray-200 transition-all cursor-pointer"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <span class="font-medium text-gray-900">{{ person.name }}</span>
          <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">
            {{ ROLE_LABELS[person.role] }}
          </span>
        </div>
        <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-500">
          <span v-if="person.email">{{ person.email }}</span>
          <span v-if="person.phone">{{ person.phone }}</span>
          <a
            v-if="person.linkedin_url"
            :href="person.linkedin_url"
            target="_blank"
            class="text-blue-500 hover:underline"
            @click.stop
          >LinkedIn</a>
        </div>
      </div>
      <div v-if="person.offers?.length" class="text-right shrink-0">
        <p class="text-xs text-gray-400 mb-1">Oferty ({{ person.offers.length }})</p>
        <div class="space-y-0.5">
          <p
            v-for="offer in person.offers.slice(0, 3)"
            :key="offer.id"
            class="text-xs text-gray-600 truncate max-w-[160px]"
          >{{ offer.title }}</p>
          <p v-if="person.offers.length > 3" class="text-xs text-gray-400">
            +{{ person.offers.length - 3 }} więcej
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ROLE_LABELS } from '../../utils.js'

defineProps({ person: Object })
defineEmits(['click'])
</script>
