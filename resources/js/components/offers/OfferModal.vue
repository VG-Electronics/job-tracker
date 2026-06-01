<template>
  <BaseModal v-model="isOpen" :title="localOffer.title" wide>
    <div v-if="localOffer.url" class="mb-5">
      <a
        :href="localOffer.url"
        target="_blank"
        rel="noopener noreferrer"
        class="btn-secondary inline-flex items-center gap-1.5"
        @click.stop
      >↗ Zobacz ofertę</a>
    </div>

    <!-- Offer details -->
    <section class="mb-6">
      <p class="section-title">Szczegóły oferty</p>
      <form @submit.prevent="saveOffer" class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Status</label>
            <select v-model="form.status" class="form-input">
              <option v-for="s in OFFER_STATUSES" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>
          <div>
            <label class="form-label">Tytuł</label>
            <input type="text" v-model="form.title" class="form-input" required>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Firma (pracodawca)</label>
            <input type="text" v-model="form.company" class="form-input">
          </div>
          <div>
            <label class="form-label">Firma rekrutacyjna</label>
            <input type="text" v-model="form.recruitment_company" class="form-input">
          </div>
        </div>
        <div>
          <label class="form-label">URL</label>
          <input type="url" v-model="form.url" class="form-input">
        </div>
        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="form-label">Min. wynagrodzenie</label>
            <input type="number" v-model.number="form.min_salary" min="0" class="form-input">
          </div>
          <div>
            <label class="form-label">Max. wynagrodzenie</label>
            <input type="number" v-model.number="form.max_salary" min="0" class="form-input">
          </div>
          <div>
            <label class="form-label">Typ</label>
            <select v-model="form.salary_type" class="form-input">
              <option value="">—</option>
              <option v-for="t in SALARY_TYPES" :key="t.value" :value="t.value">{{ t.label }}</option>
            </select>
          </div>
        </div>
        <div>
          <label class="form-label">Opis</label>
          <textarea v-model="form.description" rows="3" class="form-input resize-none"></textarea>
        </div>
        <div>
          <label class="form-label">Notatka</label>
          <textarea v-model="form.note" rows="2" class="form-input resize-none"></textarea>
        </div>
        <div class="flex justify-end">
          <button type="submit" :disabled="saving" class="btn-primary">
            {{ saving ? 'Zapisywanie…' : 'Zapisz zmiany' }}
          </button>
        </div>
      </form>
    </section>

    <hr class="border-gray-100 mb-5">

    <!-- Persons -->
    <section class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <p class="section-title mb-0">Osoby</p>
        <button @click="showPersonForm = !showPersonForm" class="btn-secondary text-xs py-1">
          + Dodaj osobę
        </button>
      </div>

      <div v-if="showPersonForm" class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
        <form @submit.prevent="addPerson" class="space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="form-label">Imię i nazwisko *</label>
              <input type="text" v-model="personForm.name" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Rola *</label>
              <select v-model="personForm.role" class="form-input" required>
                <option value="">Wybierz</option>
                <option value="recruiter">Rekruter</option>
                <option value="employee">Pracownik</option>
              </select>
            </div>
            <div>
              <label class="form-label">Email</label>
              <input type="email" v-model="personForm.email" class="form-input">
            </div>
            <div>
              <label class="form-label">Telefon</label>
              <input type="tel" v-model="personForm.phone" class="form-input">
            </div>
          </div>
          <div>
            <label class="form-label">LinkedIn URL</label>
            <input type="url" v-model="personForm.linkedin_url" class="form-input">
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="showPersonForm = false" class="btn-secondary">Anuluj</button>
            <button type="submit" :disabled="addingPerson" class="btn-primary">
              {{ addingPerson ? 'Dodawanie…' : 'Dodaj' }}
            </button>
          </div>
        </form>
      </div>

      <div v-if="localOffer.persons?.length" class="space-y-2">
        <div
          v-for="person in localOffer.persons"
          :key="person.id"
          class="flex items-center justify-between p-2.5 rounded-md bg-gray-50 text-sm"
        >
          <div>
            <span class="font-medium text-gray-800">{{ person.name }}</span>
            <span class="ml-2 text-xs px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">
              {{ ROLE_LABELS[person.role] }}
            </span>
          </div>
          <div class="text-xs text-gray-500 flex gap-3">
            <span v-if="person.email">{{ person.email }}</span>
            <span v-if="person.phone">{{ person.phone }}</span>
            <a v-if="person.linkedin_url" :href="person.linkedin_url" target="_blank" class="text-blue-500 hover:underline" @click.stop>LinkedIn</a>
          </div>
        </div>
      </div>
      <p v-else-if="!showPersonForm" class="text-sm text-gray-400">Brak przypisanych osób.</p>
    </section>

    <hr class="border-gray-100 mb-5">

    <!-- Meetings -->
    <section>
      <div class="flex items-center justify-between mb-3">
        <p class="section-title mb-0">Spotkania</p>
        <button
          @click="showMeetingForm = !showMeetingForm"
          :disabled="!localOffer.persons?.length"
          :title="!localOffer.persons?.length ? 'Najpierw dodaj osobę' : ''"
          class="btn-secondary text-xs py-1"
        >+ Dodaj spotkanie</button>
      </div>

      <div v-if="showMeetingForm" class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
        <form @submit.prevent="addMeeting" class="space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="form-label">Tytuł *</label>
              <input type="text" v-model="meetingForm.title" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Osoba *</label>
              <select v-model="meetingForm.person_id" class="form-input" required>
                <option value="">Wybierz osobę</option>
                <option v-for="p in localOffer.persons" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="form-label">Data i godzina *</label>
              <input type="datetime-local" v-model="meetingForm.occurs_at" class="form-input" required>
            </div>
            <div>
              <label class="form-label">URL spotkania</label>
              <input type="url" v-model="meetingForm.url" class="form-input">
            </div>
          </div>
          <div>
            <label class="form-label">Notatka</label>
            <textarea v-model="meetingForm.note" rows="2" class="form-input resize-none"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="showMeetingForm = false" class="btn-secondary">Anuluj</button>
            <button type="submit" :disabled="addingMeeting" class="btn-primary">
              {{ addingMeeting ? 'Dodawanie…' : 'Dodaj' }}
            </button>
          </div>
        </form>
      </div>

      <div v-if="localOffer.meetings?.length" class="space-y-2">
        <div
          v-for="meeting in sortedMeetings"
          :key="meeting.id"
          class="p-2.5 rounded-md bg-gray-50"
        >
          <div class="flex items-start justify-between">
            <div>
              <span class="text-sm font-medium text-gray-800">{{ meeting.title }}</span>
              <span v-if="meeting.person" class="text-xs text-gray-500 ml-1.5">· {{ meeting.person.name }}</span>
            </div>
            <span class="text-xs text-gray-500 shrink-0 ml-2">{{ fmtDateTime(meeting.occurs_at) }}</span>
          </div>
          <a v-if="meeting.url" :href="meeting.url" target="_blank" class="text-xs text-blue-500 hover:underline mt-0.5 block" @click.stop>
            Link do spotkania
          </a>
          <p v-if="meeting.note" class="text-xs text-gray-500 mt-0.5">{{ meeting.note }}</p>
        </div>
      </div>
      <p v-else-if="!showMeetingForm" class="text-sm text-gray-400">Brak spotkań.</p>
    </section>
  </BaseModal>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import BaseModal from '../shared/BaseModal.vue'
