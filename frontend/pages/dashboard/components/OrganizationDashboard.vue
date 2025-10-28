<script setup lang="ts">
import { ref, defineAsyncComponent, onMounted, onUnmounted, watchEffect } from 'vue'
import Card from '~/components/Card.vue'
import { IconBuilding, IconUsers, IconHeartHandshake } from '@tabler/icons-vue'

const stats = ref([
  { title: 'Parceiros Ativos', value: 8, icon: IconHeartHandshake },
  { title: 'Usuários Ativos', value: 42, icon: IconUsers }
])

const isDark = ref(false)

const chart1Series = ref([{ name: 'Usuários', data: [10, 15, 20, 25, 30, 35, 40] }])
const chart1Options = ref({})

const chart2Series = ref([{ name: 'Organizações', data: [5, 7, 8, 10, 12, 15, 18] }])
const chart2Options = ref({})

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
  chart1Options.value = {
    background: "transparent",
    chart: { type: 'line', height: 350, background: "transparent" },
    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'] },
    stroke: { curve: 'smooth', width: 3 },
    colors: ['#26CFBF'],
    theme: { mode: isDark.value ? "dark" : "light" }
  }

  chart2Options.value = {
    chart: { type: 'bar', height: 350, background: "transparent" },
    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'] },
    colors: ['#26CFBF'],
    theme: { mode: isDark.value ? "dark" : "light" }
  }
})

const ApexCharts = defineAsyncComponent(() =>
  import('vue3-apexcharts')
)
</script>

<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <Card v-for="stat in stats" :key="stat.title" :title="stat.title" :value="stat.value" :icon="stat.icon"
        />
    </div>

    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ">
        <div
          class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6">
          <div class="flex items-end gap-2">
            <p class="text-xl text-gray-800 dark:text-white">Parceiros cadastrados</p>
            <p class="text-gray-600 dark:text-muted">por mês</p>
          </div>
          <ApexCharts type="line" height="350" :options="chart1Options" :series="chart1Series" />
        </div>

        <div
          class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6">
           <div class="flex items-end gap-2">
            <p class="text-xl text-gray-800 dark:text-white">Usuários ativos</p>
            <p class="text-gray-600 dark:text-muted">por mês</p>
          </div>
          <ApexCharts type="bar" height="350" :options="chart2Options" :series="chart2Series" />
        </div>
      </div>
    </ClientOnly>
  </div>
</template>
