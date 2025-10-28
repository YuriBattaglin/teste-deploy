<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import {
  IconChartLine,
  IconBuilding,
  IconUsers,
  IconUsersGroup,
  IconChartColumn,
  IconTrendingUp2,
  IconX,
  IconUserShield,
  IconLock,
  IconCalendarBolt,
  IconCircleDashedPercentage,
  IconInfoCircle,
  IconUserDollar,
  IconTargetArrow
} from '@tabler/icons-vue'
import Logo from './Logo.vue'
import { useAuth } from '@/composables/useAuth'

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits(['close'])

const router = useRouter()
const activeMenu = ref('')
const { user } = useAuth()

const setActiveMenuByRoute = (path: string) => {
  if (path.includes('/users')) activeMenu.value = 'Usuários'
  else if (path.includes('/organizations')) activeMenu.value = 'Organizações'
  else if (path.includes('/dashboard')) activeMenu.value = 'Dashboard'
  else if (path.includes('/partners')) activeMenu.value = 'Parceiros'
  else if (path.includes('/squads')) activeMenu.value = 'Squads'
  else if (path.includes('/pgm')) activeMenu.value = 'Painel PGM'
  else if (path.includes('/manager-panel')) activeMenu.value = 'Painel do Gestor'
  else if (path.includes('/traffic')) activeMenu.value = 'Trafego'
  else if (path.includes('/daily-register')) activeMenu.value = 'Cadastro Diário'
  else if (path.includes('/commissions')) activeMenu.value = 'Comissões'
  else if (path.includes('/goals')) activeMenu.value = 'Metas'
  else if (path.includes('/seller-panel')) activeMenu.value = 'Painel Vendedor'
  else if (path.includes('/instructions')) activeMenu.value = 'Instruções PGM'
  else activeMenu.value = ''
}

const goTo = (menu: string) => {
  activeMenu.value = menu

  if (menu === 'Usuários') router.push('/users/search')
  if (menu === 'Organizações') router.push('/organizations/search')
  if (menu === 'Dashboard') router.push('/dashboard')
  if (menu === 'Parceiros') router.push('/partners/search')
  if (menu === 'Squads') router.push('/squads/search')
  if (menu === 'Painel PGM') router.push('/pgm')
  if (menu === 'Painel do Gestor') router.push('/manager-panel')
  if (menu === 'Trafego') router.push('/traffic')
  if (menu === 'Cadastro Diário') router.push('/daily-register')
  if (menu === 'Comissões') router.push('/commissions/search')
  if (menu === 'Metas') router.push('/goals/search')
  if (menu === 'Painel Vendedor') router.push('/seller-panel')
  if (menu === 'Instruções PGM') router.push('/instructions')

  emit('close')
}

setActiveMenuByRoute(router.currentRoute.value.path)