import { api } from '../../api.js'
import { fmtDateTime, OFFER_STATUSES, SALARY_TYPES, ROLE_LABELS } from '../../utils.js'

const props = defineProps({ modelValue: Boolean, offer: Object })
const emit = defineEmits(['update:modelValue', 'updated'])

const isOpen = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v),
})

const localOffer = ref({ ...props.offer })

function initForm(o) {
  return {
    status: o.status ?? '',
    title: o.title ?? '',
    company: o.company ?? '',
    recruitment_company: o.recruitment_company ?? '',
    description: o.description ?? '',
    url: o.url ?? '',
    min_salary: o.min_salary ?? '',
    max_salary: o.max_salary ?? '',
    salary_type: o.salary_type ?? '',
    note: o.note ?? '',
  }
}

const form = reactive(initForm(props.offer))

watch(() => props.offer, o => {
  localOffer.value = { ...o }
  Object.assign(form, initForm(o))
}, { deep: true })

const saving = ref(false)
const showPersonForm = ref(false)
const showMeetingForm = ref(false)
const addingPerson = ref(false)
const addingMeeting = ref(false)

const personForm = reactive({ name: '', email: '', phone: '', role: '', linkedin_url: '' })
const meetingForm = reactive({ title: '', person_id: '', occurs_at: '', url: '', note: '' })

const sortedMeetings = computed(() =>
  [...(localOffer.value.meetings ?? [])].sort((a, b) => new Date(a.occurs_at) - new Date(b.occurs_at))
)

async function saveOffer() {
  saving.value = true
  try {
    const updated = await api.patch(`/offers/${localOffer.value.id}`, {
      status: form.status,
      title: form.title,
      company: form.company || null,
      recruitment_company: form.recruitment_company || null,
      description: form.description || null,
      url: form.url,
      min_salary: form.min_salary || null,
      max_salary: form.max_salary || null,
      salary_type: form.salary_type || null,
      note: form.note || null,
    })
    localOffer.value = { ...updated, persons: localOffer.value.persons, meetings: localOffer.value.meetings }
    emit('updated', localOffer.value)
    isOpen.value = false
  } catch (e) {
    alert('Błąd podczas zapisywania oferty.')
  } finally {
    saving.value = false
  }
}

async function addPerson() {
  addingPerson.value = true
  try {
    const person = await api.post('/persons', {
      offer_id: localOffer.value.id,
      name: personForm.name,
      email: personForm.email || null,
      phone: personForm.phone || null,
      role: personForm.role,
      linkedin_url: personForm.linkedin_url || null,
    })
    if (!localOffer.value.persons) localOffer.value.persons = []
    localOffer.value.persons.push(person)
    emit('updated', { ...localOffer.value })
    Object.assign(personForm, { name: '', email: '', phone: '', role: '', linkedin_url: '' })
    showPersonForm.value = false
  } catch (e) {
    alert('Błąd podczas dodawania osoby.')
  } finally {
    addingPerson.value = false
  }
}

async function addMeeting() {
  addingMeeting.value = true
  try {
    const meeting = await api.post('/meetings', {
      offer_id: localOffer.value.id,
      person_id: Number(meetingForm.person_id),
      occurs_at: meetingForm.occurs_at,
      title: meetingForm.title,
      url: meetingForm.url || null,
      note: meetingForm.note || null,
    })
    if (!localOffer.value.meetings) localOffer.value.meetings = []
    localOffer.value.meetings.push(meeting)
    emit('updated', { ...localOffer.value })
    Object.assign(meetingForm, { title: '', person_id: '', occurs_at: '', url: '', note: '' })
    showMeetingForm.value = false
  } catch (e) {
    alert('Błąd podczas dodawania spotkania.')
  } finally {
    addingMeeting.value = false
  }
}
</script>
