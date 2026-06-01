<template>
  <BaseModal v-model="isOpen" :title="localMeeting.title">
    <div class="space-y-4">
      <!-- Read-only info -->
      <div v-if="localMeeting.offer" class="p-3 bg-gray-50 rounded-lg">
        <p class="form-label mb-0.5">Oferta</p>
        <p class="text-sm font-medium text-gray-800">{{ localMeeting.offer.title }}</p>
      </div>

      <!-- Edit form -->
      <form @submit.prevent="save" class="space-y-3">
        <div>
          <label class="form-label">Tytuł</label>
          <input type="text" v-model="form.title" class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Data i godzina</label>
            <input type="datetime-local" v-model="form.occurs_at" class="form-input">
          </div>
          <div>
            <label class="form-label">Osoba</label>
            <select v-model="form.person_id" class="form-input">
              <option value="">— brak —</option>
              <option v-for="p in persons" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
        </div>
        <div>
          <label class="form-label">URL spotkania</label>
          <input type="url" v-model="form.url" class="form-input">
        </div>
        <div>
          <label class="form-label">Notatka</label>
          <textarea v-model="form.note" rows="3" class="form-input resize-none"></textarea>
        </div>
        <div class="flex items-center justify-between pt-1">
          <button type="button" @click="deleteMeeting" :disabled="deleting" class="btn-danger">
            {{ deleting ? 'Usuwanie…' : 'Usuń spotkanie' }}
          </button>
          <button type="submit" :disabled="saving" class="btn-primary">
            {{ saving ? 'Zapisywanie…' : 'Zapisz zmiany' }}
          </button>
        </div>
      </form>
    </div>
  </BaseModal>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import BaseModal from '../shared/BaseModal.vue'
import { api } from '../../api.js'
import { toDateTimeLocal } from '../../utils.js'

const props = defineProps({ modelValue: Boolean, meeting: Object })
const emit = defineEmits(['update:modelValue', 'updated', 'deleted'])

const isOpen = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v),
})

const localMeeting = ref({ ...props.meeting })
const persons = ref([])
const saving = ref(false)
const deleting = ref(false)

const form = reactive({
  title: props.meeting.title ?? '',
  occurs_at: toDateTimeLocal(props.meeting.occurs_at),
  person_id: props.meeting.person_id ?? '',
  url: props.meeting.url ?? '',
  note: props.meeting.note ?? '',
})

onMounted(async () => {
  const data = await api.get('/persons')
  persons.value = data
})

async function save() {
  saving.value = true
  try {
    const updated = await api.patch(`/meetings/${localMeeting.value.id}`, {
      title: form.title,
      occurs_at: form.occurs_at,
      person_id: form.person_id || null,
      url: form.url || null,
      note: form.note || null,
    })
    localMeeting.value = updated
    emit('updated', updated)
  } catch (e) {
    alert('Błąd podczas zapisywania spotkania.')
  } finally {
    saving.value = false
  }
}

async function deleteMeeting() {
  if (!confirm('Usunąć to spotkanie?')) return
  deleting.value = true
  try {
    await api.delete(`/meetings/${localMeeting.value.id}`)
    emit('deleted', localMeeting.value.id)
    emit('update:modelValue', false)
  } catch (e) {
    alert('Błąd podczas usuwania spotkania.')
  } finally {
    deleting.value = false
  }
}
</script>
