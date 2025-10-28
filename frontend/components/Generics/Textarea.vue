<script setup lang="ts">
import { computed } from "vue"

const props = defineProps<{
  modelValue: string
  label?: string
  placeholder?: string
  rows?: number
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void
}>()

const value = computed({
  get: () => props.modelValue,
  set: (val: string) => emit("update:modelValue", val),
})
</script>

<template>
  <div>
    <label
      v-if="label"
      class="block text-sm font-medium mb-2 dark:text-gray-300"
    >
      {{ label }}
    </label>
    <textarea :disabled="disabled"
      v-model="value"
      :rows="rows || 4"
      :placeholder="placeholder"
      class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none dark:bg-surface-dark dark:text-white"
    />
  </div>
</template>
