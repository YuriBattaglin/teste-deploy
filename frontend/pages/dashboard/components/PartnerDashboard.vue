<script setup lang="ts">
import { ref, defineAsyncComponent, onMounted, onUnmounted, watchEffect } from 'vue'
import Card from '~/components/Card.vue'
import { IconUsers, IconTarget, IconBriefcase, IconShoppingCart } from '@tabler/icons-vue'

const stats = ref([
  { title: 'Clientes Atendidos', value: 24, subtitle: 'Mês atual', icon: IconUsers },
  { title: 'Qualificação', value: 15, subtitle: 'Mês atual', icon: IconTarget },
  { title: 'Reunião/Visita', value: 9, subtitle: 'Mês atual', icon: IconBriefcase },
  { title: 'Número de Vendas', value: 6, subtitle: 'Mês atual', icon: IconShoppingCart },
])

const isDark = ref(false)

const chart2Series = ref([{ 
  name: 'Comissões (R$)', 
  data: [1200, 1500, 1800, 2000, 2500] 
}])

const chart1Series = ref([{ 
  name: 'Vendas', 
  data: [2, 3, 4, 5, 6] 
}])

const chart2Options = ref({})
const chart1Options = ref({})

onMounted(() => {
  const saved = localStorage.getItem("darkMode")
  isDark.value = saved === "true"

  window.addEventListener("dark-mode-changed", (e: any) => {
    isDark.value = e.detail
  })
})

onUnmounted(() => {
  window.removeEventListener("dark-mode-changed", () => {})
})

watchEffect(() => {
  chart2Options.value = {
    chart: { type: 'line', height: 350 , background: "transparent" },
    xaxis: { categories: ['Maio', 'Jun', 'Jul', 'Ago', 'Set'] },
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#26CFBF'],
    theme: { mode: isDark.value ? "dark" : "light" },
    tooltip: {
      y: {
        formatter: (val: number) => `R$ ${val.toLocaleString()}`
      }
    }
  }

  chart1Options.value = {
    chart: { type: 'bar', height: 350 , background: "transparent" },
    xaxis: { categories: ['Maio', 'Jun', 'Jul', 'Ago', 'Set'] },
    colors: ['#26CFBF'],
    theme: { mode: isDark.value ? "dark" : "light" },
    tooltip: {
      y: {
        formatter: (val: number) => `${val} vendas`
      }
    }
  }
})

const ApexCharts = defineAsyncComponent(() =>
  import('vue3-apexcharts')
)
</script>

<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <Card v-for="stat in stats" 
        :key="stat.title" 
        :title="stat.title" 
        :value="stat.value" 
        :subtitle="stat.subtitle"
        :icon="stat.icon" />
    </div>

    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6">
         <div class="flex items-end gap-2">
            <p class="text-xl text-gray-800 dark:text-white">Evolução dos Últimos 5 Meses</p>
            <p class="text-gray-600 dark:text-muted">Visualização por venda</p>
          </div>
          <ApexCharts type="bar" height="350" :options="chart1Options" :series="chart1Series" />
        </div>

        <div
          class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6">
          <div class="flex items-end gap-2">
            <p class="text-xl text-gray-800 dark:text-white">Comissões</p>
            <p class="text-gray-600 dark:text-muted">Metas & Ranking</p>
          </div>
          <ApexCharts type="line" height="350" :options="chart2Options" :series="chart2Series" />
        </div>
      </div>
    </ClientOnly>
  </div>
</template>
