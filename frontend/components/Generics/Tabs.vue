<script setup lang="ts">
import { ref, watch } from 'vue'

interface Tab {
  key: string
  label: string
  disabled?: boolean
}

const props = defineProps<{
  modelValue: string
  tabs: Tab[]
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const activeTab = ref(props.modelValue)

watch(
  () => props.modelValue,
  (val) => {
    activeTab.value = val
  }
)

const setTab = (tab: Tab) => {
  if (tab.disabled) return
  activeTab.value = tab.key
  emit('update:modelValue', tab.key)
}
</script>

<template>
  <div class="flex border-b dark:text-white border-border-color dark:border-border-color-dark mb-6">
    <button
      v-for="tab in tabs"
      :key="tab.key"
      :disabled="tab.disabled"
      :class="[
        'px-4 py-2 transition-colors',
        activeTab === tab.key
          ? 'border-b-2 border-color-scale font-semibold'
          : 'text-gray-500 dark:text-gray-400',
        tab.disabled ? 'opacity-50 cursor-not-allowed' : 'hover:text-black dark:hover:text-white'
      ]"
      @click="setTab(tab)"
    >
      {{ tab.label }}
    </button>
  </div>
</template>
