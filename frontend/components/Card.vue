<script setup lang="ts">

const props = defineProps<{
  title: string;
  value: string | number;
  icon: any;
}>();
</script>

<template>
  <div
    class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 flex items-center justify-between"
  >
    <div>
      <p class="text-gray-600 dark:text-muted text-sm font-medium">{{ title }}</p>
      <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">{{ value }}</p>
    </div>
    <div :class="`w-12 h-12 bg-color-scale bg-opacity-10 rounded-lg flex items-center justify-center`">
      <component :is="icon" :class="`text-color-scale w-6 h-6`" />
    </div>
  </div>
</template>