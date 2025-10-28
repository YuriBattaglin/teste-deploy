<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { IconMoon, IconSun, IconBuilding, IconLogout, IconMenu2 } from '@tabler/icons-vue'
import { useAuth } from '@/composables/useAuth'

const isDark = ref(false)
const showUserMenu = ref(false)
const menuRef = ref<HTMLElement | null>(null)
const emit = defineEmits(['toggle-sidebar'])

const { logout, user } = useAuth()
const router = useRouter();

onMounted(() => {
  const saved = localStorage.getItem('darkMode')
  isDark.value = saved === 'true'
  document.documentElement.classList.toggle('dark', isDark.value)
})

const toggleDarkMode = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('darkMode', String(isDark.value))
  window.dispatchEvent(new CustomEvent("dark-mode-changed", { detail: isDark.value }))
}

const toggleUserMenu = () => (showUserMenu.value = !showUserMenu.value)

const handleClickOutside = (event: MouseEvent) => {
  if (menuRef.value && !menuRef.value.contains(event.target as Node)) {
    showUserMenu.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <header class="px-6 py-4 flex items-center justify-between bg-surface dark:bg-surface-dark ">
    <div class="flex items-center space-x-3">
      <button class="lg:hidden p-2 text-gray-700 dark:text-white" @click="$emit('toggle-sidebar')">
        <IconMenu2 class="w-6 h-6" />
      </button>

      <button class="p-2 text-gray-700 hover:text-gray-500 dark:text-white hover:dark:text-gray-300"
        @click="toggleDarkMode">
        <component :is="isDark ? IconSun : IconMoon" class="w-5 h-5" />
      </button>
    </div>

    <div class="relative" ref="menuRef">
      <button @click="toggleUserMenu" class="flex items-center space-x-2 focus:outline-none">
        <div class="flex flex-col text-right">
          <span class="text-sm font-medium text-gray-700 dark:text-white">{{ user?.name }}</span>
          <span class="text-xs text-muted">{{ user?.partner?.name ?? user?.organization?.name ?? '' }}</span>
        </div>

        <img :src="(user?.partner?.logo || user?.organization?.logo)
          ? `data:image/png;base64,${user.partner?.logo || user.organization?.logo}`
          : '/default-avatar.png'" 
          alt="Avatar" 
          class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-surface-secondary-dark" />
      </button>

      <transition name="fade">
        <div
          v-if="showUserMenu"
          class="absolute right-0 mt-2 w-48 bg-surface-secondary dark:bg-surface-secondary-dark 
                 border border-border-color dark:border-border-color-dark rounded-md shadow-lg z-50"
        >
          <ul class="py-1">
            <li>
              <button @click="router.push('/profile')" 
                class="w-full text-left px-4 py-2 flex items-center space-x-2 text-sm 
                       text-gray-700 dark:text-gray-200 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover">
                <IconBuilding class="w-4 h-4" />
                <span>Empresa</span>
              </button>
            </li>
            <hr class="my-1 border-gray-200 dark:border-gray-700" />
            <li>
              <button @click="logout(router)" 
                class="w-full text-left px-4 py-2 flex items-center space-x-2 text-sm 
                       text-gray-700 dark:text-gray-200 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover">
                <IconLogout class="w-4 h-4" />
                <span>Sair</span>
              </button>
            </li>
          </ul>
        </div>
      </transition>
    </div>
  </header>
</template>
