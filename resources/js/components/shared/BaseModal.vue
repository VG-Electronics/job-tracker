<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="$emit('update:modelValue', false)" />
      <div
        class="relative z-10 bg-white rounded-xl shadow-2xl flex flex-col w-full"
        :class="wide ? 'max-w-3xl' : 'max-w-2xl'"
        style="max-height: 90vh"
      >
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 shrink-0">
          <h2 class="font-semibold text-gray-900">{{ title }}</h2>
          <button
            @click="$emit('update:modelValue', false)"
            class="w-7 h-7 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 text-lg leading-none"
          >×</button>
        </div>
        <div class="overflow-y-auto flex-1 p-5">
          <slot />
        </div>
        <div v-if="$slots.footer" class="px-5 py-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
  title: { type: String, default: '' },
  wide: Boolean,
})
defineEmits(['update:modelValue'])
</script>
