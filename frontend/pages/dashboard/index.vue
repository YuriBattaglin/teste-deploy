<script setup lang="ts">
import { ref, onMounted, Transition } from 'vue'
import { IconReload } from '@tabler/icons-vue'
import { setPageTitle } from '~/scripts/utils/general'
import { useAuth } from '@/composables/useAuth'
import AdminDashboard from './components/AdminDashboard.vue'
import OrganizationDashboard from './components/OrganizationDashboard.vue'
import PartnerDashboard from './components/PartnerDashboard.vue'

const { user } = useAuth()
const randomPhrase = ref<string | null>(null)
const showPhrase = ref(true)

const phrases = [
  'Como você está se sentindo hoje?',
  'Tudo bem com você?',
  'Tudo certo por aí?',
  'Pronto para começar mais um dia?',
  'Animado para conquistar seus objetivos?',
  'Como está sua energia hoje?',
  'Preparado para novos desafios?',
  'Que tal dar o seu melhor agora?',
  'Hoje é um bom dia para evoluir!',
  'Vamos fazer este dia valer a pena?',
  'Está pronto para mais uma conquista?',
  'Como está sua motivação hoje?',
  'Mais um passo rumo ao sucesso?',
  'Tudo tranquilo por aí?',
  'Está pronto para brilhar hoje?',
  'Confiante para o que vem pela frente?',
  'Já agradeceu pelas conquistas de hoje?',
  'O que acha de dar o seu máximo agora?',
  'Vamos juntos alcançar mais resultados?',
  'Está preparado para superar limites?'
]

const randomizePhrase = () => {
  showPhrase.value = false
  setTimeout(() => {
    const index = Math.floor(Math.random() * phrases.length)
    randomPhrase.value = phrases[index]
    showPhrase.value = true 
  }, 200)
}

onMounted(() => {
  setPageTitle('Dashboard')
  randomizePhrase()
})
</script>

<template>
  <div>
    <header class="mb-8 flex flex-col md:flex-row">
      <div class="flex items-end">
        <h1 class="text-4xl dark:text-white">Olá, {{ user?.name }}</h1>
        
        <Transition name="fade">
          <h3 v-if="showPhrase" class="text-xl ms-3 mt-3 md:mt-0 text-gray-700 dark:text-muted">
            {{ randomPhrase }}
          </h3>
        </Transition>

        <button @click="randomizePhrase"
          class="p-2 mt-2 md:mt-0 ms-2 bg-color-scale rounded-full text-white hover:bg-color-scale-hover hover:text-gray-200">
          <IconReload class="w-4 h-4" />
        </button>
      </div>
    </header>

    <AdminDashboard v-if="user?.tenant?.type === 'admin'" />
    <OrganizationDashboard v-if="user?.tenant?.type === 'organization' && !user.partner?.id"/>
    <PartnerDashboard v-if="user?.tenant?.type === 'organization' && user.partner?.id"/>
  </div>
</template>
