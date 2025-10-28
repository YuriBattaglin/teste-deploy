<script setup lang="ts">
const props = defineProps<{
  justSpinner: boolean
}>()
</script>

<template>
  <div
    id="main-loader"
    class="fixed inset-0 flex items-center justify-center"
    :class="[
      !justSpinner 
        ? 'bg-surface dark:bg-surface-dark z-[9999]'
        : ''
    ]"
  >
    <svg class="spinner" viewBox="0 0 50 50">
      <circle class="path" cx="25" cy="25" r="18" fill="none" stroke-width="3" />
    </svg>
  </div>
</template>

<style scoped>
.spinner {
  width: 60px;
  height: 60px;
  animation: rotate 2s linear infinite;
}

.path {
  stroke: #26cfbf; /* cor do spinner */
  stroke-linecap: round;
  stroke-dasharray: 80,120;
  stroke-dashoffset: 0;
  transform-origin: center;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1,200;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 80,120;
    stroke-dashoffset: -30;
  }
  100% {
    stroke-dasharray: 80,120;
    stroke-dashoffset: -110;
  }
}
</style>
