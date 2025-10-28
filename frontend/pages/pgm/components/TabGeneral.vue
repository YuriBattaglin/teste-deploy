<script setup lang="ts">
import { ref, onMounted, watchEffect, defineAsyncComponent } from "vue";
import { IconCurrencyDollar, IconHeartHandshake, IconPercentage, IconSearch, IconShoppingCart, IconSpeakerphone, IconTicket, IconUsersGroup } from '@tabler/icons-vue';
import Input from '~/components/Generics/Input.vue';
import StatsCard from "./StatsCard.vue";

const ApexCharts = defineAsyncComponent(() =>
    import('vue3-apexcharts')
)

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

const salesEvolutionOptions = ref({})
const salesEvolutionSeries = ref([
    { name: "Vendas", data: [30, 40, 45, 50, 49, 60, 70, 91, 125, 140, 132, 150] },
    { name: "Metas", data: [35, 45, 50, 55, 55, 65, 75, 95, 130, 145, 140, 155] }
])

const funnelOptions = ref({})
const funnelSeries = ref([
    { data: [100, 25, 12, 6] }
])
const generalStats = [
    { title: "Parceiros Ativos", value: "28", icon: IconHeartHandshake },
    { title: "Total Vendedores", value: "156", icon: IconUsersGroup },
    { title: "Total Vendas", value: "1.247", icon: IconShoppingCart },
    { title: "Faturamento Total", value: "R$ 2.8M", icon: IconCurrencyDollar },
    { title: "Taxa de Conversão", value: "4.2%", icon: IconPercentage },
    { title: "Ticket Médio", value: "R$ 2,247", icon: IconTicket },
    { title: "Investimento em Ads", value: "R$ 485K", icon: IconSpeakerphone },
]

watchEffect(() => {
    salesEvolutionOptions.value = {
        chart: {
            type: "line",
            background: "transparent",
            toolbar: {
                show: true,
                tools: { download: true }
            }
        },
        stroke: { curve: "smooth", width: 3 },
        xaxis: {
            categories: [
                "Jan", "Fev", "Mar", "Abr", "Mai", "Jun",
                "Jul", "Ago", "Set", "Out", "Nov", "Dez"
            ],
        },
        colors: ["#26CFBF", "#959AA3"],
        legend: {
            position: "bottom",
        },
        theme: { mode: isDark.value ? "dark" : "light" }
    }

    funnelOptions.value = {
        chart: {
            type: "bar",
            toolbar: { show: true },
            background: "transparent"
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true,
                barHeight: "60%",
            }
        },
        colors: ["#26CFBF", "#959AA3", "#46DE80", "#a4ece5ff"],
        xaxis: {
            categories: ["Clientes Atendidos", "Agendamento", "Comparecimento", "Vendas"],
        },
        theme: { mode: isDark.value ? "dark" : "light" }
    }
})
</script>


<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-6">
        <StatsCard v-for="(stat, i) in generalStats" :key="i" type="general" :title="stat.title" :value="stat.value"
            :icon="stat.icon" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div
            class="bg-surface-secondary dark:bg-surface-secondary-dark p-6 rounded-lg border border-border-color dark:border-border-color-dark">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold dark:text-white">Evolução de Vendas</h3>
            </div>
            <ApexCharts type="line" height="300" :options="salesEvolutionOptions" :series="salesEvolutionSeries" />
        </div>

        <div
            class="bg-surface-secondary dark:bg-surface-secondary-dark p-6 rounded-lg border border-border-color dark:border-border-color-dark">
            <h3 class="text-xl font-bold dark:text-white mb-6">Funil de Vendas</h3>
            <ApexCharts type="bar" height="300" :options="funnelOptions" :series="funnelSeries" />
        </div>
    </div>

    <div id="partners-table-section"
        class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark mb-4">
        <div class="p-6 border-b border-border-color dark:border-border-color-dark">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Performance por Parceiro</h3>
                <div class="flex items-center space-x-3">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative">
                            <Input type="text" :placeholder="`Buscar parceiro...`" :icon="IconSearch" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="partners-table" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface dark:bg-surface-dark ">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unidade</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Investimento Ads</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº de
                            Leads</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPL
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa
                            de Conv. Agend.</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº de
                            Agend.</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa
                            de Conv. Comp.</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Comparecimento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa
                            de Conv. Venda</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vendas</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ticket Médio</th>
                    </tr>
                </thead>
                <tbody
                    class="bg-surface-secondary dark:bg-surface-secondary-dark divide-y divide-border-color dark:divide-border-color-dark">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Brasil
                            Ótica - Queiroz
                            Ótica LTDA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white ">CLÍNICA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 1.003,60</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">37</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 27,12</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">40,54%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">26,67%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">50,00%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 1.075,00</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Brasil
                            Ótica - Queiroz
                            Ótica LTDA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">MARTINS SOARES
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 336,08</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">41</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 8,20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">112,20%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">46</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">32,61%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">26,67%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 1.063,75</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Brasil
                            Ótica - Queiroz
                            Ótica LTDA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">ALTO JEQUITIBÁ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 305,36</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">74</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 4,13</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">70,27%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">52</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">25,00%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">13</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">23,08%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 1.580,00</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">HC
                            Ótica</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Única</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 927,74</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">182</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 5,10</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">4,95%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">9</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">33,33%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">33,33%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 500,00</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">JM
                            Prime</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Única</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 837,35</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">306</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 2,74</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">9,48%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">29</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">41,38%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">12</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">25,00%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">R$ 1.773,46</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>