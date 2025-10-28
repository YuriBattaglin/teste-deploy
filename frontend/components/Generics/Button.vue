<script setup lang="ts">

const props = defineProps<{
    type?: 'button' | 'submit' | 'reset'
    variant?: 'primary' | 'secondary'
    disabled?: boolean
}>()
</script>

<template>
    <button :type="type || 'button'" :disabled="disabled" :class="[
        'px-4 py-2 rounded-lg focus:outline-none',
        variant === 'primary'
            ? 'bg-color-scale text-white hover:bg-color-scale-hover disabled:opacity-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-color-scale focus:ring-offset-2 transition'
            : 'border border-border-color dark:border-border-color-dark hover:bg-gray-200 dark:hover:bg-gray-700 focus:ring-2 focus:ring-gray-200  text-gray-700 dark:text-white'
    ]">
        <slot />
    </button>
</template>
