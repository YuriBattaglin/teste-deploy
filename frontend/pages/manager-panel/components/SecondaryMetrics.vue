<script setup lang="ts">
import { computed } from "vue"
import { IconEye, IconCoin, IconTrendingUp, IconMouse2, IconActivity } from "@tabler/icons-vue"

type Secondary = {
  impressions_total?: number | null
  cpm?: number | null
  ctr?: number | null
  cpc?: number | null
  clicks_to_leads_ratio?: number | null
}

const props = withDefaults(defineProps<{
  secondary?: Secondary | null
  ctrGood?: number
  ctrWarn?: number
}>(), {
  ctrGood: 2.5,
  ctrWarn: 1.0,
})

const brl = (v: number | null | undefined) =>
  v == null ? "N/A" : v.toLocaleString("pt-BR", { style: "currency", currency: "BRL", maximumFractionDigits: 2 })
const num = (v: number | null | undefined) =>
  v == null ? "N/A" : v.toLocaleString("pt-BR")

const ctrStatus = computed(() => {
  const v = props.secondary?.ctr
  if (v == null || Number.isNaN(v)) return "na"
  if (v >= props.ctrGood) return "good"
  if (v <= props.ctrWarn) return "bad"
  return "warn"
})

const ctrChip = computed(() => ({
  good: "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-300/30",
  warn: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-300/30",
  bad:  "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-300/30",
  na:   "bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-300/20",
}[ctrStatus.value]))

const ctrValue = computed(() =>
  props.secondary?.ctr == null ? "N/A" : `${props.secondary.ctr.toLocaleString("pt-BR", { maximumFractionDigits: 2 })}%`
)
</script>

<template>
  <div class="bg-white dark:bg-surface-secondary-dark p-5 rounded-lg border border-gray-200 dark:border-border-color-dark">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="text-lg font-bold text-slate-primary dark:text-white">Métricas Secundárias</h3>
      <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium"
           :class="ctrChip" title="Taxa de Cliques (CTR)">
        <IconTrendingUp class="w-3.5 h-3.5" />
        <span>CTR</span>
        <span class="tabular-nums">{{ ctrValue }}</span>
      </div>
    </div>

    <div v-if="!secondary" class="text-sm text-gray-500 dark:text-gray-400">—</div>

    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700/60">
      <div class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center">
            <IconEye class="w-4 h-4 text-gray-600 dark:text-gray-300" />
          </div>
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Impressões</span>
        </div>
        <span class="text-base font-semibold text-slate-primary dark:text-white tabular-nums">{{ num(secondary.impressions_total) }}</span>
      </div>

      <div class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center">
            <IconCoin class="w-4 h-4 text-gray-600 dark:text-gray-300" />
          </div>
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">CPM</span>
        </div>
        <span class="text-base font-semibold text-slate-primary dark:text-white tabular-nums">{{ brl(secondary.cpm) }}</span>
      </div>

      <div class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-md flex items-center justify-center"
               :class="{
                 'bg-green-100 dark:bg-green-900/30': ctrStatus==='good',
                 'bg-amber-100 dark:bg-amber-900/20': ctrStatus==='warn',
                 'bg-red-100 dark:bg-red-900/30': ctrStatus==='bad',
                 'bg-gray-100 dark:bg-gray-800/60': ctrStatus==='na',
               }">
            <IconTrendingUp class="w-4 h-4"
              :class="{
                'text-green-600 dark:text-green-400': ctrStatus==='good',
                'text-amber-600 dark:text-amber-400': ctrStatus==='warn',
                'text-red-600 dark:text-red-400': ctrStatus==='bad',
                'text-gray-600 dark:text-gray-300': ctrStatus==='na',
              }" />
          </div>
          <span class="text-sm font-medium"
                :class="{
                  'text-green-700 dark:text-green-400': ctrStatus==='good',
                  'text-amber-700 dark:text-amber-400': ctrStatus==='warn',
                  'text-red-700 dark:text-red-400': ctrStatus==='bad',
                  'text-gray-600 dark:text-gray-400': ctrStatus==='na',
                }">CTR</span>
        </div>
        <span class="text-base font-semibold tabular-nums"
              :class="{
                'text-green-700 dark:text-green-400': ctrStatus==='good',
                'text-amber-700 dark:text-amber-400': ctrStatus==='warn',
                'text-red-700 dark:text-red-400': ctrStatus==='bad',
                'text-slate-primary dark:text-white': ctrStatus==='na',
              }">{{ ctrValue }}</span>
      </div>

      <div class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center">
            <IconMouse2 class="w-4 h-4 text-gray-600 dark:text-gray-300" />
          </div>
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">CPC</span>
        </div>
        <span class="text-base font-semibold text-slate-primary dark:text-white tabular-nums">{{ brl(secondary.cpc) }}</span>
      </div>

      <div class="flex items-center justify-between py-3">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center">
            <IconActivity class="w-4 h-4 text-gray-600 dark:text-gray-300" />
          </div>
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Clicks/Leads</span>
        </div>
        <span class="text-base font-semibold text-slate-primary dark:text-white tabular-nums">
          {{ secondary?.clicks_to_leads_ratio == null ? "N/A" : secondary.clicks_to_leads_ratio.toLocaleString("pt-BR", { maximumFractionDigits: 2 }) }}
        </span>
      </div>
    </div>
  </div>
</template>
