<script setup lang="ts">
import { dayjs } from "element-plus"
import { ref, computed, onMounted } from "vue"
import Dialog from "~/scripts/Dialog"
import API from "~/scripts/net/API"

const loading = ref(false)
const items = ref({})
const limit = 6;
const { user } = useAuth()
async function fetchData() {
  loading.value = true
  try {
    const response = await API.get(`/daily-registers?page=1&per_page=${limit}`)
    items.value = response.data.data.data;
  } catch (err: any) {
    Dialog.getInstance().toastError(err?.response?.data?.message || err?.message || "Erro ao carregar histórico");
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
defineExpose({ refresh: fetchData })
const skeletonRows = computed(() => Array.from({ length: limit }))
</script>

<template>
  <div
    class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-xl border border-border-color dark:border-border-color-dark p-6">
    <h2 class="text-lg font-semibold mb-1 dark:text-white">Histórico Recente</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Últimos {{ limit }} dias cadastrados</p>
    <div v-if="loading" class="divide-y divide-gray-200 dark:divide-gray-700">
      <div v-for="(_, i) in skeletonRows" :key="i" class="py-4 flex justify-between items-center animate-pulse">
        <div class="flex-1">
          <div class="h-4 w-28 rounded bg-gray-200 dark:bg-gray-700 mb-2"></div>
          <div class="h-3 w-40 rounded bg-gray-200 dark:bg-gray-700 mb-1"></div>
          <div class="h-3 w-56 rounded bg-gray-200 dark:bg-gray-700"></div>
        </div>
        <div class="text-right">
          <div class="h-5 w-16 rounded bg-gray-200 dark:bg-gray-700 mb-2"></div>
          <div class="h-4 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
        </div>
      </div>
    </div>
    <div v-else-if="!items.length" class="text-sm text-gray-500 dark:text-gray-400">
      Nenhum registro cadastrado ainda.
    </div>
    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
      <div v-for="(item, idx) in items" :key="idx"
        class="w-full text-left py-4 flex justify-between items-center focus:outline-none">
        <div>
          <p class="font-medium dark:text-white">
            {{ dayjs(item.date).format("D [de] MMMM [de] YYYY") }}
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-500">
            Cadastrado por: {{ item.user.name }}
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Observações: {{ item.notes ?? 'Não informado' }}
          </p>
        </div>
        <div class="text-right mb-0">
          <p class="font-semibold dark:text-white">
            {{ item.step_one }} {{user.partner.metrics.step_one}}
          </p>
            <div class="flex justify-end">
            <p class="text-sm text-gray-600 dark:text-gray-500">{{user.partner.metrics.step_two}}
               {{ item.step_two }} -
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-500 ms-1"> {{user.partner.metrics.step_three}}
               {{ item.step_three }}
            </p>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold"> {{user.partner.metrics.step_four}}
            {{ item.step_four }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
