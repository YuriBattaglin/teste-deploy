<script setup lang="ts">
import { navigateTo } from "#app"
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css'

navigateTo("/login")
</script>

<template>
    <div name="teste">
        <Toaster position="top-center" richColors />
        <NuxtPage />
    </div>
</template>
