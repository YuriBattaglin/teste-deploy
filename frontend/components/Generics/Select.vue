<script setup lang="ts">
import { defineProps, defineEmits } from "vue";

const props = defineProps<{
  modelValue: string | number | null
  options: { label: string; value: string | number | null }[]
  placeholder?: string
  className?: string
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string | number | null): void
}>();
</script>

<template>
  <select
    :value="modelValue"
    @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    class="px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-color-scale focus:border-color-scale dark:bg-surface-dark dark:text-white transition-shadow duration-300 ease-in-out"
    :class="className"
  >
    <option v-if="placeholder" value="">{{ placeholder }}</option>
    <option
      v-for="(option, idx) in options"
      :key="idx"
      :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>
