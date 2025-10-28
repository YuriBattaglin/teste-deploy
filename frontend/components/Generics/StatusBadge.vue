<script setup lang="ts">
import { defineProps } from "vue";

const props = defineProps<{
    active: boolean;
    size?: "sm" | "md";
}>();

const statusClasses = {
    Ativo: "bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300",
    Inativo: "bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300",
    circleAtivo: "bg-green-500",
    circleInativo: "bg-red-500",
};

const paddingClass = "px-2.5 py-0.5 text-xs";
</script>

<template>
    <span
        :class="['inline-flex items-center font-medium rounded-full', paddingClass, active ? statusClasses.Ativo : statusClasses.Inativo]">
        <span
            :class="['w-2 h-2 rounded-full mr-1', active ? statusClasses.circleAtivo : statusClasses.circleInativo]">
        </span>
        {{ active ? 'Ativo' : 'Inativo' }}
    </span>
</template>
