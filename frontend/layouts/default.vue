<script setup lang="ts">
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Loader from '~/components/Loader.vue';
import Dialog from '~/scripts/Dialog';

const { initAuth, user } = useAuth()
const router = useRouter()
const sidebarOpen = ref(false)

onMounted(async () => {
  Dialog.getInstance().showLoader();

  await initAuth(router)

  Dialog.getInstance().hideLoader();
})
</script>

<template>
  <div class="flex h-screen bg-surface dark:bg-surface-dark">
      <Loader :just-spinner="false" />
      <Toaster position="top-center" closeButton />
    <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" v-if="user" />
    <div class="flex-1 flex flex-col">
      <Navbar v-if="user" @toggle-sidebar="sidebarOpen = !sidebarOpen" />
      <main class="flex-1 py-2 px-10 overflow-y-auto" v-if="user">
        <slot />
      </main>
    </div>
  </div>
</template>