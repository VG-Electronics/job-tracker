<template>
  <BaseModal v-model="isOpen" :title="localPerson.name">
    <form @submit.prevent="save" class="space-y-3">
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="form-label">Imię i nazwisko</label>
          <input type="text" v-model="form.name" class="form-input">
        </div>
        <div>
          <label class="form-label">Rola</label>
          <select v-model="form.role" class="form-input">
            <option value="recruiter">Rekruter</option>
            <option value="employee">Pracownik</option>
          </select>
        </div>
        <div>
          <label class="form-label">Email</label>
          <input type="email" v-model="form.email" class="form-input">
        </div>
        <div>
          <label class="form-label">Telefon</label>
          <input type="tel" v-model="form.phone" class="form-input">
        </div>
      </div>
      <div>
        <label class="form-label">LinkedIn URL</label>
        <input type="url" v-model="form.linkedin_url" class="form-input">
      </div>

      <div v-if="localPerson.offers?.length" class="pt-1">
        <p class="form-label">Przypisane oferty</p>
        <div class="space-y-1">
          <div
            v-for="offer in localPerson.offers"
            :key="offer.id"
            class="text-sm text-gray-700 py-1 border-b border-gray-100 last:border-0"
          >{{ offer.title }}</div>
        </div>
      </div>

      <div class="flex items-center justify-between pt-1">
        <button type="button" @click="deletePerson" :disabled="deleting" class="btn-danger">
          {{ deleting ? 'Usuwanie…' : 'Usuń osobę' }}
        </button>
        <button type="submit" :disabled="saving" class="btn-primary">
          {{ saving ? 'Zapisywanie…' : 'Zapisz zmiany' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import BaseModal from '../shared/BaseModal.vue'
import { api } from '../../api.js'

const props = defineProps({ modelValue: Boolean, person: Object })
const emit = defineEmits(['update:modelValue', 'updated', 'deleted'])

const isOpen = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v),
})

const localPerson = ref({ ...props.person })
const saving = ref(false)
const deleting = ref(false)

const form = reactive({
  name: props.person.name ?? '',
  email: props.person.email ?? '',
  phone: props.person.phone ?? '',
  role: props.person.role ?? 'recruiter',
  linkedin_url: props.person.linkedin_url ?? '',
})

async function save() {
  saving.value = true
  try {
    const updated = await api.patch(`/persons/${localPerson.value.id}`, {
      name: form.name,
      email: form.email || null,
      phone: form.phone || null,
      role: form.role,
      linkedin_url: form.linkedin_url || null,
    })
    localPerson.value = { ...updated, offers: localPerson.value.offers }
    emit('updated', localPerson.value)
  } catch (e) {
    alert('Błąd podczas zapisywania osoby.')
  } finally {
    saving.value = false
  }
}

async function deletePerson() {
  if (!confirm('Usunąć tę osobę?')) return
  deleting.value = true
  try {
    await api.delete(`/persons/${localPerson.value.id}`)
    emit('deleted', localPerson.value.id)
    emit('update:modelValue', false)
  } catch (e) {
    alert('Błąd podczas usuwania osoby.')
  } finally {
    deleting.value = false
  }
}
</script>
