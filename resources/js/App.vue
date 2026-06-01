<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
      <div class="max-w-5xl mx-auto px-4 flex items-center gap-1 h-12">
        <span class="text-sm font-bold text-gray-700 mr-5 tracking-widest uppercase">Job Tracker</span>
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="active = tab.id"
          :class="[
            'px-4 py-1.5 rounded-md text-sm font-medium transition-colors',
            active === tab.id ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100',
          ]"
        >{{ tab.label }}</button>
      </div>
    </header>
    <main class="max-w-5xl mx-auto px-4 py-6">
      <OffersTab v-if="active === 'offers'" />
      <MeetingsTab v-else-if="active === 'meetings'" />
      <PersonsTab v-else-if="active === 'persons'" />
      <SettingsTab v-else-if="active === 'settings'" />
      <LogsTab v-else-if="active === 'logs'" />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import OffersTab from './components/offers/OffersTab.vue'
import MeetingsTab from './components/meetings/MeetingsTab.vue'
import PersonsTab from './components/persons/PersonsTab.vue'
import SettingsTab from './components/settings/SettingsTab.vue'
import LogsTab from './components/logs/LogsTab.vue'

const tabs = [
  { id: 'offers', label: 'Oferty' },
  { id: 'meetings', label: 'Spotkania' },
  { id: 'persons', label: 'Osoby' },
  { id: 'settings', label: 'Ustawienia' },
  { id: 'logs', label: 'Logi' },
]

const active = ref('offers')
</script>
