<script setup lang="ts">
import { IconPencil, IconSearch, IconTrash, IconUsersGroup } from '@tabler/icons-vue'
import Button from '~/components/Generics/Button.vue'
import { ref } from 'vue'
import Dialog from '~/scripts/Dialog'
import { initialOf } from '~/scripts/utils/string'
import { capitalize } from '~/scripts/utils/string'
import Input from '~/components/Generics/Input.vue'
import API from '~/scripts/net/API'
import { debounce } from 'lodash'
import Loader from '../Loader.vue'
import Select from './Select.vue'
import StatusBadge from './StatusBadge.vue'

interface Column {
    key: string
    label: string
    formatter?: (value: any, row: any) => string | number | HTMLElement
}

const props = defineProps<{
    tenant?: string
    filter?: string
    columns: Column[]
    endPoint: string
    objectName: string
    showTitle: boolean
    viewMode: string
}>()

const isLoading = ref(false);
const search = ref('')
const activeFilter = ref(1);
const object = ref<any[]>([])
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 6,
    total: 0,
})


const pluralObjectName = computed(() => {
  if (props.objectName.endsWith("ão")) {
    return props.objectName.slice(0, -2) + "ões";
  }
  return props.objectName + "s";
});

const fetchObject = async (page = 1) => {
    isLoading.value = true;
    let url = `/${props.endPoint}?page=${page}&per_page=${pagination.value.per_page}`;
    if (props.filter) {
        url += `&${props.filter}`;
    }
    if (search.value) {
        url += `&search=${encodeURIComponent(search.value)}`
    }
    if (activeFilter.value) {
        url += `&active=${activeFilter.value}`
    }
    const response = await API.get(url);
    object.value = response.data.data.data;
    pagination.value = {
        current_page: response.data.data.current_page,
        last_page: response.data.data.last_page,
        per_page: response.data.data.per_page,
        total: response.data.data.total,
    };
    isLoading.value = false;
}

const debouncedFetch = debounce(() => fetchObject(1), 500)

watch(search, () => {
    debouncedFetch()
})

defineExpose({
    reload: fetchObject
})

const deleteObject = async (object: any, inactive = false) => {
  const actionText = inactive ? 'inativar' : 'remover'

  const confirmou = await Dialog.getInstance().question({
    html: `Tem certeza que deseja ${actionText} este ${props.objectName}?`
  })

  if (!confirmou.isConfirmed) return
  try {
    if (inactive) {
      await API.put(`/${props.endPoint}/${object.tenant_id ?? object.id}`, { active: false })
      Dialog.getInstance().toastSuccess(`${capitalize(props.objectName)} inativado com sucesso!`)
    } else {
      await API.delete(`/${props.endPoint}/${object.tenant_id ?? object.id}`)
      Dialog.getInstance().toastSuccess(`${capitalize(props.objectName)} deletado com sucesso!`)
    }

    fetchObject()
  } catch (error) {
    Dialog.getInstance().toastError(`Erro ao ${actionText} ${props.objectName}!`)
    console.error(error)
  }
}

