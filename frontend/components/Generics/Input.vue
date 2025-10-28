<script setup lang="ts">
import { ref, watch } from 'vue'
import API from '~/scripts/net/API';

const props = defineProps<{
  modelValue?: string | number
  type?: string
  placeholder?: string
  disabled?: boolean
  label?: string
  icon?: any 
  useMask?: 'phone' | 'cpf-cnpj'
  error?: string
}>()

const emit = defineEmits(['update:modelValue', 'cnpj-data'])

const internalValue = ref(
  props.type === 'number'
    ? (props.modelValue ?? 0)
    : (props.modelValue ?? '')
)

const formatValue = (value: string) => {
  if (!value) return ''
  switch (props.useMask) {
    case 'phone':
      const digits = value.replace(/\D/g, '')
      if (digits.length <= 10) {
        return digits.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3').replace(/-$/, '')
      } else {
        return digits.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3').replace(/-$/, '')
      }
    case 'cpf-cnpj':
      const n = value.replace(/\D/g, '')
      if (n.length <= 11) {
        return n.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4').replace(/-$/, '')
      } else {
        return n.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5').replace(/-$/, '')
      }
    default:
      return value
  }
}

watch(() => props.modelValue, (val) => {
  const validacao = props.type === 'number' ? 0 : ''
  internalValue.value = val ?? validacao;
})

const fetchCNPJData = async (cnpj: string) => {
  try {
    const digits = cnpj.replace(/\D/g, '')
    if (digits.length === 14) {
      const response = await API.get(`/consultar/cnpj/${digits}`)
      emit('cnpj-data', response.data.data)
    }
  } catch (error) {
    console.error('Erro ao buscar CNPJ:', error)
  }
}

const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  const formatted = formatValue(target.value)
  internalValue.value = formatted
  emit('update:modelValue', formatted)

  if (props.useMask === 'cpf-cnpj') {
    fetchCNPJData(formatted)
  }
}
</script>
<template>
  <label v-if="label" class="block text-sm font-medium mb-2 dark:text-gray-300">{{ label }}</label>
  <div class="relative">
    <component v-if="icon" :is="icon" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />

    <input :type="type || 'text'" :placeholder="placeholder" :disabled="disabled" :value="internalValue" v-bind="$attrs"
      @input="onInput" :maxlength="useMask === 'cpf-cnpj' ? 18 : (useMask === 'phone' ? 15 : undefined)" :class="[
        'w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-color-scale focus:border-color-scale dark:bg-surface-dark dark:text-white',
        'transition-shadow duration-300 ease-in-out',
        icon ? 'pl-10 pr-3' : '',
      ]" />
  </div>

  <transition name="fade">
    <p v-if="error" class="mt-1 text-xs text-red-400">
      {{ error }}
    </p>
  </transition>
</template>