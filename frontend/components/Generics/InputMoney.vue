<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  modelValue?: string | number
  placeholder?: string
  disabled?: boolean
  label?: string
  icon?: any
  error?: string
}>()

const emit = defineEmits(['update:modelValue'])

const internalValue = ref(formatMoney(props.modelValue ?? 0))

watch(() => props.modelValue, (val) => {
  internalValue.value = formatMoney(val ?? 0)
})

function formatMoney(value: string | number): string {
  const number = typeof value === 'string'
    ? parseFloat(value.replace(/\D/g, '')) / 100 || 0
    : (Number(value) || 0)
  return number.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  let onlyDigits = target.value.replace(/\D/g, '')

  if (onlyDigits === '') {
    onlyDigits = '0'
  }

  const number = parseFloat(onlyDigits) / 100
  const formatted = formatMoney(number)

  target.value = formatted
  internalValue.value = formatted

  emit('update:modelValue', number)
}

</script>

<template>
  <label v-if="label" class="block text-sm font-medium mb-2 dark:text-gray-300">{{ label }}</label>
  <div class="relative">
    <component
      v-if="icon"
      :is="icon"
      class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"
    />

    <input
      type="text"
      :placeholder="placeholder"
      :disabled="disabled"
      :value="internalValue"
      @input="onInput"
      @keypress="onKeyPress"
      inputmode="numeric"
      class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-color-scale focus:border-color-scale dark:bg-surface-dark dark:text-white transition-shadow duration-300 ease-in-out"
      :class="icon ? 'pl-10 pr-3' : ''"
    />
  </div>

  <transition name="fade">
    <p v-if="error" class="mt-1 text-xs text-red-400">
      {{ error }}
    </p>
  </transition>
</template>