onMounted(() => {
    fetchObject()
})
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4" v-if="showTitle">
            <h1 class="text-3xl dark:text-white">{{ capitalize(pluralObjectName) }}</h1>
        </div>
        <div id="filters-section"
            class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative">
                        <Input type="text"
                            :placeholder="`Buscar ${objectName === 'organização' ? 'organizaçõe' : objectName}s...`"
                            v-model="search" :icon="IconSearch" />
                    </div>
                    <div>
                        <Select v-model="activeFilter" @change="debouncedFetch" :options="[
                            { label: 'Ativos', value: '1' },
                            { label: 'Inativos', value: '0' },
                            { label: 'Todos', value: '' }
                        ]"></Select>
                    </div>

                </div>
                <Button variant="primary" @click="$emit('new')">
                    <span class="text-lg leading-none mr-1">+</span>
                    <span>Criar {{ objectName }}</span>
                </Button>
            </div>
        </div>

        <Loader v-if="isLoading && viewMode === 'cards'" :just-spinner="true"></Loader>
        <div v-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="row in object" :key="row.id"
                class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border p-6 cursor-pointer hover:scale-105 transition-transform flex flex-col justify-between h-full border-border-color dark:border-border-color-dark"
                @click="navigateTo(`register?id=${row.id}`)">

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-lg flex items-center justify-center mr-3 bg-color-scale bg-opacity-10">
                            <component :is="row.icon ?? IconUsersGroup" class="text-color-scale text-xl" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ row.name }}</h3>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <StatusBadge :active="row.active"></StatusBadge>
                        <button class="p-2 text-red-500 hover:text-red-700" @click.stop="deleteObject(row)">
                            <IconTrash class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <p class="text-gray-600 dark:text-muted text-sm mb-3">{{ row.description }}</p>

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <p class="text-gray-600 dark:text-muted">Líder: <span
                                class="text-gray-800 dark:text-white font-medium">{{ row.leader }}</span></p>
                        <p class="text-gray-600 dark:text-muted">Membros: <span
                                class="text-gray-800 dark:text-white font-medium">{{ Array.isArray(row.members) ?
                                    row.members.join(', ') : row.members }}</span></p>
                    </div>
                </div>

            </div>
        </div>


        <div id="users-list" v-else-if="viewMode === 'table'"
            class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark ">
            <div class="p-6 border-b border-border-color dark:border-border-color-dark ">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lista de {{ capitalize(pluralObjectName) }}</h3>
            </div>
            <Loader :just-spinner="true" v-if="isLoading" />
            <div v-else class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface dark:bg-surface-dark">
                        <tr>
                            <th v-for="column in columns" :key="column.key"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ column.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-surface-secondary dark:bg-surface-secondary-dark divide-y divide-border-color dark:divide-border-color-dark">
                        <tr v-if="!isLoading && (!object || object.length === 0)">
                            <td :colspan="columns.length" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                Nenhum registro cadastrado
                            </td>
                        </tr>
                        <tr v-for="row in object" :key="row.id">
                            <td v-for="col in columns" :key="col.key"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">

                                <template v-if="col.key === 'name' || col.key === 'description'">
                                    <div class="flex items-center">
                                        <div
                                            class="mr-4 flex h-10 w-10 items-center justify-center rounded-full text-white font-bold bg-color-scale">
                                            {{ initialOf(row.name ?? row.description) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ row.name ?? row.description }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ row.tenant_id ?? 'ID: #' + row.id }}
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template v-else-if="col.key === 'active'">
                                    <StatusBadge :active="row.active" />
                                </template>

                                <template v-else-if="col.formatter">
                                    <span v-html="col.formatter(row[col.key], row)"></span>
                                </template>

                                <template v-else-if="col.key !== 'actions' && col.key !== 'actions_inactive'">
                                    {{ row[col.key] }}
                                </template>

                                <template v-else-if="col.key === 'actions' || col.key === 'actions_inactive'">
                                    <button class="p-2 text-color-scale hover:text-color-scale-hover mr-2"
                                        @click="$emit('edit', row)">
                                        <IconPencil class="w-5 h-5" />
                                    </button>

                                    <button class="p-2 text-red-500 hover:text-red-700"
                                        @click="deleteObject(row, col.key === 'actions_inactive')">
                                        <IconTrash class="w-5 h-5" />
                                    </button>
                                </template>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div
            class="px-6 py-border-t border-border-color dark:border-border-color-dark flex items-center justify-between my-3">
            <div class="text-sm text-gray-500">
                Mostrando
                {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                a
                {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
                de {{ pagination.total }} {{ pluralObjectName }}
            </div>

            <div class="flex items-center space-x-2">
                <button
                    class="px-3 py-1 border border-border-color dark:border-border-color-dark rounded text-sm text-gray-500 hover:bg-color-scale-hover cursor-pointer"
                    :disabled="pagination.current_page === 1" @click="fetchObject(pagination.current_page - 1)">
                    Anterior
                </button>

                <button v-for="page in pagination.last_page" :key="page" @click="fetchObject(page)" :class="[
                    'px-3 py-1 rounded text-sm',
                    page === pagination.current_page ? 'bg-accent dark:text-white' : 'border border-border-color dark:border-border-color-dark text-gray-500 hover:bg-color-scale-hover'
                ]">
                    {{ page }}
                </button>

                <button
                    class="px-3 py-1 border border-border-color dark:border-border-color-dark rounded text-sm text-gray-500 hover:bg-color-scale-hover cursor-pointer"
                    :disabled="pagination.current_page === pagination.last_page"
                    @click="fetchObject(pagination.current_page + 1)">
                    Próximo
                </button>
            </div>
        </div>
    </div>
</template>