watch(
  () => router.currentRoute.value.path,
  (newPath) => setActiveMenuByRoute(newPath)
)
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" @click="emit('close')"></div>

  <aside class="fixed lg:static inset-y-0 left-0 z-50 transform transition-transform duration-300
           bg-surface-secondary dark:bg-surface-secondary-dark border-r border-border-color 
           dark:border-border-color-dark flex flex-col w-64"
    :class="isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="flex items-center justify-between px-6 py-4 lg:hidden">
      <div class="cursor-pointer" @click="router.push('/dashboard')">
        <Logo />
      </div>
      <button class="p-2 rounded-md hover:bg-surface dark:hover:bg-surface-dark" @click="emit('close')">
        <IconX class="w-5 h-5 text-gray-600 dark:text-gray-300" />
      </button>
    </div>

    <div class="hidden lg:block text-center mt-10 cursor-pointer" @click="router.push('/dashboard')">
      <Logo />
    </div>

    <nav class="flex-1 overflow-y-auto p-5 space-y-2">
      <div class="mt-4 mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 relative">
        <span class="relative z-10 bg-surface-secondary dark:bg-surface-secondary-dark pr-2">Início</span>
        <span class="absolute left-0 top-1/2 w-full h-px bg-gray-400 opacity-30"></span>
      </div>
      <button @click="goTo('Dashboard')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Dashboard'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconChartLine class="w-5 h-5 mr-3" /> Dashboard
      </button>

      <div
        class="mt-8 mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 relative">
        <span class="relative z-10 bg-surface-secondary dark:bg-surface-secondary-dark pr-2">Cadastros</span>
        <span class="absolute left-0 top-1/2 w-full h-px bg-gray-400 opacity-30"></span>
      </div>
      
      <button v-if="user?.partner?.id" @click="goTo('Cadastro Diário')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Cadastro Diário'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconCalendarBolt class="w-5 h-5 mr-3" /> Cadastro Diário
      </button>

        <button v-if="user?.partner?.id" @click="goTo('Comissões')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Comissões'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconCircleDashedPercentage class="w-5 h-5 mr-3" /> Comissões
      </button>

        <button v-if="user?.partner?.id" @click="goTo('Metas')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Metas'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconTargetArrow class="w-5 h-5 mr-3" /> Metas
      </button>

      <button v-if="user?.tenant?.type === 'organization' && !user?.partner?.id" @click="goTo('Trafego')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Trafego'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconTrendingUp2 class="w-5 h-5 mr-3" /> Tráfego
      </button>

      <button v-if="user?.tenant?.type === 'admin'" @click="goTo('Organizações')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Organizações'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconBuilding class="w-5 h-5 mr-3" /> Organizações
      </button>

      <button v-if="user?.tenant?.type === 'organization' && !user?.partner?.id" @click="goTo('Parceiros')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Parceiros'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconBuilding class="w-5 h-5 mr-3" /> Parceiros
      </button>

      <button v-if="user?.tenant?.type === 'organization' && !user?.partner?.id" @click="goTo('Squads')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Squads'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconUsersGroup class="w-5 h-5 mr-3" /> Squads
      </button>

      <div v-if="user?.tenant?.type === 'organization'"
        class="mt-8 mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 relative">
        <span class="relative z-10 bg-surface-secondary dark:bg-surface-secondary-dark pr-2">Análise</span>
        <span class="absolute left-0 top-1/2 w-full h-px bg-gray-400 opacity-30"></span>
      </div>

      <button v-if="user?.tenant?.type === 'organization'" @click="goTo('Painel PGM')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Painel PGM'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconChartColumn class="w-5 h-5 mr-3" /> Painel PGM
      </button>

         <button v-if="user?.partner?.id" @click="goTo('Painel Vendedor')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Painel Vendedor'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconUserDollar class="w-5 h-5 mr-3" /> Painel Vendedor
      </button>

              <button v-if="user?.partner?.id" @click="goTo('Instruções PGM')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Instruções PGM'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconInfoCircle class="w-5 h-5 mr-3" /> Instruções PGM
      </button>

      <button v-if="user?.tenant?.type === 'organization' && !user?.partner?.id" @click="goTo('Painel do Gestor')" :disabled="![1, 2].includes(user?.access_type)"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="[
          ![1, 2].includes(user?.access_type)
            ? 'text-gray-300 cursor-not-allowed '
            : activeMenu === 'Painel do Gestor'
              ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
              : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'
        ]">
          <IconUserShield class="w-5 h-5 mr-3" /> Painel do Gestor
        <IconLock v-if="![1, 2].includes(user?.access_type)" class="w-5 h-5 ms-3 text-gray-300" />
      </button>

      <div class="mt-8 mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 relative">
        <span class="relative z-10 bg-surface-secondary dark:bg-surface-secondary-dark pr-2">Segurança</span>
        <span class="absolute left-0 top-1/2 w-full h-px bg-gray-400 opacity-30"></span>
      </div>

      <button @click="goTo('Usuários')"
        class="flex items-center px-4 py-3 rounded-lg w-full text-left transition-colors duration-150" :class="activeMenu === 'Usuários'
          ? 'bg-color-scale dark:bg-active-dark text-white font-medium'
          : 'text-gray-600 dark:text-gray-300 hover:bg-color-scale-hover dark:hover:bg-active-dark-hover'">
        <IconUsers class="w-5 h-5 mr-3" /> Usuários
      </button>
    </nav>
  </aside>
</template>
