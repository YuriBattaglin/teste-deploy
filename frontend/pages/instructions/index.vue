<script setup lang="ts">
import {
  IconTarget,
  IconChartLine,
  IconUsers,
  IconBrain,
  IconCalendar,
  IconChartBar,
  IconPercentage,
  IconUsersGroup
} from "@tabler/icons-vue";
import { setPageTitle } from "~/scripts/utils/general";

onMounted(async () => {
  setPageTitle('Instruções PGM')
})
</script>

<template>
  <div class="space-y-8">
  <h1 class="text-3xl dark:text-white mb-4">Instruções PGM</h1>

    <section class="bg-surface dark:bg-surface-dark p-8 rounded-lg border border-border-color dark:border-border-color-dark">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-accent-primary bg-opacity-10 rounded-full mb-4">
          <IconTarget class="text-color-scale w-8 h-8" />
        </div>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">O que é o PGM?</h3>
        <p class="text-gray-600 dark:text-muted text-lg max-w-3xl mx-auto leading-relaxed">
          O Plans and Goals Management é um sistema completo de gestão estratégica que permite o planejamento,
          acompanhamento e análise de metas organizacionais. Com ele, você pode definir objetivos claros,
          monitorar o progresso em tempo real e tomar decisões baseadas em dados precisos.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 ">
        <div class="flex flex-col items-center text-center p-6 bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark ">
          <IconChartLine class="text-color-scale w-10 h-10 mb-4" />
          <h4 class="font-bold text-gray-800 dark:text-white mb-2">Acompanhamento Visual</h4>
          <p class="text-gray-600 dark:text-muted text-sm">
            Visualize o progresso das suas metas através de gráficos e relatórios intuitivos
          </p>
        </div>

        <div class="flex flex-col items-center text-center p-6 bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark">
          <IconUsers class="text-color-scale w-10 h-10 mb-4" />
          <h4 class="font-bold text-gray-800 dark:text-white mb-2">Gestão Colaborativa</h4>
          <p class="text-gray-600 dark:text-muted text-sm">
            Trabalhe em equipe com atribuição de responsabilidades
          </p>
        </div>

        <div class="flex flex-col items-center text-center p-6 bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark">
          <IconBrain class="text-color-scale w-10 h-10 mb-4" />
          <h4 class="font-bold text-gray-800 dark:text-white mb-2">Insights Inteligentes</h4>
          <p class="text-gray-600 dark:text-muted text-sm">
            Receba sugestões para otimização de resultados
          </p>
        </div>
      </div>
    </section>

    <!-- Shortcuts -->
    <section class="space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Acesso Rápido - Funcionalidades</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
        <div @click="navigateTo('/daily-register/register')" class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg flex flex-col items-center text-center cursor-pointer hover:scale-105 transition-transform border border-border-color dark:border-border-color-dark ">
          <IconCalendar class="w-8 h-8 text-color-scale mb-3" />
          <h5 class="text-gray-800 dark:text-white font-bold mb-1">Cadastro Diário</h5>
          <p class="text-gray-600 dark:text-muted text-sm">Registre atividades e tarefas diárias rapidamente</p>
        </div>

        <div @click="navigateTo('/commissions/search')" class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg flex flex-col items-center text-center cursor-pointer hover:scale-105 transition-transform border border-border-color dark:border-border-color-dark ">
          <IconPercentage class="w-8 h-8 text-color-scale mb-3" />
          <h5 class="text-gray-800 dark:text-white font-bold mb-1">Comissões</h5>
          <p class="text-gray-600 dark:text-muted text-sm">Acompanhe a comissão de cada colaborador</p>
        </div>

        <div @click="navigateTo('/goals/search')" class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg flex flex-col items-center text-center cursor-pointer hover:scale-105 transition-transform border border-border-color dark:border-border-color-dark ">
          <IconTarget class="w-8 h-8 text-color-scale mb-3" />
          <h5 class="text-gray-800 dark:text-white font-bold mb-1">Metas</h5>
          <p class="text-gray-600 dark:text-muted text-sm">Defina e monitore metas individuais e da equipe</p>
        </div>

        <div @click="navigateTo('/pgm')" class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg flex flex-col items-center text-center cursor-pointer hover:scale-105 transition-transform border border-border-color dark:border-border-color-dark ">
          <IconChartBar class="w-8 h-8 text-color-scale mb-3" />
          <h5 class="text-gray-800 dark:text-white font-bold mb-1">Painel PGM</h5>
          <p class="text-gray-600 dark:text-muted text-sm">Visualize dados e métricas do PGM em tempo real</p>
        </div>
          <div @click="navigateTo('/users/search')" class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg flex flex-col items-center text-center cursor-pointer hover:scale-105 transition-transform border border-border-color dark:border-border-color-dark">
          <IconUsersGroup class="w-8 h-8 text-color-scale mb-3" />
          <h5 class="text-gray-800 dark:text-white font-bold mb-1">Gestão de Usuários</h5>
          <p class="text-gray-600 dark:text-muted text-sm">Controle acesso e permissões de cada usuário do sistema</p>
        </div>
      </div>
    </section>

  </div>
</template>
