<script setup lang="ts">
import { defineProps, computed, ref, onMounted, onUnmounted, watchEffect, defineAsyncComponent } from "vue"
import { formatBRL } from "~/scripts/utils/number"
import { formatWeekRangeLabel } from "~/scripts/utils/date"

const ApexCharts = defineAsyncComponent(() => import("vue3-apexcharts"))

const props = defineProps<{ table: any[] }>()

const isDark = ref(false)

onMounted(() => {
  const saved = localStorage.getItem("darkMode")
  isDark.value = saved === "true"

  const listener = (e: any) => (isDark.value = e.detail)
  window.addEventListener("dark-mode-changed", listener)
  onUnmounted(() => window.removeEventListener("dark-mode-changed", listener))
})

const niceMax = (v: number) => {
  if (!isFinite(v) || v <= 0) return 1
  const p = 10 ** Math.floor(Math.log10(v))
  const n = v / p
  const step = n <= 1 ? 1 : n <= 2 ? 2 : n <= 5 ? 5 : 10
  return step * p
}

const weeklyAgg = computed(() => {
  const m = new Map<string, { leads: number; inv: number }>()
  for (const { weeks = [] } of props.table) {
    for (const w of weeks) {
      if (!w?.week) continue
      const k = String(w.week)
      const leads = Number(w.leads || 0)
      const inv = w.cpl != null && leads > 0 ? Number(w.cpl) * leads : 0
      const a = m.get(k) ?? { leads: 0, inv: 0 }
      a.leads += leads
      a.inv += inv
      m.set(k, a)
    }
  }
  const entries = [...m.entries()].sort((a, b) => a[0].localeCompare(b[0]))

  const toTwoLine = (k: string) => {
    const full = formatWeekRangeLabel(k)
    const [sem, range] = full.split("•").map(s => s.trim())
    const line1 = (sem || "").replace("Semana", "Sem")
    const line2 = (range || "").replace(/\s/g, "")
    return `${line1}\n${line2}`
  }

  return {
    categories: entries.map(([k]) => toTwoLine(k)),
    leads: entries.map(([, v]) => v.leads),
    cpl: entries.map(([, v]) => (v.leads > 0 ? v.inv / v.leads : null))
  }
})

const leadsMax = computed(() => niceMax(Math.max(0, ...(weeklyAgg.value.leads ?? [0]))))
const cplMax = computed(() => niceMax(Math.max(0, ...(weeklyAgg.value.cpl?.filter(x => x != null) ?? [0]))))

// Chart dinâmico com suporte a dark mode
const weeklyChartOptions = computed(() => {
  const dark = isDark.value

  return {
    chart: {
      type: "line",
      background: "transparent",
      toolbar: { show: false },
      foreColor: dark ? "#F3F3F3" : "#333333",
      animations: { enabled: true, speed: 600 },
    },

    grid: {
      borderColor: dark ? "rgba(149,154,163,0.2)" : "rgba(0,0,0,0.1)",
      strokeDashArray: 4,
      padding: { left: 8, right: 16, top: 8, bottom: 12 },
    },

    xaxis: {
      categories: weeklyAgg.value.categories,
      tickPlacement: "between",
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: {
        style: { colors: dark ? "#F3F3F3" : "#333", fontSize: "12px", fontWeight: 500 },
      },
    },

    yaxis: [
      {
        title: { text: "Leads", style: { color: dark ? "#959AA3" : "#666", fontWeight: 500 } },
        min: 0,
        max: leadsMax.value,
        tickAmount: 5,
        labels: {
          style: { colors: dark ? "#959AA3" : "#666", fontSize: "11px" },
          formatter: (v: number) => Math.round(v).toLocaleString("pt-BR"),
        },
      },
      {
        opposite: true,
        title: { text: "CPL (R$)", style: { color: dark ? "#959AA3" : "#666", fontWeight: 500 } },
        min: 0,
        max: cplMax.value,
        tickAmount: 5,
        labels: {
          style: { colors: dark ? "#959AA3" : "#666", fontSize: "11px" },
          formatter: (v: number) => (v == null ? "" : formatBRL(v)),
        },
      },
    ],

    series: [
      { name: "Leads", type: "column", data: weeklyAgg.value.leads },
      { name: "CPL", type: "line", data: weeklyAgg.value.cpl },
    ],

    fill: {
      type: ["gradient", "solid"],
      gradient: {
        shade: dark ? "dark" : "light",
        type: "horizontal",
        gradientToColors: ["#a4ece5ff"],
        opacityFrom: 0.95,
        opacityTo: 0.85,
        stops: [0, 100],
      },
    },

    colors: dark ? ["#26CFBF", "#F3F3F3"] : ["#26CFBF", "#959AA3"],

    plotOptions: {
      bar: {
        borderRadius: 10,
        columnWidth: "40%",
      },
    },

    stroke: {
      curve: "smooth",
      width: [0, 3],
      lineCap: "round",
    },

    markers: {
      size: 5,
      strokeWidth: 2,
      strokeColors: dark ? "#a4ece5ff" : "#26CFBF",
      colors: [dark ? "#26CFBF" : "#00796B"],
      hover: { size: 7 },
    },

    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontSize: "12px",
      labels: { colors: dark ? "#F3F3F3" : "#333" },
      markers: { radius: 12 },
      itemMargin: { horizontal: 10, vertical: 0 },
    },

    tooltip: {
      theme: dark ? "dark" : "light",
      fillSeriesColor: false,
      shared: true,
      intersect: false,
      style: { fontSize: "12px" },
      x: { show: true },
      y: [
        { formatter: (v: number) => (v == null ? "—" : Math.round(v).toLocaleString("pt-BR")) },
        { formatter: (v: number) => (v == null ? "—" : formatBRL(v)) },
      ],
    },

    theme: { mode: dark ? "dark" : "light" },
  }
})
</script>

<template>
  <section
    class="bg-surface-secondary dark:bg-surface-secondary-dark p-5 rounded-lg border border-border-color dark:border-border-color-dark transition-all"
  >
    <h3 class="text-lg font-bold text-slate-primary dark:text-white mb-4 flex items-center gap-2">
      Performance Semanal
    </h3>

    <ApexCharts
      type="line"
      height="300"
      :options="weeklyChartOptions"
      :series="weeklyChartOptions.series"
      class="rounded-lg"
    />

    <p
      v-if="!weeklyAgg.categories.length"
      class="mt-3 text-sm text-muted text-center py-4"
    >
      Sem dados para o período selecionado.
    </p>
  </section>
</template>
