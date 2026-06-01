<template>
  <BaseModal v-model="isOpen" :title="website ? 'Edytuj stronę' : 'Dodaj stronę'">
    <form @submit.prevent="save" class="space-y-3">
      <div>
        <label class="form-label">Nazwa *</label>
        <input type="text" v-model="form.name" class="form-input" required>
      </div>
      <div>
        <label class="form-label">URL *</label>
        <input type="url" v-model="form.url" class="form-input" required placeholder="https://...">
      </div>
      <div>
        <label class="form-label">Fragment URL ofert</label>
        <input type="text" v-model="form.offer_url_part" class="form-input" placeholder="np. /oferta/ lub /job/">
        <p class="text-xs text-gray-500 mt-1">Tylko linki zawierające ten fragment będą traktowane jako oferty.</p>
      </div>
      <div class="flex items-center justify-between pt-1">
        <button
          v-if="website"
          type="button"
          @click="deleteWebsite"
          :disabled="deleting"
          class="btn-danger"
        >{{ deleting ? 'Usuwanie…' : 'Usuń' }}</button>
        <div v-else />
        <button type="submit" :disabled="saving" class="btn-primary">
          {{ saving ? 'Zapisywanie…' : (website ? 'Zapisz zmiany' : 'Dodaj stronę') }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import BaseModal from '../shared/BaseModal.vue'
import { api } from '../../api.js'

const props = defineProps({
  modelValue: Boolean,
  website: { type: Object, default: null },
})
const emit = defineEmits(['update:modelValue', 'saved', 'deleted'])

const isOpen = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v),
})

const saving = ref(false)
const deleting = ref(false)

const form = reactive({
  name: props.website?.name ?? '',
  url: props.website?.url ?? '',
  offer_url_part: props.website?.offer_url_part ?? '',
})

async function save() {
  saving.value = true
  try {
    const result = props.website
      ? await api.patch(`/websites/${props.website.id}`, form)
      : await api.post('/websites', form)
    emit('saved', result)
    emit('update:modelValue', false)
  } catch (e) {
    alert('Błąd podczas zapisywania strony.')
  } finally {
    saving.value = false
  }
}

async function deleteWebsite() {
  if (!confirm('Usunąć tę stronę?')) return
  deleting.value = true
  try {
    await api.delete(`/websites/${props.website.id}`)
    emit('deleted', props.website.id)
    emit('update:modelValue', false)
  } catch (e) {
    alert('Błąd podczas usuwania strony.')
  } finally {
    deleting.value = false
  }
}
</script>
