<script setup lang="ts">
import { ref, onMounted, onUnmounted, watchEffect, defineAsyncComponent } from "vue"
import {
    IconEdit,
    IconChartBar,
    IconShare3,
    IconUserPlus,
    IconShoppingCart,
    IconCurrencyDollar,
    IconHeartHandshake
} from "@tabler/icons-vue"
import StatsCard from "./StatsCard.vue"
import StatusBadge from "~/components/Generics/StatusBadge.vue"
import Button from "~/components/Generics/Button.vue"

const ApexCharts = defineAsyncComponent(() => import("vue3-apexcharts"))

const isDark = ref(false)

onMounted(() => {
    const saved = localStorage.getItem("darkMode")
    isDark.value = saved === "true"

    window.addEventListener("dark-mode-changed", (e: any) => {
        isDark.value = e.detail
    })
})

onUnmounted(() => {
    window.removeEventListener("dark-mode-changed", () => { })
})

const funnelOptions = ref({})
const funnelSeries = ref([{ data: [100, 25, 12, 6] }])

const performanceOptions = ref({})
const performanceSeries = ref([
    { name: "Investimento", data: [800, 950, 1100, 1200, 1350, 1400, 1500, 1450, 1600, 1550, 1645, 1700] },
    { name: "Faturamento", data: [3200, 4200, 5800, 6400, 7200, 8100, 8900, 8500, 9200, 9100, 9718, 10200] },
])

const cards = [
    {
        title: "Investimento em Ads",
        value: "R$ 1.645,04",
        subtitle: "Últimos 30 dias",
        percent: "+12.5%",
        icon: IconShare3
    },
    {
        title: "Total de Leads",
        value: "152",
        subtitle: "CPL médio: R$ 10,82",
        percent: "+8.3%",
        icon: IconUserPlus
    },
    {
        title: "Vendas Realizadas",
        value: "9",
        subtitle: "Taxa de conversão: 5.92%",
        percent: "+15.2%",
        icon: IconShoppingCart
    },
    {
        title: "Faturamento",
        value: "R$ 9.718,75",
        subtitle: "Ticket médio: R$ 1.079,86",
        percent: "+22.1%",
        icon: IconCurrencyDollar
    }
]

watchEffect(() => {
    funnelOptions.value = {
        chart: {
            type: "bar",
            toolbar: { show: false },
            background: "transparent",
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true,
                barHeight: "60%",
            },
        },
        colors: ["#26CFBF", "#959AA3", "#46DE80", "#a4ece5ff"],
        xaxis: { categories: ["Clientes Atendidos", "Agendamento", "Comparecimento", "Vendas"] },
        theme: { mode: isDark.value ? "dark" : "light" },
    }

    performanceOptions.value = {
        chart: {
            type: "bar",
            toolbar: { show: false },
            background: "transparent",
        },
        plotOptions: { bar: { borderRadius: 4 } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
        },
        colors: ["#959AA3", "#26CFBF"],
        theme: { mode: isDark.value ? "dark" : "light" },
    }
})
</script>

<template>
    <div id="parceiro-content">
        <div
            class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-16 h-16 bg-color-scale bg-opacity-10 rounded-lg flex items-center justify-center">
                        <IconHeartHandshake class="w-8 h-8 text-color-scale" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Brasil Ótica - Queiroz Ótica LTDA
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">Cliente desde Janeiro 2023</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <StatusBadge :active="true" />
                            <span class="text-sm text-gray-500 dark:text-gray-400">3 unidades</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">12 vendedores</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <Button variant="secondary" class="flex"><IconEdit class="w-4 h-4 mr-2 mt-1" /> Editar</Button>
                    <Button variant="primary" class="flex"><IconChartBar class="w-4 h-4 mr-2 mt-1"/>Relatório Detalhado</Button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatsCard v-for="(card, i) in cards" :key="i" type="partner" :title="card.title" :value="card.value"
                :subtitle="card.subtitle" :percent="card.percent" :icon="card.icon" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div
                class="bg-surface-secondary dark:bg-surface-secondary-dark p-6 rounded-lg border border-border-color dark:border-border-color-dark">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Funil de Vendas - Brasil Ótica</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Performance detalhada do parceiro</p>
                    </div>
                    <select
                        class="border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-transparent dark:text-white">
                        <option>Últimos 30 dias</option>
                        <option>Últimos 7 dias</option>
                        <option>Últimos 90 dias</option>
                    </select>
                </div>
                <ApexCharts type="bar" height="300" :options="funnelOptions" :series="funnelSeries" />
            </div>

            <div
                class="bg-surface-secondary dark:bg-surface-secondary-dark p-6 rounded-lg border border-border-color dark:border-border-color-dark">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Performance Mensal</h3>
                    <select
                        class="border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-transparent dark:text-white">
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                </div>
                <ApexCharts type="bar" height="300" :options="performanceOptions" :series="performanceSeries" />
            </div>
        </div>
    </div>
</template>
