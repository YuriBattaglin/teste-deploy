<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import { IconCurrencyDollar, IconUserPlus, IconChartLine, IconPercentage, IconFilter } from "@tabler/icons-vue"
import Button from "~/components/Generics/Button.vue"
import Select from "~/components/Generics/Select.vue"
import API from "~/scripts/net/API"
import Dialog from "~/scripts/Dialog"
import { getMonths, getYears } from "~/scripts/utils/date"
import { formatBRL } from "~/scripts/utils/number"
import SecondaryMetrics from "./components/SecondaryMetrics.vue"
import PartnersPerformance from "./components/PartnersPerformance.vue"
import WeeklyPerformanceChart from "./components/WeeklyPerformanceChart.vue"
import Card from "~/components/Card.vue"
import { setPageTitle } from "~/scripts/utils/general"

const month = ref<number>(new Date().getMonth() + 1)
const year = ref<number>(new Date().getFullYear())
const loading = ref(false)
const summary = ref<any>(null)
const secondary = ref<any>(null)
const table = ref<any[]>([])

const fmtNumber = (v: number | null | undefined) => (v == null ? "N/A" : v.toLocaleString("pt-BR"))

const summaryCards = computed(() => {
  const s = summary.value ?? {}
  return [
    { id: "investment", title: "Investimento Total", value: formatBRL(s.total_investment), icon: IconCurrencyDollar },
    { id: "leads",      title: "Total de Leads",     value: fmtNumber(s.total_leads),        icon: IconUserPlus },
    { id: "cpl",        title: "CPL Médio",          value: formatBRL(s.avg_cpl),            icon: IconChartLine },
    { id: "roas",       title: "ROAS",               value: s.roas == null ? "N/A" : s.roas.toLocaleString("pt-BR", { maximumFractionDigits: 2 }), icon: IconPercentage },
  ]
})

async function fetchData() {
  loading.value = true
  try {
    const { data } = await API.get("/manager-panel", { params: { month: month.value, year: year.value } })
    summary.value = data?.summary ?? null
    secondary.value = data?.secondary_metrics ?? null
    table.value = Array.isArray(data?.table) ? data.table : []
  } catch (err: any) {
    Dialog.getInstance().toastError(err?.response?.data?.message || err?.message || "Erro ao carregar dados")
  } finally {
    loading.value = false
  }
}

const applyFilters = () => fetchData()

onMounted(async () => {
  setPageTitle('Painel do Gestor')
  fetchData
})

const months = computed(() => getMonths())
const years = computed(() => getYears(2020, new Date().getFullYear() + 1))
</script>

<template>
  <div class="flex items-center justify-between mb-6">
    <div class="border-b border-border-color dark:border-border-color-dark flex-1">
      <h1 class="text-3xl dark:text-white mb-2">Painel do Gestor</h1>
    </div>

    <div class="flex items-center space-x-3 ml-6">
      <div class="flex items-center space-x-2">
        <IconFilter class="w-4 h-4 text-gray-600 dark:text-white" />
        <span class="text-sm text-gray-600 dark:text-white font-medium">Filtros:</span>
      </div>

      <Select
        v-model="month"
        :options="months.map(m => ({ label: m.label, value: m.value }))"
      />
      <Select
        v-model="year"
        :options="years.map(y => ({ label: y.toString(), value: y }))"
      />

      <Button variant="primary" @click="applyFilters" :disabled="loading">
        {{ loading ? "Carregando..." : "Aplicar" }}
      </Button>
    </div>
  </div>

  <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
     <Card v-for="card in summaryCards" 
        :key="card.id" 
        :title="card.title" 
        :value="card.value" 
        :icon="card.icon" />
  </section>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    <WeeklyPerformanceChart :table="table" />
    <SecondaryMetrics :secondary="secondary" />
  </div>

  <PartnersPerformance :table="table" />
</template>
