<template>
  <div>
    <label v-if="label" class="block text-sm font-medium mb-2 dark:text-gray-300">{{ label }}</label>
    <div class="relative w-full">
      <el-autocomplete
        ref="autocomplete"
        v-model="state"
        :fetch-suggestions="querySearchAsync"
        :placeholder="placeholder"
        @select="onSelect"
        @clear="onClear"
        class="w-full custom-autocomplete"
        popper-class="no-arrow-popper"
        clearable
        :disabled="disabled"
        style="display:block; width:100%"
      />
    </div>
    
  <transition name="fade">
    <p v-if="error" class="mt-1 text-xs text-red-400">
      {{ error }}
    </p>
  </transition>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import API from '~/scripts/net/API'

interface OptionItem {
  [key: string]: any
}

const emit = defineEmits(['select', 'update:modelValue'])

const props = defineProps<{
  modelValue?: string | number | null
  label?: string
  endpoint: string
  valueKey?: string
  labelKey?: string
  placeholder?: string
  method?: 'GET' | 'POST'
  postFilter?: Record<string, any>
  disabled?: boolean
error?: string
}>()

const valueKey = props.valueKey ?? 'id'
const labelKey = props.labelKey ?? 'label'
const placeholder = props.placeholder ?? 'Digite algo...'
const method = props.method ?? 'GET'

const state = ref('') 
const options = ref<OptionItem[]>([])
const selectedItem = ref<OptionItem | null>(null)
const autocomplete = ref<any>(null)

watch(() => props.modelValue, async (val) => {
  if (val === null || val === undefined || val === '' || val === 0) {
    selectedItem.value = null
    state.value = ''
    return
  }

  if (selectedItem.value && String(selectedItem.value[valueKey]) === String(val)) {
    state.value = String(selectedItem.value[labelKey] ?? '')
    return
  }

  try {
    const url = props.endpoint
    const params = method === 'GET' ? ({ [valueKey]: val, ...props.postFilter }) : undefined
    const data = method === 'POST' ? { [valueKey]: val, ...props.postFilter } : undefined

    const response = await API.request({
      url,
      method,
      params,
      data
    })
    const payload = response.data?.data.data ?? response.data?.data ?? []
    let item: any = null
    if (Array.isArray(payload)) {
      item = payload.find((x: any) => String(x[valueKey]) === String(val)) ?? payload[0] ?? null
    } else if (payload && typeof payload === 'object') {
      item = payload
    }
    if (item) {
      selectedItem.value = item
      state.value = String(item[labelKey] ?? '')
    } else {
      selectedItem.value = null
      state.value = ''
    }
  } catch (err) {
    selectedItem.value = null
    state.value = ''
    console.error('Erro buscando label para modelValue', err)
  }
},{ immediate: true })

const fetchOptions = async (query: string) => {
  try {
    const url = props.endpoint
    const params = method === 'GET' 
      ? ({ search: query, limit: 5, ...props.postFilter })
      : undefined
    const data = method === 'POST' 
      ? ({ search: query, limit: 5, ...props.postFilter })
      : undefined

      const response = await API.request({ url, method, params, data })
      const payload = response.data?.data.data ?? response.data?.data ?? []
    options.value = Array.isArray(payload) ? payload.slice(0, 5) : [] 
    return options.value
  } catch (err) {
    console.error('Erro fetchOptions', err)
    options.value = []
    return []
  }
}

let debounceTimeout: number | undefined

const querySearchAsync = (queryString: string, cb: (items: any[]) => void) => {
  clearTimeout(debounceTimeout)
  debounceTimeout = window.setTimeout(async () => {
    const results = await fetchOptions(queryString)
    const mapped = results.map((item: any) => ({
      ...item,
      value: String(item[labelKey] ?? '')
    }))
    cb(mapped)
  }, 400)
}

const onSelect = (item: any) => {
  selectedItem.value = item
  state.value = String(item[labelKey] ?? item.value ?? '')
  const emittedValue = item[valueKey] ?? item.id ?? item.value ?? null

  emit('select', item)
  emit('update:modelValue', emittedValue)
}

const onClear = () => {
  selectedItem.value = null
  state.value = ''
  emit('update:modelValue', null)
}

</script>

<style scoped>
.w-full {
  width: 100%;
}
</style>
