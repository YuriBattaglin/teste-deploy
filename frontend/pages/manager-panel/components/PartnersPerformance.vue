<script setup lang="ts">
import { defineProps, ref, computed, watch } from "vue"
import { IconSearch } from "@tabler/icons-vue"
import { initialOf } from "~/scripts/utils/string"
import { formatWeekRangeLabel } from "~/scripts/utils/date"
import { formatBRL } from "~/scripts/utils/number"
import Input from "~/components/Generics/Input.vue"
import Select from "~/components/Generics/Select.vue"

type WeekItem = { week: string; cpl?: number | null; leads?: number | null }
type PartnerRow = { partner_id: number; partner_name: string; month_cpl?: number | null; weeks?: WeekItem[] }

const props = defineProps<{
  table: PartnerRow[]
  pageSize?: number
  maxWeeks?: number
}>()

const page = ref(1)
const size = ref(props.pageSize ?? 10)
const q = ref("")
const options = ref([
  { label: '5', value: 5 },
  { label: '10', value: 10 },
  { label: '20', value: 20 },
  { label: '50', value: 50 },
])

watch([() => props.table, size, q], () => { page.value = 1 })

const allWeeks = computed(() => {
  const s = new Set<string>()
  for (const p of props.table ?? []) for (const w of p.weeks ?? []) if (w?.week) s.add(String(w.week))
  const arr = Array.from(s).sort((a, b) => a.localeCompare(b))
  return props.maxWeeks ? arr.slice(-props.maxWeeks) : arr
})

const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  if (!term) return props.table ?? []
  return (props.table ?? []).filter(p =>
    String(p.partner_id).includes(term) || p.partner_name?.toLowerCase().includes(term)
  )
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / size.value)))

const paged = computed(() => {
  const start = (page.value - 1) * size.value
  return filtered.value.slice(start, start + size.value)
})

function cellForWeek(weeks: WeekItem[] | undefined, weekKey: string) {
  if (!weekKey) return "—"
  const found = (weeks ?? []).find(w => String(w.week) === weekKey)
  return found?.cpl == null ? "—" : formatBRL(found.cpl)
}

function go(p: number) {
  if (p < 1 || p > totalPages.value) return
  page.value = p
}

const pageButtons = computed(() => {
  const last = totalPages.value, cur = page.value, out: number[] = []
  const add = (n: number) => { if (!out.includes(n) && n >= 1 && n <= last) out.push(n) }
  add(1); add(2); add(cur - 1); add(cur); add(cur + 1); add(last - 1); add(last)
  return out.sort((a, b) => a - b)
})
</script>

<template>
  <section
    class="bg-white dark:bg-surface-secondary-dark rounded-lg border border-gray-200 dark:border-border-color-dark p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-4">
      <div>
        <h3 class="text-lg font-bold text-slate-primary dark:text-white">Tabela Consolidada</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Performance semanal por parceiro</p>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
        <div class="relative">
          <Input type="text" :placeholder="`Buscar por parceiro ou ID...`" v-model.trim="q" :icon="IconSearch" />
        </div>

        <div class="flex items-center gap-2">
          <Select v-model.number="size" :options="options"></Select>
          <span class="text-sm text-gray-500 dark:text-gray-400">Itens por página</span>
        </div>
      </div>
    </div>

    <div v-if="!filtered.length" class="text-sm text-gray-500 dark:text-gray-400">
      Sem dados para o período selecionado.
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th
              class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
              Parceiros
            </th>
            <th v-for="(wk, i) in allWeeks" :key="wk + i"
              class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
              {{ formatWeekRangeLabel(wk) }}
              <div class="text-xs text-gray-500 dark:text-gray-400">CPL</div>
            </th>
            <th
              class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
              Mês<br />
              <span class="text-xs text-gray-500 dark:text-gray-400">CPL</span>
            </th>
          </tr>
        </thead>

        <tbody class="bg-white dark:bg-surface-secondary-dark divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="p in paged" :key="p.partner_id">
            <td class="px-5 py-4 font-semibold text-slate-primary dark:text-white whitespace-nowrap">
              <div class="flex items-center">
                <div
                  class="mr-4 flex h-10 w-10 items-center justify-center rounded-full text-white font-bold bg-color-scale">
                  {{ initialOf(p.partner_name) }}
                </div>
                <div>
                  <p>{{ p.partner_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ p.partner_id }}</p>
                </div>
              </div>
            </td>

            <td v-for="(wk, i) in allWeeks" :key="p.partner_id + '-' + wk + '-' + i"
              class="px-5 py-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">
              {{ cellForWeek(p.weeks, wk) }}
            </td>

            <td class="px-5 py-4 font-semibold text-slate-primary dark:text-white whitespace-nowrap">
              {{ p.month_cpl == null ? "—" : formatBRL(p.month_cpl) }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="flex items-center justify-between mt-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">
          Página {{ page }} de {{ totalPages }} — {{ filtered.length }} resultado(s)
        </div>
        <div class="flex items-center gap-1">
          <button
            class="px-3 py-1 rounded border border-border-color dark:border-border-color-dark text-sm text-gray-700 dark:text-gray-200 disabled:opacity-50"
            :disabled="page <= 1" @click="go(page - 1)">
            Anterior
          </button>

          <template v-for="(pno, idx) in pageButtons" :key="idx">
            <button class="px-3 py-1 rounded text-sm"
              :class="pno === page
                ? 'bg-color-scale text-white'
                : 'border border-border-color dark:border-border-color-dark text-gray-700 dark:text-gray-200 bg-transparent'" @click="go(pno)">
              {{ pno }}
            </button>
          </template>

          <button
            class="px-3 py-1 rounded border border-border-color dark:border-border-color-dark text-sm text-gray-700 dark:text-gray-200 disabled:opacity-50"
            :disabled="page >= totalPages" @click="go(page + 1)">
            Próxima
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
