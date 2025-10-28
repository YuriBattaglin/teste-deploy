<script setup lang="ts">
import Search from '~/pages/users/search.vue';

const props = defineProps<{
  tenantId: string
}>()
</script>

<template>
     <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark px-6 pt-6" >
      <Search :tenant-id="tenantId" :show-title="false" />
      </div>
</template>
