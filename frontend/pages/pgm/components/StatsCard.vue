<script setup lang="ts">
import { defineProps } from "vue";

const props = defineProps<{
  title: string;
  value: string | number;
  icon?: any;
  type?: "general" | "partner";
  subtitle?: string;
  percent?: string;
}>();
</script>

<template>
  <div
    v-if="type === 'general' || !type"
    class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-4 flex items-center justify-between"
  >
    <div>
      <p class="text-xs font-medium text-gray-600 dark:text-muted">{{ title }}</p>
      <p class="text-lg font-bold text-gray-900 dark:text-white">{{ value }}</p>
    </div>

    <div v-if="icon" class="bg-color-scale bg-opacity-10 p-2 rounded-full">
      <component :is="icon" class="w-5 h-5 text-color-scale" />
    </div>
  </div>

  <div
    v-else-if="type === 'partner'"
    class="bg-surface-secondary dark:bg-surface-secondary-dark p-6 rounded-lg border border-border-color dark:border-border-color-dark"
  >
    <div class="flex items-center justify-between mb-4">
      <div
        class="p-3 rounded-full bg-color-scale bg-opacity-10"
      >
        <component
          v-if="icon"
          :is="icon"
          class="text-lg text-color-scale"
        />
      </div>

      <span
        v-if="percent"
        class="text-xs font-medium px-2 py-1 rounded-full"
        :class="[
          percent.startsWith('-')
            ? 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400'
            : 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400'
        ]"
      >
        {{ percent }}
      </span>
    </div>

    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ title }}</p>
    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ value }}</p>
    <p v-if="subtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
      {{ subtitle }}
    </p>
  </div>
</template>
